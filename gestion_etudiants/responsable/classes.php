<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// 1. Vérification de l'accès (Responsable uniquement)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Responsable') {
    header("Location: ../connexion.php?error=Veuillez vous connecter");
    exit;
}

require_once "../DB_connexion.php";

// Récupération de la filière du responsable stockée en session
$filiere_nom = $_SESSION['filiere'] ?? null;

if (!$filiere_nom) {
    die("Erreur : Aucune filière n'est associée à votre compte responsable.");
}

try {
    // 2. Récupérer les niveaux distincts pour cette filière
    // Correction de la ligne 29 : Utilisation de -> au lieu de .
    $sql_niveaux = "SELECT DISTINCT e.niveau 
                    FROM etudiants e
                    JOIN filiere f ON e.id_filiere = f.id_filiere
                    WHERE f.nom_filiere = ? 
                    ORDER BY e.niveau ASC";
    $stmt_n = $conn->prepare($sql_niveaux);
    $stmt_n->execute([$filiere_nom]); // CORRIGÉ : -> à la place de .
    $niveaux = $stmt_n->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listes des Classes | <?= htmlspecialchars($filiere_nom) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
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
        
        body { background-color: var(--fond); color: var(--texte); font-family: 'Inter', sans-serif; }
        
        .text-muted { color: var(--texte-secondaire) !important; }
        .text-dark { color: var(--texte) !important; }
        
        .white-card { 
            background: var(--blanc); 
            border-radius: 20px; 
            padding: 25px; 
            border: 1px solid var(--bordure); 
            box-shadow: var(--ombre); 
        }
        
        .nav-pills .nav-link { 
            color: var(--texte-secondaire); 
            font-weight: 600; 
            border-radius: 10px; 
            margin-right: 8px; 
            border: 1px solid transparent; 
            transition: 0.3s; 
        }
        .nav-pills .nav-link:hover {
            background-color: var(--fond-secondaire);
            color: var(--primaire);
        }
        .nav-pills .nav-link.active { 
            background-color: var(--primaire); 
            color: var(--blanc); 
            box-shadow: 0 4px 12px rgba(162, 28, 59, 0.2); 
        }
        
        .border-bottom { border-bottom: 1px solid var(--bordure) !important; }
        
        .table thead th { 
            background: var(--fond-secondaire); 
            color: var(--texte-secondaire); 
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 0.75rem; 
            border: none; 
            padding: 15px; 
        }
        .table tbody td {
            color: var(--texte);
            border-bottom: 1px solid var(--bordure);
        }
        
        .btn-outline-secondary {
            color: var(--texte-secondaire);
            border-color: var(--bordure);
            background-color: var(--blanc);
        }
        .btn-outline-secondary:hover {
            background-color: var(--fond-secondaire);
            color: var(--texte);
            border-color: var(--secondaire);
        }
        
        .btn-print { 
            background: var(--fond-secondaire); 
            color: var(--primaire); 
            border: 1px solid var(--bordure); 
            border-radius: 10px; 
            font-weight: 600; 
            transition: 0.2s all;
        }
        .btn-print:hover { 
            background: var(--primaire); 
            color: var(--blanc); 
            border-color: var(--primaire);
        }
        
        .alert-light {
            background-color: var(--blanc);
            border-color: var(--bordure) !important;
            color: var(--texte-secondaire);
        }

        /* Barre de recherche personnalisée */
        .search-container {
            max-width: 400px;
            position: relative;
        }
        .search-container input {
            padding-left: 40px;
            border-radius: 10px;
            border: 1px solid var(--bordure);
            color: var(--texte);
        }
        .search-container input:focus {
            border-color: var(--primaire);
            box-shadow: 0 0 0 0.25rem rgba(162, 28, 59, 0.15);
        }
        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondaire);
        }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container-fluid" style="max-width: 1200px;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
            <div>
                <h1 class="fw-bold h3 mb-1">Listes des étudiants</h1>
                <p class="text-muted">Filière : <span class="fw-bold text-dark"><?= htmlspecialchars($filiere_nom) ?></span></p>
            </div>
            
            <div class="d-flex flex-column flex-sm-row gap-3 mt-3 mt-md-0 align-items-sm-center">
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="studentSearch" class="form-control" placeholder="Rechercher un étudiant (Nom, ID)..." onkeyup="filtrerEtudiants()">
                </div>
                <a href="index.php" class="btn btn-outline-secondary px-4 shadow-sm">
                    <i class="fa-solid fa-house me-2"></i>Accueil
                </a>
            </div>
        </div>

        <?php if (!empty($niveaux)): ?>
        <div class="white-card">
            <ul class="nav nav-pills mb-4 border-bottom pb-3" id="pills-tab" role="tablist">
                <?php foreach ($niveaux as $key => $n): ?>
                <li class="nav-item">
                    <button class="nav-link <?= $key === 0 ? 'active' : '' ?>" 
                            id="tab-<?= $key ?>" 
                            data-bs-toggle="pill" 
                            data-bs-target="#content-<?= $key ?>" 
                            type="button"
                            onclick="clearSearch()">
                        Niveau <?= htmlspecialchars($n['niveau']) ?>
                    </button>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <?php foreach ($niveaux as $key => $n): ?>
                <div class="tab-pane fade <?= $key === 0 ? 'show active' : '' ?>" 
                     id="content-<?= $key ?>" 
                     role="tabpanel" 
                     aria-labelledby="tab-<?= $key ?>">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Classe : <?= htmlspecialchars($n['niveau']) ?></h5>
                        <button onclick="imprimerListe('<?= addslashes($n['niveau']) ?>', '<?= addslashes($filiere_nom) ?>')" 
                                class="btn btn-print">
                            <i class="fa-solid fa-print me-2"></i>Imprimer la liste
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle student-table">
                            <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Année Académique</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // On récupère les étudiants pour ce niveau spécifique
                                $sql_e = "SELECT e.id_etudiants, e.nom, e.prenom, i.annee_academique 
                                          FROM etudiants e
                                          INNER JOIN inscription i ON e.id_etudiants = i.id_etudiants
                                          INNER JOIN filiere f ON e.id_filiere = f.id_filiere
                                          WHERE f.nom_filiere = ? AND e.niveau = ?
                                          ORDER BY e.nom ASC";
                                $stmt_e = $conn->prepare($sql_e);
                                $stmt_e->execute([$filiere_nom, $n['niveau']]); // CORRIGÉ : -> à la place de .
                                while($row = $stmt_e->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                    <tr class="student-row">
                                        <td class="text-muted fw-bold student-id">#<?= $row['id_etudiants'] ?></td>
                                        <td class="text-uppercase fw-semibold student-name"><?= htmlspecialchars($row['nom']) ?></td>
                                        <td class="text-capitalize student-firstname"><?= htmlspecialchars($row['prenom']) ?></td>
                                        <td><?= htmlspecialchars($row['annee_academique']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr class="no-result-row" style="display: none;">
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i>Aucun étudiant ne correspond à cette recherche.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-light border p-5 text-center shadow-sm rounded-4">
                <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="h5 text-muted">Aucun étudiant inscrit trouvé pour cette filière.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // Liaison directe de l'impression vers imprimer_liste.php
    function imprimerListe(niveau, filiere) {
        const url = `imprimer_liste.php?niveau=${encodeURIComponent(niveau)}`;
        window.open(url, '_blank');
    }

    // Moteur de filtrage/recherche dynamique côté client
    function filtrerEtudiants() {
        const input = document.getElementById('studentSearch');
        const filter = input.value.toLowerCase().trim();
        const activePane = document.querySelector('.tab-pane.active');
        
        if (!activePane) return;
        
        const rows = activePane.querySelectorAll('.student-row');
        const noResultRow = activePane.querySelector('.no-result-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const idText = row.querySelector('.student-id').innerText.toLowerCase();
            const nameText = row.querySelector('.student-name').innerText.toLowerCase();
            const firstnameText = row.querySelector('.student-firstname').innerText.toLowerCase();

            if (idText.includes(filter) || nameText.includes(filter) || firstnameText.includes(filter)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noResultRow) {
            noResultRow.style.display = (visibleCount === 0 && filter !== '') ? '' : 'none';
        }
    }

    // Réinitialise la barre lors d'un changement d'onglet (de classe)
    function clearSearch() {
        const input = document.getElementById('studentSearch');
        input.value = '';
        const panes = document.querySelectorAll('.tab-pane');
        panes.forEach(pane => {
            const rows = pane.querySelectorAll('.student-row');
            rows.forEach(row => row.style.display = '');
            const noResultRow = pane.querySelector('.no-result-row');
            if (noResultRow) noResultRow.style.display = 'none';
        });
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>