<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Responsable') {
    header("Location: ../connexion.php?error=Veuillez vous connecter");
    exit;
}

include_once "../DB_connexion.php";

$id_responsable = $_SESSION['user_id'];
$prenom = $_SESSION['prenom'] ?? 'Responsable';
$nom    = $_SESSION['nom']    ?? '';

// 1. Récupération du département du responsable connecté
$nom_departement = "Aucun département assigné";
$id_departement = 0;
try {
    $stmt_dept = $conn->prepare("SELECT id_departement, nom_departement FROM departement WHERE id_responsable = ? LIMIT 1");
    $stmt_dept->execute([$id_responsable]);
    $dept = $stmt_dept->fetch(PDO::FETCH_ASSOC);
    if ($dept) {
        $id_departement = $dept['id_departement'];
        $nom_departement = $dept['nom_departement'];
    }
} catch (Exception $e) {
    // Gestion d'erreur silencieuse
}

// AJOUT : Récupération dynamique de l'année universitaire active définie par l'admin
try {
    $annee_active = $conn->query("SELECT valeur FROM configuration_gde WHERE cle = 'annee_universitaire_active'")->fetchColumn();
    if (!$annee_active) {
        $annee_active = date('Y') . "-" . (date('Y') + 1); // Valeur de secours par défaut
    }
} catch (Exception $e) {
    $annee_active = date('Y') . "-" . (date('Y') + 1);
}

// 2. Statistiques des effectifs par niveau (uniquement pour le département du responsable ET filtré par l'année active)
$stats_niveaux = [];
if ($id_departement > 0) {
    try {
        $sql_stats = "SELECT e.niveau, COUNT(*) as total 
                      FROM etudiants e
                      JOIN filiere f ON e.id_filiere = f.id_filiere
                      WHERE f.id_departement = ? 
                        AND e.statut_inscription = 'Validé définitivement'
                        AND e.annee_scolaire = ?
                      GROUP BY e.niveau
                      ORDER BY e.niveau ASC";
        $stmt_s = $conn->prepare($sql_stats);
        $stmt_s->execute([$id_departement, $annee_active]);
        $stats_niveaux = $stmt_s->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats_niveaux = [];
    }
}

// 3. Traitement des filtres et moteur de recherche des notifications
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'tous';

// Construction de la requête de notifications
$sql_notifs = "SELECT * FROM notifications WHERE destinataire_role = 'Responsable' AND id_destinataire_specifique = ? ";
$params = [$id_responsable];

if ($filter === 'non_lu') {
    $sql_notifs .= " AND statut_lecture = 0";
} elseif ($filter === 'lu') {
    $sql_notifs .= " AND statut_lecture = 1";
} elseif ($filter === 'historique') {
    $sql_notifs .= " AND statut_lecture = 2";
}

if (!empty($search)) {
    $sql_notifs .= " AND message LIKE ?";
    $params[] = "%$search%";
}

