<?php 
session_start();

if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    $admin_id = $_SESSION['admin_id'];
    $stmt_user = $conn->prepare("SELECT * FROM administrateur WHERE administrateur_id = ?");
    $stmt_user->execute([$admin_id]);
    $user = $stmt_user->fetch();

    $menu = isset($_GET['menu']) ? $_GET['menu'] : 'responsables';

    if ($menu == 'responsables') {
        $sql = "SELECT * FROM responsablefiliere ORDER BY id_responsablefiliere DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $responsables = $stmt->fetchAll();

        $sql_stats = "SELECT filiere, COUNT(*) as total FROM responsablefiliere GROUP BY filiere";
        $stats_resp = $conn->query($sql_stats)->fetchAll();

        $filieres_list = $conn->query("SELECT nom_filiere FROM filiere")->fetchAll();
    } 

    if ($menu == 'filieres') {
        $sql = "SELECT * FROM filiere ORDER BY nom_filiere ASC";
        $filieres = $conn->query($sql)->fetchAll();
    } 

    if ($menu == 'etudiants') {
        // On utilise e.* pour avoir tous les nouveaux champs (santé, parents, id_permanent)
        $sql = "SELECT e.*, f.nom_filiere FROM etudiants e 
                LEFT JOIN filiere f ON e.id_filiere = f.id_filiere 
                ORDER BY e.date_inscription DESC";
        $etudiants = $conn->query($sql)->fetchAll();
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
            /* Fonds de page, sections et conteneurs (Teintes claires, crème et ivoire) */
            --fond: #fafefc;              /* --color-4 : Blanc opale très doux pour le fond général */
            --fond-secondaire: #fbf4f0;   /* --color-5 : Crème chaud pour les blocs et formulaires */
            --blanc: #fffffe;             /* --color-3 : Blanc pur pour les cartes et tables */
            
            /* Textes (Haute lisibilité) */
            --texte: #090b3c;             /* --color-6 : Bleu nuit très sombre pour les titres et textes principaux */
            --texte-secondaire: #573e4d;  /* --color-10 : Prune foncé pour les sous-titres et labels */
            
            /* Éléments d'accentuation et boutons (Vifs et professionnels) */
            --primaire: #a21c3b;          /* --color-7 : Bordeaux/Rouge académique pour les actions principales */
            --primaire-hover: #391f20;    /* --color-1 : Brun chocolat profond pour le survol (hover) */
            
            /* Bordures, badges et états secondaires */
            --bordure: #d9d6df;           /* --color-2 : Gris lavande clair pour des séparations très épurées */
            --secondaire: #7d6f77;        /* --color-8 : Gris de roche pour les boutons secondaires ou désactivés */
            --info: #b8aeb0;              /* --color-9 : Gris perle pour les arrière-plans d'éléments neutres */
            
            /* Ombres légères pour garder le style épuré */
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

        .sidebar .bg-primary {
            background-color: var(--primaire) !important;
        }
        
        .sidebar .text-primary {
            color: var(--primaire) !important;
        }

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

        .nav-link i { 
            font-size: 1.1rem; 
            width: 25px; 
            margin-right: 10px; 
        }

        .nav-link:hover, .nav-link.active { 
            background: var(--fond-secondaire); 
            color: var(--primaire); 
        }

        .main-content { 
            margin-left: 280px; 
            min-height: 100vh; 
        }

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

        .top-bar .text-primary {
            color: var(--primaire) !important;
        }

        .content-body { 
            padding: 2.5rem; 
        }

        .white-card { 
            background: var(--blanc); 
            border-radius: 20px; 
            padding: 1.5rem; 
            border: 1px solid var(--bordure); 
            box-shadow: var(--ombre);
        }

        .stat-card {
            border-bottom: 4px solid var(--primaire); 
            transition: transform 0.2s;
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: var(--ombre-md);
        }

        .table thead th { 
            background: var(--fond-secondaire); 
            border: none; 
            color: var(--texte-secondaire);
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 0.05em;
            padding: 12px 15px;
        }
        .table tbody td { 
            padding: 15px; 
            border-bottom: 1px solid var(--bordure); 
            color: var(--texte);
        }

        .status-pill { 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            background: var(--fond-secondaire); 
            color: var(--primaire); 
        }

        .form-control, .form-select { 
            background: var(--fond); 
            border: 1px solid var(--bordure); 
            border-radius: 12px; 
            padding: 10px 15px;
            color: var(--texte);
        }
        .form-control:focus, .form-select:focus { 
            background: var(--blanc);
            color: var(--texte);
            box-shadow: 0 0 0 4px rgba(162, 28, 59, 0.1); 
            border-color: var(--primaire); 
        }

        .btn-primary { 
            background: var(--primaire); 
            border: none; 
            border-radius: 12px; 
            padding: 10px 20px; 
            font-weight: 600; 
            box-shadow: 0 4px 6px rgba(162, 28, 59, 0.2);
            transition: all 0.2s;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background: var(--primaire-hover) !important;
            box-shadow: 0 4px 12px rgba(57, 31, 32, 0.3);
        }

        .btn-light {
            background: var(--fond);
            border-color: var(--bordure);
            color: var(--texte-secondaire);
        }
        .btn-light:hover {
            background: var(--fond-secondaire);
            border-color: var(--secondaire);
        }
        .text-primary {
            color: var(--primaire) !important;
        }
        .text-muted {
            color: var(--texte-secondaire) !important;
        }
        .bg-light {
            background-color: var(--fond-secondaire) !important;
        }
        .modal-content {
            background: var(--blanc);
            color: var(--texte);
            border: 1px solid var(--bordure);
        }
        .modal-header.bg-dark {
            background-color: var(--primaire-hover) !important;
        }
        .modal-header.bg-primary {
            background-color: var(--primaire) !important;
        }
        .modal-body.bg-light {
            background-color: var(--fond) !important;
        }
        .modal-footer .btn-secondary {
            background-color: var(--secondaire);
            border: none;
        }
        .modal-footer .btn-secondary:hover {
            background-color: var(--texte-secondaire);
        }
        .badge.bg-primary.bg-opacity-10 {
            background-color: var(--fond-secondaire) !important;
            color: var(--primaire) !important;
            border: 1px solid rgba(162, 28, 59, 0.2);
        }
        .white-card.bg-primary.bg-opacity-10 {
            background-color: var(--fond-secondaire) !important;
            border: 1px solid var(--bordure);
        }
        code.text-primary {
            color: var(--primaire) !important;
            background: var(--fond-secondaire);
            padding: 2px 6px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="d-flex align-items-center mb-5 px-2">
            <div class="bg-primary text-white rounded-3 p-2 me-3">
                <i class="fa-solid fa-shield-halved fa-lg"></i>
            </div>
            <h5 class="fw-bold mb-0">ESPACE <span class="text-primary text-opacity-50">ADMINISTRATEUR</span></h5>
        </div>

        <nav class="nav flex-column">
            <small class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.65rem; letter-spacing: 1px;">Menu Principal</small>
            <a href="?menu=responsables" class="nav-link <?= $menu == 'responsables' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-tie"></i> Responsables Filières
            </a>
            <a href="?menu=filieres" class="nav-link <?= $menu == 'filieres' ? 'active' : '' ?>">
                <i class="fa-solid fa-graduation-cap"></i> Filières
            </a>
            <a href="?menu=etudiants" class="nav-link <?= $menu == 'etudiants' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Étudiants
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
            <h4 class="fw-bold mb-0">
                <?php 
                    if($menu == 'responsables') echo "Gestion des Responsables";
                    elseif($menu == 'filieres') echo "Configuration des Filières";
                    else echo "Annuaire Étudiants";
                ?>
            </h4>
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
            
            <?php if ($menu == 'responsables'): ?>
                <div class="row g-4 mb-4">
                    <?php foreach($stats_resp as $stat): ?>
                    <div class="col-md-3">
                        <div class="white-card stat-card text-center">
                            <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.7rem;"><?= $stat['filiere'] ?></p>
                            <h2 class="fw-bold mb-0"><?= $stat['total'] ?></h2>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="col-md-3">
                        <button class="white-card w-100 h-100 d-flex align-items-center justify-content-center text-primary fw-bold" 
                                style="border: 2px dashed var(--primaire); background: transparent;"
                                data-bs-toggle="modal" data-bs-target="#addRespModal">
                            <i class="fa-solid fa-plus me-2"></i> Ajouter un responsable
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group" style="max-width: 400px;">
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
                                    <th>Filière</th>
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
                                    <td><span class="status-pill"><?= $r['filiere'] ?></span></td>
                                    <td>
                                        <div class="small"><i class="fa-regular fa-envelope me-2"></i><?= $r['email'] ?></div>
                                        <div class="small text-muted"><i class="fa-solid fa-phone me-2"></i><?= $r['telephone'] ?></div>
                                    </td>
                                    <td class="text-end">
                                        <a href="edit_resp.php?id=<?= $r['id_responsablefiliere'] ?>" class="btn btn-sm btn-light text-primary border"><i class="fa-pen-to-square"></i></a>
                                        <button onclick="confirmDelete('resp', <?= $r['id_responsablefiliere'] ?>)" class="btn btn-sm btn-light text-danger border ms-1"><i class="fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($menu == 'filieres'): ?>

                <div class="d-flex justify-content-between mb-4">
                    <p class="text-muted">Gérez les départements et les niveaux d'études</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFiliereModal"><i class="fa-plus me-2"></i>Nouvelle Filière</button>
                </div>

                <div class="row g-4">
                    <?php foreach ($filieres as $f): ?>
                    <div class="col-md-4">
                        <div class="white-card h-100 d-flex flex-column justify-content-between shadow-sm">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?= $f['abreviation_filiere'] ?></span>
                                    <button onclick="confirmDelete('filiere', <?= $f['id_filiere'] ?>)" class="btn btn-link text-danger p-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                </div>
                                <h5 class="fw-bold"><?= $f['nom_filiere'] ?></h5>
                            </div>
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center" style="border-color: var(--bordure) !important;">
                                <span class="small text-muted"><i class="fa-solid fa-layer-group me-2"></i>Niveau d'étude</span>
                                <span class="fw-bold">Bac +<?= $f['niveau'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($menu == 'etudiants'): ?>

                <div class="white-card">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap g-3">
                        <h5 class="fw-bold mb-0">Total inscrits : <?= count($etudiants) ?></h5>
                        <div class="input-group" style="max-width: 400px;">
                            <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px; border-color: var(--bordure);">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text" id="searchEtudiant" class="form-control border-start-0" placeholder="Nom, ID ou filière..." style="border-radius: 0 12px 12px 0;">
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
                                        <div class="fw-bold text-dark"><?= strtoupper($e['nom']) ?> <?= $e['prenom'] ?></div>
                                        <div class="small text-muted"><?= $e['email'] ?></div>
                                    </td>
                                    <td>
                                        <span class="status-pill"><?= $e['nom_filiere'] ?></span>
                                        <div class="small mt-1 text-muted"><?= $e['niveau_etude'] ?> (<?= $e['cours'] ?>)</div>
                                    </td>
                                    <td><div class="small"><i class="fa-solid fa-phone me-1"></i> <?= $e['contact_etudiant'] ?></div></td>
                                    <td class="text-end">
                                       
                                        <button class="btn btn-sm btn-primary border ms-1" data-bs-toggle="modal" data-bs-target="#viewEtudiant<?= $e['id_etudiants'] ?>">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button onclick="confirmDelete('etudiant', <?= $e['id_etudiants'] ?>)" class="btn btn-sm btn-light text-danger border ms-1">
                                            <i class="fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php foreach ($etudiants as $e): ?>
                <div class="modal fade" id="viewEtudiant<?= $e['id_etudiants'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered shadow-lg">
                        <div class="modal-content border-0" style="border-radius: 20px;">
                            <div class="modal-header bg-dark text-white border-0 py-3 px-4">
                                <h5 class="mb-0"><i class="fa-solid fa-address-card me-2 text-primary"></i> Dossier Complet : <?= $e['id_permanent'] ?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            
                            <div class="modal-body p-4 bg-light">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="white-card h-100 shadow-sm">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary" style="border-color: var(--bordure) !important;">1. ÉTAT CIVIL</h6>
                                            <p class="small mb-1 text-muted">Nom & Prénoms</p>
                                            <p class="fw-bold mb-3"><?= strtoupper($e['nom']) ?> <?= $e['prenom'] ?></p>
                                            
                                            <p class="small mb-1 text-muted">Né(e) le</p>
                                            <p class="fw-bold mb-3"><?= date('d/m/Y', strtotime($e['date_naissance'])) ?> à <?= $e['lieu_naissance'] ?></p>
                                            
                                            <p class="small mb-1 text-muted">Sexe & Nationalité</p>
                                            <p class="fw-bold mb-3"><?= $e['sexe'] ?> | <?= $e['nationalite'] ?></p>

                                            <p class="small mb-1 text-muted">Pièces d'identité</p>
                                            <p class="fw-bold mb-0">CNI: <?= $e['n_carte_identite'] ?></p>
                                            <p class="fw-bold">CMU: <?= $e['n_cmu'] ?></p>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="white-card shadow-sm mb-3">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-success" style="border-color: var(--bordure) !important;">2. ACADÉMIQUE</h6>
                                            <p class="small mb-1 text-muted">Filière & Régime</p>
                                            <p class="fw-bold mb-2"><?= $e['nom_filiere'] ?> (<?= $e['cours'] ?>)</p>
                                            
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="small mb-0 text-muted">Niveau</p>
                                                    <p class="fw-bold"><?= $e['niveau_etude'] ?></p>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <p class="small mb-0 text-muted">Année BAC</p>
                                                    <p class="fw-bold"><?= $e['annee_diplome'] ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="white-card shadow-sm border-start border-danger border-4">
                                            <h6 class="fw-bold mb-2 text-danger">3. SANTÉ</h6>
                                            <div class="row g-2">
                                                <div class="col-6 small">Asthme: <b><?= $e['asthme'] ?></b></div>
                                                <div class="col-6 small">Tension: <b><?= $e['hypertension'] ?></b></div>
                                                <div class="col-6 small">Psy: <b><?= $e['problemes_psychiques'] ?></b></div>
                                                <div class="col-6 small">Handicap: <b><?= $e['handicap_physique'] ?></b></div>
                                            </div>
                                            <p class="small mt-2 text-muted italic">Obs: <?= $e['autres_infos_sante'] ?></p>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="white-card h-100 shadow-sm bg-primary bg-opacity-10">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary" style="border-color: var(--primaire) !important;">4. CONTACTS PARENTS</h6>
                                            
                                            <div class="mb-3">
                                                <p class="small mb-0 fw-bold text-primary">Père :</p>
                                                <p class="mb-0"><?= $e['nom_prenom_pere'] ?></p>
                                                <p class="small text-muted"><i class="fa-solid fa-phone"></i> <?= $e['telephone_pere'] ?></p>
                                            </div>

                                            <div class="mb-3">
                                                <p class="small mb-0 fw-bold text-primary">Mère :</p>
                                                <p class="mb-0"><?= $e['nom_prenom_mere'] ?></p>
                                                <p class="small text-muted"><i class="fa-solid fa-phone"></i> <?= $e['telephone_mere'] ?></p>
                                            </div>

                                            <div class="p-2 bg-white rounded border" style="border-color: var(--bordure) !important; background: var(--blanc) !important;">
                                                <p class="small mb-0 fw-bold text-dark">Tuteur Légal :</p>
                                                <p class="mb-0"><?= $e['tuteur_legal'] ?></p>
                                                <p class="fw-bold text-primary small mb-0"><?= $e['telephone_tuteur'] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fermer le dossier</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </div>

    <div class="modal fade" id="changePassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Modifier mon profil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="req_update_profile.php" method="POST" class="p-4">
                    <div class="mb-3">
                        <label class="small fw-bold">Nom</label>
                        <input type="text" name="nom" class="form-control" value="<?= $user['nom'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Prénom</label>
                        <input type="text" name="prenom" class="form-control" value="<?= $user['prenom'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Nom d'utilisateur</label>
                        <input type="text" name="uname" class="form-control" value="<?= $user['nomutilisateur'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Ancien mot de passe</label>
                        <input type="password" name="old_pass" class="form-control" placeholder="Obligatoire pour valider" required>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold">Nouveau mot de passe</label>
                        <input type="password" name="new_pass" class="form-control" placeholder="Laisser vide si inchangé">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Enregistrer les modifications</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addRespModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="req_add_resp.php" method="POST">
                    <div class="modal-header bg-primary text-white border-0 py-3">
                        <h5 class="fw-bold mb-0">Créer un profil Responsable</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="small fw-bold text-primary">Identifiant (ID) Unique</label>
                                <input type="number" name="forced_id" class="form-control border-primary" placeholder="Ex: 101" style="border-color: var(--primaire) !important;" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold">Nom d'utilisateur</label>
                                <input type="text" name="uname" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold">Mot de passe initial</label>
                                <input type="password" name="pass" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold">Nom</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold">Prénom</label>
                                <input type="text" name="prenom" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold">Téléphone</label>
                                <input type="text" name="telephone" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="small fw-bold">Filière d'affectation</label>
                                <select name="filiere" class="form-select" required>
                                    <option value="">Sélectionner une filière...</option>
                                    <?php foreach($filieres_list as $fl): ?>
                                        <option value="<?= $fl['nom_filiere'] ?>"><?= $fl['nom_filiere'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-primary w-100 py-3">Enregistrer le responsable</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addFiliereModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <form action="req_add_filiere.php" method="POST">
                    <div class="modal-header bg-dark text-white border-0 py-3">
                        <h5 class="fw-bold mb-0">Nouvelle Filière</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="small fw-bold">Nom de la filière</label>
                            <input type="text" name="nom_filiere" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Abréviation (Code)</label>
                            <input type="text" name="abreviation" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Niveau (Bac + X)</label>
                            <input type="number" name="niveau" class="form-control" value="3" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-primary w-100 py-2">Créer la filière</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // LOGIQUE DE RECHERCHE DYNAMIQUE
        document.addEventListener('DOMContentLoaded', function() {
            function initSearch(inputId, tableId) {
                const searchInput = document.getElementById(inputId);
                const table = document.getElementById(tableId);
                if(!searchInput || !table) return;

                searchInput.addEventListener('keyup', function() {
                    const filter = searchInput.value.toLowerCase();
                    const rows = table.getElementsByTagName('tr');

                    for (let i = 1; i < rows.length; i++) {
                        const content = rows[i].textContent.toLowerCase();
                        rows[i].style.display = content.includes(filter) ? '' : 'none';
                    }
                });
            }
            initSearch('searchResp', 'tableResponsables');
            initSearch('searchEtudiant', 'tableEtudiants');
        });

        // CONFIRMATION DE SUPPRESSION
        function confirmDelete(type, id) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette suppression sera définitive !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#a21c3b',
                cancelButtonColor: '#7d6f77',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                borderRadius: '15px'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '';
                    if (type === 'resp') url = 'req_delete_resp.php?id=';
                    else if (type === 'filiere') url = 'req_delete_filiere.php?id=';
                    else if (type === 'etudiant') url = 'req_delete_etudiant.php?id=';
                    window.location.href = url + id;
                }
            })
        }

        // ALERTES DE SUCCÈS/ERREUR
        <?php if (isset($_GET['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Génial !',
            text: '<?= $_GET['success'] ?>',
            timer: 3000,
            showConfirmButton: false,
            borderRadius: '15px'
        });
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Oups...',
            text: '<?= $_GET['error'] ?>',
            confirmButtonColor: '#a21c3b'
        });
        <?php endif; ?>
    </script>

</body>
</html>

<?php 
} else { 
    header("Location: ../connexion.php"); 
    exit; 
} 
?>