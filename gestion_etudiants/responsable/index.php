<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Responsable') {
    header("Location: ../connexion.php?error=Veuillez vous connecter");
    exit;
}

include_once "../DB_connexion.php";

$prenom  = $_SESSION['prenom'] ?? 'Responsable';
$nom     = $_SESSION['nom']    ?? '';
$filiere = $_SESSION['filiere'] ?? 'Non définie';

$stats_niveaux = [];
try {
    $sql_stats = "SELECT niveau, COUNT(*) as total 
                  FROM etudiants 
                  WHERE id_filiere = (SELECT id_filiere FROM filieres WHERE nom_filiere = ? LIMIT 1)
                  GROUP BY niveau
                  ORDER BY niveau ASC";
    $stmt_s = $conn->prepare($sql_stats);
    $stmt_s->execute([$filiere]);
    $stats_niveaux = $stmt_s->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $stats_niveaux = [];
}
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
            background-color: var(--fond);
            color: var(--texte);
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.022em;
            -webkit-font-smoothing: antialiased;
        }

        .navbar-minimal {
            background-color: var(--blanc);
            border-bottom: 1px solid var(--bordure);
            padding: 1.25rem 0;
        }

        .navbar-minimal .text-primary {
            color: var(--primaire) !important;
        }

        .glass-card {
            background: var(--blanc);
            border: 1px solid var(--bordure);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: var(--ombre-md);
            transition: all 0.3s ease;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--bordure);
            color: var(--texte);
        }
        .stat-item:last-child { border: 0; }
        
        .stat-item .text-muted {
            color: var(--texte-secondaire) !important;
        }

        .btn-dark-modern {
            background: var(--primaire);
            color: var(--blanc);
            padding: 1rem 2rem;
            border-radius: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--ombre);
            transition: 0.2s;
        }
        .btn-dark-modern:hover {
            background: var(--primaire-hover);
            color: var(--blanc);
            transform: translateY(-2px);
            box-shadow: var(--ombre-md);
        }

        .profile-link {
            background: var(--fond-secondaire);
            padding: 8px 16px;
            border-radius: 100px;
            border: 1px solid var(--bordure);
            text-decoration: none;
            color: var(--texte-secondaire);
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        .profile-link:hover {
            background: var(--blanc);
            color: var(--primaire);
            border-color: var(--primaire);
        }

        .tag-filiere {
            color: var(--primaire);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .text-muted {
            color: var(--texte-secondaire) !important;
        }

        .text-success {
            color: var(--secondaire) !important;
        }

        .text-primary {
            color: var(--primaire) !important;
        }

        .border-top {
            border-top: 1px solid var(--bordure) !important;
        }
    </style>
</head>
<body>

    <nav class="navbar-minimal">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="fw-800 fs-4">Espace<span class="text-primary"> Responsable</span></div>
            
            <div class="d-flex align-items-center gap-4">
                <a href="profil.php" class="profile-link">
                    <i class="fa-regular fa-user-circle me-1"></i>
                    <?= htmlspecialchars($prenom) ?>
                </a>
                <a href="../Deconnexion.php" class="text-muted text-decoration-none small fw-medium">
                    Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <main class="container py-5 mt-lg-4">
        <div class="row g-5">
            
            <div class="col-lg-4">
                <div class="mb-5">
                    <span class="tag-filiere">Département</span>
                    <h2 class="fw-800 tracking-tight mt-2"><?= htmlspecialchars($filiere) ?></h2>
                </div>
                
                <div class="pe-lg-4">
                    <p class="small text-muted fw-bold text-uppercase mb-2"></p>
                    <div class="stats-list">
                        <?php foreach($stats_niveaux as $stat): ?>
                            <div class="stat-item">
                                <span class="text-muted fw-medium"><?= htmlspecialchars($stat['niveau']) ?></span>
                                <span class="fw-bold"><?= $stat['total'] ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if(empty($stats_niveaux)): ?>
                            <p class="small text-muted italic"></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass-card h-100">
                    <div class="mb-5">
                        <h1 class="display-5 fw-800 mb-3">Bonjour, <?= htmlspecialchars($prenom) ?>.</h1>
                        <p class="text-muted fs-5 lh-base" style="max-width: 500px;">
                            Bienvenue sur votre tableau de bord. Tout est à jour pour la gestion de votre département académique.
                        </p>
                    </div>
                    
                    <div class="py-3">
                        <a href="classes.php" class="btn-dark-modern shadow-sm">
                            Ouvrir la gestion des étudiants
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </a>
                    </div>

                    <div class="mt-5 pt-5 row g-4 border-top">
                        <div class="col-md-6">
                            <div class="d-flex gap-2 align-items-center">
                                <i class="fa-solid fa-check-circle text-success small"></i>
                                <span class="small fw-bold text-muted">Système à jour</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 align-items-center">
                                <i class="fa-solid fa-shield-halved text-primary small"></i>
                                <span class="small fw-bold text-muted">Accès Responsable sécurisé</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>