$sql_notifs .= " ORDER BY created_at DESC";
$stmt_n = $conn->prepare($sql_notifs);
$stmt_n->execute($params);
$notifications = $stmt_n->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil | GDE Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
            --ombre: 0 1px 2px rgba(9, 11, 60, 0.05);
            --ombre-md: 0 4px 12px rgba(9, 11, 60, 0.08);
        }

        body { 
            background-color: var(--fond);
            color: var(--texte);
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.022em;
            -webkit-font-smoothing: antialiased;
        }

        .navbar-minimal {
            background-color: var(--blanc);
            border-bottom: 1px solid var(--bordure);
            padding: 1rem 0;
        }

        .navbar-minimal .text-primary { color: var(--primaire) !important; }

        .glass-card {
            background: var(--blanc);
            border: 1px solid var(--bordure);
            border-radius: 24px;
            padding: 1.5rem;
        }
        
        @media (min-width: 576px) {
            .glass-card {
                padding: 2rem;
            }
        }
        @media (min-width: 992px) {
            .glass-card {
                padding: 2.5rem;
            }
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--bordure);
        }
        .stat-item:last-child { border: 0; }

        .btn-dark-modern {
            background: var(--primaire);
            color: var(--blanc);
            padding: 0.75rem 1.5rem;
            border-radius: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            border: none;
            transition: 0.2s;
        }
        .btn-dark-modern:hover {
            background: var(--primaire-hover);
            color: var(--blanc);
            transform: translateY(-2px);
        }

        .profile-link {
            background: var(--fond-secondaire);
            padding: 6px 14px;
            border-radius: 100px;
            border: 1px solid var(--bordure);
            text-decoration: none;
            color: var(--texte-secondaire);
            font-weight: 500;
            font-size: 0.85rem;
        }

        .tag-filiere {
            color: var(--primaire);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Amélioration de la barre d'onglets pour le défilement mobile */
        .nav-tabs-responsive {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-bottom: 1px solid var(--bordure);
            margin-bottom: 1.5rem;
        }
        .nav-tabs-responsive .nav-item {
            flex: 0 0 auto;
        }
        .nav-tabs-responsive .nav-link {
            color: var(--texte-secondaire);
            border: none;
            font-weight: 500;
            padding: 0.6rem 1rem;
            white-space: nowrap;
        }
        .nav-tabs-responsive .nav-link.active {
            color: var(--primaire);
            border-bottom: 3px solid var(--primaire);
            background: none;
            font-weight: 700;
        }

        .notif-card {
            background: var(--blanc);
            border: 1px solid var(--bordure);
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }
        @media (min-width: 576px) {
            .notif-card { padding: 1.5rem; }
        }
        .notif-unread { border-left: 5px solid var(--primaire); }
        
        .fiche-inscription {
            background: var(--fond-secondaire);
            border-radius: 12px;
            padding: 1rem;
            font-size: 0.85rem;
            margin-top: 1rem;
            border: 1px dashed var(--bordure);
        }
        @media (min-width: 576px) {
            .fiche-inscription { padding: 1.25rem; font-size: 0.9rem; }
        }
        .section-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primaire);
            text-transform: uppercase;
            border-bottom: 1px solid var(--bordure);
            padding-bottom: 3px;
            margin-top: 10px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

    <nav class="navbar-minimal">
        <div class="container d-flex flex-row justify-content-between align-items-center">
            <div class="fw-800 fs-5 fs-sm-4">Espace<span class="text-primary"> Responsable</span></div>
            <div class="d-flex align-items-center gap-2 gap-sm-4">
                <a href="profil.php" class="profile-link text-truncate" style="max-width: 140px;">
                    <i class="fa-regular fa-user-circle me-1"></i><?= htmlspecialchars($prenom) ?>
                </a>
                <a href="../Deconnexion.php" class="text-muted text-decoration-none small fw-medium">Déconnexion</a>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-lg-5 mt-lg-4">
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 g-lg-5">
            
            <div class="col-lg-4">
                <div class="mb-4 mb-lg-5">
                    <span class="tag-filiere">Département académique</span>
                    <h2 class="fw-800 tracking-tight mt-1 fs-3 fs-sm-2"><?= htmlspecialchars($nom_departement) ?></h2>
                    <small class="text-muted fw-semibold"><i class="fa-solid fa-calendar-day me-1"></i> Année active : <?= htmlspecialchars($annee_active) ?></small>
                </div>
                
                <div class="glass-card mb-4">
                    <h5 class="fw-bold mb-3">Effectifs Validés</h5>
                    <div class="stats-list">
                        <?php foreach($stats_niveaux as $stat): ?>
                            <div class="stat-item">
                                <span class="text-muted fw-medium">Niveau <?= htmlspecialchars($stat['niveau']) ?></span>
                                <span class="fw-bold"><?= $stat['total'] ?></span>
                            </div>
                        <?php endforeach; ?>
                        <?php if(empty($stats_niveaux)): ?>
                            <p class="small text-muted m-0">Aucun étudiant validé pour le moment.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="py-1">
                    <a href="classes.php" class="btn-dark-modern w-100 justify-content-center shadow-sm">
                        Ouvrir la gestion des étudiants <i class="fa-solid fa-arrow-right-long"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass-card">
                    <h3 class="fw-800 mb-4 fs-4 fs-sm-3">Fiches d'inscription reçues</h3>
                    
                    <form method="GET" action="" class="row g-2 mb-4">
                        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher dans les messages..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-dark w-100" style="border-radius:10px; padding: 0.6rem;">Filtrer</button>
                        </div>
                    </form>

                    <ul class="nav nav-tabs-responsive">
                        <li class="nav-item">
                            <a class="nav-link <?= $filter === 'tous' ? 'active' : '' ?>" href="?filter=tous&search=<?= urlencode($search) ?>">Tous</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $filter === 'non_lu' ? 'active' : '' ?>" href="?filter=non_lu&search=<?= urlencode($search) ?>">Non lus</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $filter === 'lu' ? 'active' : '' ?>" href="?filter=lu&search=<?= urlencode($search) ?>">Lus</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $filter === 'historique' ? 'active' : '' ?>" href="?filter=historique&search=<?= urlencode($search) ?>">Historique</a>
                        </li>
                    </ul>

                    <div class="notifs-container">
                        <?php if(empty($notifications)): ?>
                            <div class="text-center py-5">
                                <i class="fa-regular fa-folder-open fs-1 text-muted mb-3"></i>
                                <p class="text-muted m-0">Aucun message ou dossier reçu dans cette section.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach($notifications as $notif): 
                                $is_unread = ($notif['statut_lecture'] == 0);
                                
                                preg_match('/ID:\s*(\d+)/', $notif['message'], $matches);
                                $id_etudiant_extrait = isset($matches[1]) ? intval($matches[1]) : null;
                                
                                $etudiant_data = null;
                                if ($id_etudiant_extrait) {
                                    $stmt_etudiant = $conn->prepare("SELECT e.*, f.nom_filiere FROM etudiants e JOIN filiere f ON e.id_filiere = f.id_filiere WHERE e.id_etudiants = ?");
                                    $stmt_etudiant->execute([$id_etudiant_extrait]);
                                    $etudiant_data = $stmt_etudiant->fetch(PDO::FETCH_ASSOC);
                                }
                            ?>
                                <div class="notif-card <?= $is_unread ? 'notif-unread' : '' ?>">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-1 mb-2">
                                        <h6 class="fw-bold m-0 text-uppercase text-secondary small">
                                            <i class="fa-solid fa-envelope-open-text me-2"></i>Dossier d'inscription
                                        </h6>
                                        <small class="text-muted fw-semibold">
                                            <i class="fa-regular fa-calendar-days me-1"></i>
                                            <?= date('d/m/Y à H:i', strtotime($notif['created_at'])) ?>
                                        </small>
                                    </div>
                                    
                                    <p class="text-dark bg-light p-2 rounded small mb-3 text-break"><?= htmlspecialchars($notif['message']) ?></p>

                                    <?php if($etudiant_data): ?>
                                        <div class="fiche-inscription shadow-sm">
                                            <div class="fw-bold mb-3 text-dark d-flex flex-column flex-sm-row justify-content-between gap-2">
                                                <span><i class="fa-solid fa-id-card me-1"></i> FICHE D'INSCRIPTION REMPLIE</span>
                                                <span class="badge bg-warning text-dark align-self-start align-self-sm-center"><?= htmlspecialchars($etudiant_data['statut_inscription']) ?></span>
                                            </div>
                                            
                                            <div class="section-title">Identité de l'étudiant</div>
                                            <div class="row g-2">
                                                <div class="col-sm-6 text-break"><strong>Nom :</strong> <?= htmlspecialchars($etudiant_data['nom']) ?></div>
                                                <div class="col-sm-6 text-break"><strong>Prénom :</strong> <?= htmlspecialchars($etudiant_data['prenom']) ?></div>
                                                <div class="col-sm-6"><strong>Sexe :</strong> <?= htmlspecialchars($etudiant_data['sexe']) ?></div>
                                                <div class="col-sm-6 text-break"><strong>Né(e) le :</strong> <?= date('d/m/Y', strtotime($etudiant_data['date_naissance'])) ?> à <?= htmlspecialchars($etudiant_data['lieu_naissance']) ?></div>
                                                <div class="col-sm-6 text-break"><strong>Nationalité :</strong> <?= htmlspecialchars($etudiant_data['nationalite']) ?></div>
                                                <div class="col-sm-6 text-break"><strong>Email :</strong> <?= htmlspecialchars($etudiant_data['email']) ?></div>
                                                <div class="col-sm-6 text-break"><strong>Contact :</strong> <?= htmlspecialchars($etudiant_data['contact_etudiant']) ?></div>
                                                <div class="col-sm-6 text-break"><strong>Adresse :</strong> <?= htmlspecialchars($etudiant_data['adresse']) ?></div>
                                            </div>

                                            <div class="section-title">Cursus Académique Demandé</div>
                                            <div class="row g-2">
                                                <div class="col-sm-6 text-break"><strong>Filière :</strong> <?= htmlspecialchars($etudiant_data['nom_filiere']) ?></div>
                                                <div class="col-sm-6"><strong>Niveau d'entrée :</strong> <?= htmlspecialchars($etudiant_data['niveau']) ?></div>
                                                <div class="col-sm-6 text-break"><strong>Etablissement d'origine :</strong> <?= htmlspecialchars($etudiant_data['etablissement_origine'] ?? 'Néant') ?></div>
                                                <div class="col-sm-6 text-break"><strong>Série du BAC :</strong> <?= htmlspecialchars($etudiant_data['serie_diplome'] ?? '-') ?> (<?= htmlspecialchars($etudiant_data['points_diplome'] ?? '0') ?> pts)</div>
                                            </div>

                                            <div class="section-title">Contacts des Parents & Tuteurs</div>
                                            <div class="row g-2">
                                                <div class="col-sm-6 text-break"><strong>Père :</strong> <?= htmlspecialchars($etudiant_data['nom_prenom_pere'] ?? 'Néant') ?> (<?= htmlspecialchars($etudiant_data['telephone_pere'] ?? '-') ?>)</div>
                                                <div class="col-sm-6 text-break"><strong>Mère :</strong> <?= htmlspecialchars($etudiant_data['nom_prenom_mere'] ?? 'Néant') ?> (<?= htmlspecialchars($etudiant_data['telephone_mere'] ?? '-') ?>)</div>
                                                <div class="col-12 text-break"><strong>Tuteur légal :</strong> <?= htmlspecialchars($etudiant_data['tuteur_legal']) ?> | Tél: <?= htmlspecialchars($etudiant_data['telephone_tuteur']) ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-3 align-items-stretch align-items-sm-center">
                                        <?php if($is_unread): ?>
                                            <a href="req_action_notif.php?action=lu&id=<?= $notif['id_notification'] ?>" class="btn btn-sm btn-outline-secondary py-2 text-center" style="border-radius:8px;">Marquer lu</a>
                                        <?php elseif($notif['statut_lecture'] == 1): ?>
                                            <a href="req_action_notif.php?action=historique&id=<?= $notif['id_notification'] ?>" class="btn btn-sm btn-link text-muted small text-decoration-none text-center py-2">Déplacer à l'historique</a>
                                        <?php endif; ?>
                                        
                                        <?php if($etudiant_data && $etudiant_data['statut_inscription'] !== 'Validé définitivement'): ?>
                                            <form action="req_valider_etudiant.php" method="POST" class="d-block m-0">
                                                <input type="hidden" name="id_notification" value="<?= $notif['id_notification'] ?>">
                                                <input type="hidden" name="id_etudiants" value="<?= $etudiant_data['id_etudiants'] ?>">
                                                <button type="submit" class="btn btn-sm btn-dark-modern py-2 px-3 w-100 justify-content-center" style="font-size:0.85rem;">
                                                    <i class="fa-solid fa-user-check me-1"></i> VALIDER LA CANDIDATURE
                                                </button>
                                            </form>
                                        <?php elseif($etudiant_data): ?>
                                            <span class="text-success small fw-bold text-center py-2"><i class="fa-solid fa-circle-check"></i> Étudiant validé et visible</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>