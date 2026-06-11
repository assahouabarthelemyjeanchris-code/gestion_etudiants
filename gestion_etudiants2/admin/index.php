<?php 
session_start();

if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    $admin_id = $_SESSION['admin_id'];
    $stmt_user = $conn->prepare("SELECT * FROM administrateur WHERE administrateur_id = ?");
    $stmt_user->execute([$admin_id]);
    $user = $stmt_user->fetch();

    // =========================================================================
    // TRAITEMENT : Enregistrement de la nouvelle année universitaire (si soumis)
    // =========================================================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_annee_scolaire'])) {
        $debut_annee = intval($_POST['debut_annee']);
        $fin_annee = intval($_POST['fin_annee']);
        $libelle_annee = $debut_annee . "-" . $fin_annee;

        // On vérifie si une configuration existe déjà
        $check_config = $conn->query("SELECT COUNT(*) FROM configuration_gde WHERE cle = 'annee_universitaire_active'")->fetchColumn();
        if ($check_config > 0) {
            $stmt_up = $conn->prepare("UPDATE configuration_gde SET valeur = ? WHERE cle = 'annee_universitaire_active'");
            $stmt_up->execute([$libelle_annee]);
        } else {
            // Création de la table si elle n'existait pas (sécurité au premier clic)
            $conn->query("CREATE TABLE IF NOT EXISTS configuration_gde (cle VARCHAR(50) PRIMARY KEY, valeur VARCHAR(50))");
            $stmt_ins = $conn->prepare("INSERT INTO configuration_gde (cle, valeur) VALUES ('annee_universitaire_active', ?)");
            $stmt_ins->execute([$libelle_annee]);
        }
        header("Location: ?menu=" . $_GET['menu'] . "&success=Année universitaire mise à jour : " . $libelle_annee);
        exit;
    }

    // Récupération de l'année universitaire active pour affichage dans le formulaire
    try {
        $annee_active_brut = $conn->query("SELECT valeur FROM configuration_gde WHERE cle = 'annee_universitaire_active'")->fetchColumn();
        if ($annee_active_brut) {
            $parts = explode('-', $annee_active_brut);
            $defaut_debut = isset($parts[0]) ? intval($parts[0]) : date('Y');
            $defaut_fin = isset($parts[1]) ? intval($parts[1]) : date('Y') + 1;
        } else {
            $defaut_debut = date('Y');
            $defaut_fin = date('Y') + 1;
        }
    } catch (Exception $e) {
        $defaut_debut = date('Y');
        $defaut_fin = date('Y') + 1;
    }

    // =========================================================================
    // TRAITEMENT & ACTIONS DES NOTIFICATIONS ADMINISTRATEUR
    // =========================================================================
    if (isset($_GET['action_notif']) && isset($_GET['id_notif'])) {
        $id_notif = intval($_GET['id_notif']);
        $action = $_GET['action_notif'];
        $redirect_menu = isset($_GET['menu']) ? $_GET['menu'] : 'notifications';
        $filtre_curr = isset($_GET['filtre_notif']) ? $_GET['filtre_notif'] : 'toutes';
        $search_curr = isset($_GET['search_notif']) ? $_GET['search_notif'] : '';

        if ($action === 'marquer_lu') {
            $stmt = $conn->prepare("UPDATE notifications SET statut_lecture = 1 WHERE id_notification = ? AND destinataire_role = 'Admin'");
            $stmt->execute([$id_notif]);
            header("Location: ?menu=" . $redirect_menu . "&filtre_notif=" . $filtre_curr . "&search_notif=" . urlencode($search_curr) . "&success=Notification marquée comme lue");
            exit;
        } elseif ($action === 'supprimer') {
            $stmt = $conn->prepare("DELETE FROM notifications WHERE id_notification = ? AND destinataire_role = 'Admin'");
            $stmt->execute([$id_notif]);
            header("Location: ?menu=" . $redirect_menu . "&filtre_notif=" . $filtre_curr . "&search_notif=" . urlencode($search_curr) . "&success=Notification supprimée");
            exit;
        }
    }

    $filtre_notif = isset($_GET['filtre_notif']) ? $_GET['filtre_notif'] : 'toutes';
    $recherche_notif = isset($_GET['search_notif']) ? trim($_GET['search_notif']) : '';

    $sql_notifs = "SELECT * FROM notifications WHERE destinataire_role = 'Admin'";
    $params_notifs = [];

    if ($filtre_notif == 'non_lues') {
        $sql_notifs .= " AND statut_lecture = 0";
    } elseif ($filtre_notif == 'lues') {
        $sql_notifs .= " AND statut_lecture = 1";
    }

    if (!empty($recherche_notif)) {
        $sql_notifs .= " AND message LIKE ?";
        $params_notifs[] = "%" . $recherche_notif . "%";
    }

    $sql_notifs .= " ORDER BY created_at DESC";
    $stmt_notifs = $conn->prepare($sql_notifs);
    $stmt_notifs->execute($params_notifs);
    $notifications_list = $stmt_notifs->fetchAll();

    // Compteur global des notifications non lues pour la cloche d'alerte
    $notif_count = $conn->query("SELECT COUNT(*) FROM notifications WHERE destinataire_role = 'Admin' AND statut_lecture = 0")->fetchColumn();
    // =========================================================================

    $menu = isset($_GET['menu']) ? $_GET['menu'] : 'responsables';

    if ($menu == 'responsables') {
        $sql = "SELECT * FROM responsable_departement ORDER BY id_responsable DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $responsables = $stmt->fetchAll();

        $sql_stats = "SELECT d.nom_departement, COUNT(r.id_responsable) as total 
                      FROM departement d 
                      LEFT JOIN responsable_departement r ON d.id_responsable = r.id_responsable 
                      GROUP BY d.id_departement";
        $stats_resp = $conn->query($sql_stats)->fetchAll();

        $filieres_list = $conn->query("SELECT nom_filiere FROM filiere")->fetchAll();
    } 

    if ($menu == 'filieres') {
        $sql = "SELECT f.*, d.nom_departement FROM filiere f 
                LEFT JOIN d.id_departement ON f.id_departement = d.id_departement 
                ORDER BY f.nom_filiere ASC";
        $filieres = $conn->query($sql)->fetchAll();
    } 

    if ($menu == 'etudiants') {
        $sql = "SELECT e.*, f.nom_filiere FROM etudiants e 
                LEFT JOIN filiere f ON e.id_filiere = f.id_filiere 
                ORDER BY e.date_inscription DESC";
        $etudiants = $conn->query($sql)->fetchAll();
    }

    if ($menu == 'departements') {
        $sql = "SELECT d.*, r.nom as resp_nom, r.prenom as resp_prenom 
                FROM departement d 
                LEFT JOIN responsable_departement r ON d.id_responsable = r.id_responsable 
                ORDER BY d.nom_departement ASC";
        $departements = $conn->query($sql)->fetchAll();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | GDE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root { 
            --fond: #fafefc;              
            --fond-secondaire: #fbf4f0;   
            --blanc: #fffffe;             
            --texte: #090b3c;             
            --texte-secondaire: #573e4d;  
            --primaire: #a21c3b;          
            --primaire-hover: #391f20;    
            --bordure: #d9d6df;           
            --secondaire: #7d6f77;        
            --info: #b8aeb0;              
            --ombre: 0 1px 2px rgba(9, 11, 60, 0.05);
            --ombre-md: 0 4px 12px rgba(9, 11, 60, 0.08);
        }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--fond); 
            color: var(--texte); 
            overflow-x: hidden;
        }

        .sidebar { 
            width: 280px; 
            height: 100vh; 
            position: fixed; 
            background: var(--blanc); 
            border-right: 1px solid var(--bordure); 
            padding: 2rem 1.5rem; 
            z-index: 1000;
        }

        .sidebar .bg-primary { background-color: var(--primaire) !important; }
        .sidebar .text-primary { color: var(--primaire) !important; }

        .nav-link { 
            color: var(--texte-secondaire); 
            padding: 0.8rem 1rem;
            border-radius: 12px; 
            margin-bottom: 0.5rem; 
            font-weight: 500; 
            display: flex; 
            align-items: center; 
            transition: all 0.2s;
        }

        .nav-link i { font-size: 1.1rem; width: 25px; margin-right: 10px; }
        .nav-link:hover, .nav-link.active { background: var(--fond-secondaire); color: var(--primaire); }

        .main-content { margin-left: 280px; min-height: 100vh; transition: margin-left 0.3s; }

        .top-bar {
            padding: 1.5rem 2.5rem; 
            background: rgba(250, 254, 252, 0.8);
            backdrop-filter: blur(10px); 
            position: sticky; 
            top: 0; 
            display: flex;
            justify-content: space-between; 
            align-items: center; 
            z-index: 999;
            border-bottom: 1px solid var(--bordure);
        }

        .top-bar .text-primary { color: var(--primaire) !important; }

        .content-body { padding: 2.5rem; }

        .white-card { 
            background: var(--blanc); 
            border-radius: 20px; 
            padding: 1.5rem; 
            border: 1px solid var(--bordure); 
            box-shadow: var(--ombre);
        }

        .stat-card { border-bottom: 4px solid var(--primaire); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: var(--ombre-md); }

        .table thead th { 
            background: var(--fond-secondaire); 
            border: none; 
            color: var(--texte-secondaire);
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 0.05em;
            padding: 12px 15px;
        }
        .table tbody td { padding: 15px; border-bottom: 1px solid var(--bordure); color: var(--texte); }

        .status-pill { 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            background: var(--fond-secondaire); 
            color: var(--primaire); 
        }

        .form-control, .form-select { background: var(--fond); border: 1px solid var(--bordure); border-radius: 12px; padding: 10px 15px; color: var(--texte); }
        .form-control:focus, .form-select:focus { background: var(--blanc); color: var(--texte); box-shadow: 0 0 0 4px rgba(162, 28, 59, 0.1); border-color: var(--primaire); }

        .btn-primary { 
            background: var(--primaire); 
            border: none; 
            border-radius: 12px; 
            padding: 10px 20px; 
            font-weight: 600; 
            box-shadow: 0 4px 6px rgba(162, 28, 59, 0.2);
            transition: all 0.2s;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active { background: var(--primaire-hover) !important; box-shadow: 0 4px 12px rgba(57, 31, 32, 0.3); }

        .btn-light { background: var(--fond); border-color: var(--bordure); color: var(--texte-secondaire); }
        .btn-light:hover { background: var(--fond-secondaire); border-color: var(--secondaire); }
        .text-primary { color: var(--primaire) !important; }
        .text-muted { color: var(--texte-secondaire) !important; }
        .bg-light { background-color: var(--fond-secondaire) !important; }
        .modal-content { background: var(--blanc); color: var(--texte); border: 1px solid var(--bordure); }
        .modal-header.bg-dark { background-color: var(--primaire-hover) !important; }
        .modal-header.bg-primary { background-color: var(--primaire) !important; }
        .modal-body.bg-light { background-color: var(--fond) !important; }
        .modal-footer .btn-secondary { background-color: var(--secondaire); border: none; }
        .modal-footer .btn-secondary:hover { background-color: var(--texte-secondaire); }
        .badge.bg-primary.bg-opacity-10 { background-color: var(--fond-secondaire) !important; color: var(--primaire) !important; border: 1px solid rgba(162, 28, 59, 0.2); }
        .white-card.bg-primary.bg-opacity-10 { background-color: var(--fond-secondaire) !important; border: 1px solid var(--bordure); }
        code.text-primary { color: var(--primaire) !important; background: var(--fond-secondaire); padding: 2px 6px; border-radius: 4px; }
        
        .notif-tab-btn { border-radius: 10px; font-weight: 600; font-size: 0.85rem; padding: 8px 16px; transition: 0.2s; color: var(--texte-secondaire); border: 1px solid var(--bordure); background: var(--blanc); text-decoration: none; }
        .notif-tab-btn.active { background: var(--primaire); color: white; border-color: var(--primaire); }
        
        .info-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--texte-secondaire); font-weight: 600; margin-bottom: 4px; }
        .info-value { font-size: 0.95rem; color: var(--texte); font-weight: 500; background: var(--fond); padding: 10px 14px; border-radius: 10px; border: 1px solid var(--bordure); min-height: 43px; }
        .section-title { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primaire); font-weight: 700; margin-top: 1rem; border-left: 3px solid var(--primaire); padding-left: 8px; }

        /* =========================================================================
           CORE CODE DE CORRECTION GLOBAL RESPONSIVE : S'ADAPTE À N'IMPORTE QUEL MOBILE
           ========================================================================= */
        .mobile-toggle-btn { display: none; background: transparent; border: none; color: var(--texte); font-size: 1.4rem; cursor: pointer; }
        
        @media (max-width: 991px) {
            /* 1. Reset Global Structure */
            .sidebar { 
                transform: translateX(-100%); 
                transition: transform 0.3s ease; 
                position: fixed; 
                box-shadow: var(--ombre-md);
            }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0 !important; width: 100% !important; }
            .top-bar { padding: 1rem 1.5rem !important; }
            .content-body { padding: 1rem !important; }
            .mobile-toggle-btn { display: block; margin-right: 15px; }

            /* 2. Formulaire Année Universitaire */
            .white-card form { flex-wrap: wrap; justify-content: flex-start !important; gap: 10px !important; }
            .white-card form div { width: 45% !important; }
            .white-card form button { width: 100%; margin-top: 5px !important; }

            /* 3. Ajustement de la boîte mail & Notifications */
            .responsive-mail-sidebar { border-right: none !important; border-bottom: 1px solid var(--bordure) !important; background: #fff !important; padding: 10px !important; }
            .responsive-mail-sidebar .nav { flex-direction: row !important; flex-wrap: nowrap !important; overflow-x: auto !important; padding-bottom: 5px; -webkit-overflow-scrolling: touch; }
            .responsive-mail-sidebar .nav-link { margin-bottom: 0 !important; white-space: nowrap !important; padding: 6px 12px !important; font-size: 0.85rem !important; }
            .responsive-mail-sidebar .d-grid { display: none !important; }
            .item-mail-row { flex-wrap: wrap !important; padding: 12px 10px !important; }
            .mail-sender { width: 100% !important; margin-bottom: 4px !important; font-size: 0.85rem !important; }
            .mail-body { width: 100% !important; padding-right: 0 !important; font-size: 0.8rem !important; white-space: normal !important; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
            .mail-date { position: absolute; top: 12px; right: 10px; font-size: 0.75rem !important; }
            .mail-actions { display: none !important; }
            .w-100-mobile { width: 100% !important; margin-top: 5px; }

            /* 4. Tableaux & Eléments d'en-tête */
            .table-responsive { border: 0; }
            .d-flex.justify-content-between { flex-direction: column; gap: 10px; align-items: flex-start !important; }
            .input-group { max-width: 100% !important; }
        }
    </style>
</head>
<body>

    <div class="sidebar" id="sidebarMenu">
        <div class="d-flex align-items-center mb-5 px-2 justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-3 p-2 me-3">
                    <i class="fa-solid fa-shield-halved fa-lg"></i>
                </div>
                <h5 class="fw-bold mb-0">ESPACE <span class="text-primary text-opacity-50">ADMIN</span></h5>
            </div>
            <button class="mobile-toggle-btn d-lg-none text-danger" onclick="toggleMobileMenu()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <nav class="nav flex-column">
            <small class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.65rem; letter-spacing: 1px;">Menu Principal</small>
            <a href="?menu=responsables" class="nav-link <?= $menu == 'responsables' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-tie"></i> Responsables
            </a>
            <a href="?menu=departements" class="nav-link <?= $menu == 'departements' ? 'active' : '' ?>">
                <i class="fa-solid fa-building"></i> Départements
            </a>
            <a href="?menu=filieres" class="nav-link <?= $menu == 'filieres' ? 'active' : '' ?>">
                <i class="fa-solid fa-graduation-cap"></i> Filières
            </a>
            <a href="?menu=etudiants" class="nav-link <?= $menu == 'etudiants' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Étudiants
            </a>
            <a href="?menu=notifications" class="nav-link <?= $menu == 'notifications' ? 'active' : '' ?>">
                <i class="fa-solid fa-bell"></i> Notifications
                <?php if ($notif_count > 0): ?>
                    <span class="badge bg-danger ms-auto rounded-pill" style="font-size: 0.75rem;"><?= $notif_count ?></span>
                <?php endif; ?>
            </a>

            <small class="text-uppercase text-muted fw-bold mt-4 mb-3" style="font-size: 0.65rem; letter-spacing: 1px;">Paramètres</small>
            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#changePassModal">
                <i class="fa-solid fa-user-gear"></i> Mon Profil
            </a>
            <a href="../Deconnexion.php" class="nav-link text-danger mt-5">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        </nav>
    </div>

    <div class="main-content">
        <header class="top-bar">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle-btn" onclick="toggleMobileMenu()"><i class="fa-solid fa-bars"></i></button>
                <h4 class="fw-bold mb-0">
                    <?php 
                        if($menu == 'responsables') echo "Gestion des Responsables";
                        elseif($menu == 'departements') echo "Gestion des Départements";
                        elseif($menu == 'filieres') echo "Configuration des Filières";
                        elseif($menu == 'notifications') echo "Centre des Notifications";
                        else echo "Annuaire Étudiants";
                    ?>
                </h4>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="text-end me-3 d-none d-md-block">
                    <p class="small fw-bold mb-0"><?= $_SESSION['prenom'] ?> <?= $_SESSION['nom'] ?></p>
                    <p class="small text-muted mb-0">Administrateur</p>
                </div>
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; border: 1px solid var(--bordure);">
                    <i class="fa-solid fa-user-shield text-primary"></i>
                </div>
            </div>
        </header>

        <div class="content-body">
            
            <?php if ($menu == 'responsables' || $menu == 'departements'): ?>
            <div class="white-card mb-4">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <h6 class="fw-bold text-primary mb-1"><i class="fa-solid fa-calendar-days me-2"></i>Année Universitaire Active</h6>
                        <p class="text-muted small mb-0">Définissez la période académique active générale de l'établissement.</p>
                    </div>
                    <div class="col-lg-6">
                        <form action="" method="POST" class="d-flex gap-2 align-items-center justify-content-lg-end">
                            <input type="hidden" name="action_annee_scolaire" value="1">
                            <div>
                                <label class="small text-muted fw-bold ps-1" style="font-size:0.7rem;">Début</label>
                                <input type="number" name="debut_annee" class="form-control text-center py-1" value="<?= $defaut_debut ?>" min="2020" max="2050" required>
                            </div>
                            <div class="fw-bold mt-3 text-muted">/</div>
                            <div>
                                <label class="small text-muted fw-bold ps-1" style="font-size:0.7rem;">Fin</label>
                                <input type="number" name="fin_annee" class="form-control text-center py-1" value="<?= $defaut_fin ?>" min="2021" max="2051" required>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3 py-2 px-3 small"><i class="fa-solid fa-floppy-disk"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($menu == 'responsables'): ?>
                <div class="row g-3 mb-4">
                    <?php foreach($stats_resp as $stat): ?>
                    <div class="col-6 col-md-3">
                        <div class="white-card stat-card text-center p-3">
                            <p class="text-uppercase text-muted fw-bold mb-1 text-truncate" style="font-size: 0.65rem;"><?= $stat['nom_departement'] ?? 'Général' ?></p>
                            <h3 class="fw-bold mb-0"><?= $stat['total'] ?></h3>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="col-12 col-md-3">
                        <button class="white-card w-100 h-100 d-flex align-items-center justify-content-center text-primary fw-bold py-3" 
                                style="border: 2px dashed var(--primaire); background: transparent;"
                                data-bs-toggle="modal" data-bs-target="#addRespModal">
                            <i class="fa-solid fa-plus me-2"></i> Ajouter
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px; border-color: var(--bordure);">
                            <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        </span>
                        <input type="text" id="searchResp" class="form-control border-start-0" placeholder="Rechercher un responsable..." style="border-radius: 0 12px 12px 0;">
                    </div>
                </div>

                <div class="white-card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tableResponsables">
                            <thead>
                                <tr>
                                    <th>Responsable</th>
                                    <th>Coordonnées</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($responsables as $r): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= $r['prenom'] ?> <?= $r['nom'] ?></div>
                                        <div class="text-muted small">@<?= $r['nomutilisateur'] ?></div>
                                    </td>
                                    <td>
                                        <div class="small text-truncate" style="max-width: 150px;"><i class="fa-regular fa-envelope me-2"></i><?= $r['email'] ?></div>
                                        <div class="small text-muted"><i class="fa-solid fa-phone me-2"></i><?= $r['telephone'] ?></div>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="edit_resp.php?id=<?= $r['id_responsable'] ?>" class="btn btn-sm btn-light text-primary border"><i class="fa-pen-to-square"></i></a>
                                            <button onclick="confirmDelete('resp', <?= $r['id_responsable'] ?>)" class="btn btn-sm btn-light text-danger border"><i class="fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($menu == 'departements'): ?>
                <div class="d-flex justify-content-between mb-4">
                    <p class="text-muted mb-0">Gérez les structures de l'établissement</p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDeptModal"><i class="fa-plus me-2"></i>Nouveau</button>
                </div>

                <div class="white-card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nom du Département</th>
                                    <th>Responsable Assigné</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($departements as $d): ?>
                                <tr>
                                    <td><b class="text-dark"><?= htmlspecialchars($d['nom_departement']) ?></b></td>
                                    <td>
                                        <?php if ($d['id_responsable']): ?>
                                            <span class="status-pill text-nowrap"><i class="fa-solid fa-user-tie me-1"></i> <?= htmlspecialchars($d['resp_prenom'] . ' ' . $d['resp_nom']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Aucun assigné</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="edit_dept.php?id=<?= $d['id_departement'] ?>" class="btn btn-sm btn-light text-primary border"><i class="fa-pen-to-square"></i></a>
                                            <button onclick="confirmDelete('dept', <?= $d['id_departement'] ?>)" class="btn btn-sm btn-light text-danger border"><i class="fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($menu == 'filieres'): ?>
                <div class="d-flex justify-content-between mb-4">
                    <p class="text-muted mb-0">Gérez les niveaux d'études</p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFiliereModal"><i class="fa-plus me-2"></i>Nouvelle</button>
                </div>

                <div class="row g-3">
                    <?php foreach ($filieres as $f): ?>
                    <div class="col-12 col-md-4">
                        <div class="white-card h-100 d-flex flex-column justify-content-between shadow-sm">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?= $f['abreviation_filiere'] ?></span>
                                    <button onclick="confirmDelete('filiere', <?= $f['id_filiere'] ?>)" class="btn btn-link text-danger p-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                </div>
                                <h5 class="fw-bold mb-1" style="font-size:1rem;"><?= $f['nom_filiere'] ?></h5>
                                <small class="text-muted d-block"><i class="fa-solid fa-building me-1"></i> <?= htmlspecialchars($f['nom_departement'] ?? 'Non assigné') ?></small>
                            </div>
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center" style="border-color: var(--bordure) !important;">
                                <span class="small text-muted"><i class="fa-solid fa-layer-group me-2"></i>Niveau</span>
                                <span class="fw-bold">Bac +<?= $f['niveau'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($menu == 'etudiants'): ?>
                <div class="white-card">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <h5 class="fw-bold mb-0">Total : <?= count($etudiants) ?></h5>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px; border-color: var(--bordure);">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text" id="searchEtudiant" class="form-control border-start-0" placeholder="Rechercher..." style="border-radius: 0 12px 12px 0;">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tableEtudiants">
                            <thead>
                                <tr>
                                    <th>ID Permanent</th>
                                    <th>Étudiant</th>
                                    <th>Formation</th>
                                    <th>Contact</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($etudiants as $e): ?>
                                <tr>
                                    <td><code class="fw-bold text-primary"><?= $e['id_permanent'] ?></code></td>
                                    <td>
                                        <div class="fw-bold text-dark text-nowrap"><?= strtoupper($e['nom']) ?> <?= $e['prenom'] ?></div>
                                        <div class="small text-muted text-truncate" style="max-width:140px;"><?= $e['email'] ?></div>
                                    </td>
                                    <td>
                                        <span class="status-pill text-nowrap"><?= $e['nom_filiere'] ?></span>
                                        <div class="small mt-1 text-muted text-nowrap"><?= $e['niveau_etude'] ?> (<?= $e['cours'] ?>)</div>
                                    </td>
                                    <td><div class="small text-nowrap"><i class="fa-solid fa-phone me-1"></i> <?= $e['contact_etudiant'] ?></div></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <button class="btn btn-sm btn-primary border" data-bs-toggle="modal" data-bs-target="#viewEtudiant<?= $e['id_etudiants'] ?>">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <button onclick="confirmDelete('etudiant', <?= $e['id_etudiants'] ?>)" class="btn btn-sm btn-light text-danger border">
                                                <i class="fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php foreach ($etudiants as $e): ?>
                <div class="modal fade" id="viewEtudiant<?= $e['id_etudiants'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                            <div class="modal-header bg-primary text-white border-0 py-3">
                                <h5 class="fw-bold mb-0"><i class="fa-solid fa-database me-2"></i>Dossier Brute Étudiant</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-3 bg-light">
                                <div class="row g-2">
                                    <?php 
                                    foreach ($e as $champ => $valeur): 
                                        if (is_numeric($champ)) continue; 
                                    ?>
                                        <div class="col-12 col-md-6">
                                            <div class="info-label text-monospace text-secondary" style="font-size:0.65rem;">
                                                <i class="fa-solid fa-key fa-xs me-1 text-muted"></i><?= htmlspecialchars($champ) ?>
                                            </div>
                                            <div class="info-value font-monospace" style="background: var(--blanc); font-size: 0.85rem; min-height:35px; padding:6px 10px;">
                                                <?= ($valeur === null || $valeur === '') ? '<span class="text-muted italic small">[Vide]</span>' : htmlspecialchars($valeur) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-3 bg-white">
                                <button type="button" class="btn btn-light border w-100" data-bs-dismiss="modal" style="border-radius:12px;">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php elseif ($menu == 'notifications'): ?>
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_masse']) && $_POST['action_masse'] === 'supprimer_selection') {
                    if (!empty($_POST['notif_ids']) && is_array($_POST['notif_ids'])) {
                        $ids_a_supprimer = array_map('intval', $_POST['notif_ids']);
                        $notifs_placeholders = implode(',', array_fill(0, count($ids_a_supprimer), '?'));
                        $stmt_masse = $conn->prepare("DELETE FROM notifications WHERE id_notification IN ($notifs_placeholders) AND destinataire_role = 'Admin'");
                        $stmt_masse->execute($ids_a_supprimer);
                        echo "<script>window.location.href='?menu=notifications&filtre_notif=".htmlspecialchars($filtre_notif)."&success=Notifications supprimées';</script>";
                        exit;
                    }
                }

                if (isset($_GET['ouvrir_id'])) {
                    $id_ouvrir = intval($_GET['ouvrir_id']);
                    $stmt_auto_lu = $conn->prepare("UPDATE notifications SET statut_lecture = 1 WHERE id_notification = ? AND destinataire_role = 'Admin'");
                    $stmt_auto_lu->execute([$id_ouvrir]);
                    $stmt_get_body = $conn->prepare("SELECT * FROM notifications WHERE id_notification = ?");
                    $stmt_get_body->execute([$id_ouvrir]);
                    $notif_ouverte = $stmt_get_body->fetch();
                }
                ?>

                <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: #fff;">
                    <form id="formActionMasse" method="POST" action="">
                        <input type="hidden" name="action_masse" value="supprimer_selection">
                        
                        <div class="row g-0" style="min-height: 450px;">
                            <div class="col-lg-3 border-end p-3 responsive-mail-sidebar" style="background: #f8f9fa; border-color: var(--bordure) !important;">
                                <div class="nav flex-column nav-pills gap-1">
                                    <a href="?menu=notifications&filtre_notif=toutes" class="nav-link border-0 d-flex align-items-center justify-content-between py-2 px-3 <?= $filtre_notif == 'toutes' ? 'active' : '' ?>">
                                        <span><i class="fa-solid fa-inbox me-2" style="width:20px;"></i>Boîte de réception</span>
                                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary small ms-2"><?= count($notifications_list) ?></span>
                                    </a>
                                    <a href="?menu=notifications&filtre_notif=non_lues" class="nav-link border-0 d-flex align-items-center justify-content-between py-2 px-3 <?= $filtre_notif == 'non_lues' ? 'active' : '' ?>">
                                        <span><i class="fa-regular fa-envelope me-2" style="width:20px;"></i>Non lus</span>
                                        <?php if ($notif_count > 0): ?>
                                            <span class="badge rounded-pill bg-danger text-white small ms-2"><?= $notif_count ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <a href="?menu=notifications&filtre_notif=lues" class="nav-link border-0 d-flex align-items-center justify-content-between py-2 px-3 <?= $filtre_notif == 'lues' ? 'active' : '' ?>">
                                        <span><i class="fa-regular fa-envelope-open me-2" style="width:20px;"></i>Lus</span>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-lg-9 d-flex flex-column bg-white">
                                <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2" style="border-color: var(--bordure) !important;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-check m-0">
                                            <input class="form-check-input" type="checkbox" id="selectAllNotifs">
                                        </div>
                                        <button type="button" id="btnDeleteMasse" class="btn btn-light btn-sm text-danger border d-none py-1 px-2" style="border-radius: 20px;" onclick="executerSuppressionMasse()">
                                            <i class="fa-regular fa-trash-can"></i> <span id="countSelected">0</span>
                                        </button>
                                    </div>
                                    <div class="d-flex align-items-center style-search-box w-100-mobile" style="background: #f1f3f4; border-radius: 24px; padding: 2px 12px;">
                                        <i class="fa-solid fa-magnifying-glass text-muted me-2"></i>
                                        <input type="text" id="searchMailInput" class="form-control bg-transparent border-0 shadow-none py-1 small" placeholder="Filtrer...">
                                    </div>
                                </div>
                                
                                <?php if (isset($notif_ouverte) && $notif_ouverte): ?>
                                    <div class="p-3 border-bottom bg-light bg-opacity-50">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Détails de la Notification</h6>
                                                <small class="text-muted"><?= date('d/m à H:i', strtotime($notif_ouverte['created_at'])) ?></small>
                                            </div>
                                            <a href="?menu=notifications&filtre_notif=<?= $filtre_notif ?>" class="btn btn-sm btn-light border py-0 px-2"><i class="fa-solid fa-xmark"></i></a>
                                        </div>
                                        <div class="p-2 bg-white border rounded text-secondary font-monospace small" style="white-space: pre-line; word-break: break-word;">
                                            <?= htmlspecialchars($notif_ouverte['message']) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="flex-grow-1 overflow-auto" style="max-height: 400px;">
                                    <?php if (empty($notifications_list)): ?>
                                        <div class="text-center py-5">
                                            <i class="fa-solid fa-tray fa-2x text-muted mb-2 opacity-30"></i>
                                            <p class="text-muted small">Aucune notification disponible.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="list-group list-group-flush" id="mailContainerList">
                                            <?php foreach ($notifications_list as $notif): 
                                                $est_non_lu = ($notif['statut_lecture'] == 0);
                                                $est_active_view = (isset($id_ouvrir) && $id_ouvrir == $notif['id_notification']);
                                            ?>
                                                <div class="list-group-item list-group-item-action d-flex align-items-center px-3 py-2 position-relative border-bottom item-mail-row" 
                                                     style="<?= $est_non_lu ? 'background-color: #f7f9fa; font-weight: 700;' : 'background-color: #ffffff;' ?> <?= $est_active_view ? 'border-left: 4px solid var(--primaire) !important; background-color: #fff8f9;' : '' ?>"
                                                     id="notif_row_<?= $notif['id_notification'] ?>">
                                                    
                                                    <div class="d-flex align-items-center gap-2 me-2" style="z-index: 5;">
                                                        <input class="form-check-input mail-checkbox" type="checkbox" name="notif_ids[]" value="<?= $notif['id_notification'] ?>" onchange="calculerSelection()">
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-center flex-grow-1 min-w-0" onclick="window.location.href='?menu=notifications&filtre_notif=<?= $filtre_notif ?>&ouvrir_id=<?= $notif['id_notification'] ?>';" style="cursor: pointer;">
                                                        <div class="mail-sender text-truncate me-2 <?= $est_non_lu ? 'text-dark fw-bold' : 'text-secondary' ?>" style="width: 120px; font-size: 0.8rem;">
                                                            Système GDE
                                                        </div>
                                                        <div class="mail-body text-truncate pe-4 <?= $est_non_lu ? 'text-dark fw-bold' : 'text-secondary' ?>" style="font-size: 0.82rem;">
                                                            <?= htmlspecialchars($notif['message']) ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mail-date text-end text-muted small" style="min-width: 65px; font-size: 0.75rem;">
                                                        <?= (date('Y-m-d', strtotime($notif['created_at'])) == date('Y-m-d')) ? date('H:i', strtotime($notif['created_at'])) : date('d M.', strtotime($notif['created_at'])) ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <script>
                    document.getElementById('selectAllNotifs')?.addEventListener('change', function() {
                        const checkboxes = document.querySelectorAll('.mail-checkbox');
                        checkboxes.forEach(cb => cb.checked = this.checked);
                        calculerSelection();
                    });

                    function calculerSelection() {
                        const checkboxes = document.querySelectorAll('.mail-checkbox:checked');
                        const btnDeleteMasse = document.getElementById('btnDeleteMasse');
                        const countSpan = document.getElementById('countSelected');
                        if (checkboxes.length > 0) {
                            btnDeleteMasse.classList.remove('d-none');
                            countSpan.textContent = checkboxes.length;
                        } else {
                            btnDeleteMasse.classList.add('d-none');
                            if(document.getElementById('selectAllNotifs')) document.getElementById('selectAllNotifs').checked = false;
                        }
                    }

                    function executerSuppressionMasse() {
                        Swal.fire({
                            title: 'Supprimer les éléments ?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#a21c3b',
                            cancelButtonColor: '#7d6f77',
                            confirmButtonText: 'Supprimer',
                            cancelButtonText: 'Annuler'
                        }).then((result) => {
                            if (result.isConfirmed) document.getElementById('formActionMasse').submit();
                        });
                    }

                    document.getElementById('searchMailInput')?.addEventListener('input', function() {
                        const query = this.value.toLowerCase().trim();
                        document.querySelectorAll('.item-mail-row').forEach(row => {
                            row.style.setProperty('display', row.textContent.toLowerCase().includes(query) ? 'flex' : 'none', 'important');
                        });
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="changePassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Modifier mon profil</h5>
                </div>
                <form action="req_update_profile.php" method="POST" class="p-4">
                    <div class="mb-3"><label class="small fw-bold">Nom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required></div>
                    <div class="mb-3"><label class="small fw-bold">Prénom</label><input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required></div>
                    <div class="mb-3"><label class="small fw-bold">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required></div>
                    <div class="mb-3"><label class="small fw-bold">Nom d'utilisateur</label><input type="text" name="uname" class="form-control" value="<?= htmlspecialchars($user['nomutilisateur'] ?? '') ?>" required></div>
                    <div class="mb-3"><label class="small fw-bold">Ancien mot de passe</label><input type="password" name="old_pass" class="form-control" placeholder="Obligatoire" required></div>
                    <div class="mb-4"><label class="small fw-bold">Nouveau mot de passe</label><input type="password" name="new_pass" class="form-control" placeholder="Laisser vide si inchangé"></div>
                    <button type="submit" class="btn btn-primary w-100">Enregistrer les modifications</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addDeptModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <form action="req_add_dept.php" method="POST">
                    <div class="modal-header bg-dark text-white border-0 py-3"><h5 class="fw-bold mb-0">Nouveau Département</h5></div>
                    <div class="modal-body p-4">
                        <div class="mb-3"><label class="small fw-bold">Nom du département</label><input type="text" name="nom_departement" class="form-control" required placeholder="Ex: Informatique"></div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0"><button type="submit" class="btn btn-primary w-100 py-2">Créer</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addFiliereModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <form action="req_add_filiere.php" method="POST">
                    <div class="modal-header bg-dark text-white border-0 py-3"><h5 class="fw-bold mb-0">Nouvelle Filière</h5></div>
                    <div class="modal-body p-4">
                        <div class="mb-3"><label class="small fw-bold">Nom de la filière</label><input type="text" name="nom_filiere" class="form-control" required></div>
                        <div class="mb-3"><label class="small fw-bold">Abréviation (Code)</label><input type="text" name="abreviation" class="form-control" required></div>
                        <div class="mb-3"><label class="small fw-bold">Niveau (Bac + X)</label><input type="number" name="niveau" class="form-control" value="3" min="1" max="7" required></div>
                        <div class="mb-3">
                            <label class="small fw-bold">Département Assigné</label>
                            <select name="id_departement" class="form-select" required>
                                <option value="">-- Choisir --</option>
                                <?php 
                                $depts_list = $conn->query("SELECT * FROM departement ORDER BY nom_departement ASC")->fetchAll();
                                foreach($depts_list as $d): ?>
                                    <option value="<?= $d['id_departement'] ?>"><?= htmlspecialchars($d['nom_departement']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0"><button type="submit" class="btn btn-primary w-100 py-2">Créer la filière</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addRespModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="req_add_resp.php" method="POST">
                    <div class="modal-header bg-primary text-white border-0 py-3"><h5 class="fw-bold mb-0">Créer un profil Responsable</h5></div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12"><label class="small fw-bold text-primary">Identifiant (ID) Unique</label><input type="number" name="forced_id" class="form-control border-primary" placeholder="Ex: 101" required></div>
                            <div class="col-md-6"><label class="small fw-bold">Nom d'utilisateur</label><input type="text" name="uname" class="form-control" required></div>
                            <div class="col-md-6"><label class="small fw-bold">Mot de passe initial</label><input type="password" name="pass" class="form-control" required></div>
                            <div class="col-md-6"><label class="small fw-bold">Nom</label><input type="text" name="nom" class="form-control" required></div>
                            <div class="col-md-6"><label class="small fw-bold">Prénom</label><input type="text" name="prenom" class="form-control" required></div>
                            <div class="col-md-6"><label class="small fw-bold">Email</label><input type="email" name="email" class="form-control" required></div>
                            <div class="col-md-6"><label class="small fw-bold">Téléphone</label><input type="text" name="telephone" class="form-control" required></div>
                            <div class="col-md-12">
                                <label class="small fw-bold">Département Assigné</label>
                                <select name="id_departement" class="form-select" required>
                                    <option value="">-- Choisir un département --</option>
                                    <?php 
                                    $departements_list = $conn->query("SELECT * FROM departement ORDER BY nom_departement ASC")->fetchAll();
                                    foreach($departements_list as $d): ?>
                                        <option value="<?= $d['id_departement'] ?>"><?= htmlspecialchars($d['nom_departement']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0"><button type="submit" class="btn btn-primary w-100 py-3">Enregistrer</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Fonction Javascript exclusive pour ouvrir/fermer le menu de navigation sur mobile
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebarMenu');
            if (sidebar) {
                sidebar.classList.toggle('show');
            }
        }

        // Script de filtrage visuel en JS natif pour les listes
        document.getElementById('searchResp')?.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tableResponsables tbody tr').forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        document.getElementById('searchEtudiant')?.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tableEtudiants tbody tr').forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        function confirmDelete(type, id) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette suppression sera définitive !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#a21c3b',
                cancelButtonColor: '#7d6f77',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '';
                    if (type === 'resp') url = 'req_delete_resp.php?id=';
                    else if (type === 'filiere') url = 'req_delete_filiere.php?id=';
                    else if (type === 'etudiant') url = 'req_delete_etudiant.php?id=';
                    else if (type === 'dept') url = 'req_delete_dept.php?id=';
                    window.location.href = url + id;
                }
            })
        }

        <?php if (isset($_GET['success'])): ?>
        Swal.fire({ icon: 'success', title: 'Succès', text: '<?= htmlspecialchars($_GET['success']) ?>', timer: 3000, showConfirmButton: false, borderRadius: '15px' });
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
        Swal.fire({ icon: 'error', title: 'Erreur', text: '<?= htmlspecialchars($_GET['error']) ?>', confirmButtonColor: '#a21c3b' });
        <?php endif; ?>
    </script>
</body>
</html>
<?php 
} else { header("Location: ../connexion.php"); exit; } ?>