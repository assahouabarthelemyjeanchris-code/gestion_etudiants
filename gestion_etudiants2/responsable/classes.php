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

$id_responsable = $_SESSION['user_id'];

// Récupération de l'année universitaire active depuis la configuration globale
try {
    $annee_active = $conn->query("SELECT valeur FROM configuration_gde WHERE cle = 'annee_universitaire_active'")->fetchColumn();
    if (!$annee_active) {
        $annee_active = date('Y') . "-" . (date('Y') + 1);
    }
} catch (Exception $e) {
    $annee_active = date('Y') . "-" . (date('Y') + 1);
}

// Récupération des filtres depuis l'interface (formulaire GET)
$filtre_filiere = isset($_GET['filiere']) ? intval($_GET['filiere']) : 0;
$filtre_niveau  = isset($_GET['niveau']) ? trim($_GET['niveau']) : '';
// Si aucune année n'est explicitement demandée en GET, on applique par défaut l'année active
$filtre_annee   = isset($_GET['annee']) ? trim($_GET['annee']) : $annee_active;

try {
    // A. Récupérer toutes les filières rattachées au département de ce responsable pour le formulaire
    $sql_filieres_resp = "SELECT f.id_filiere, f.nom_filiere, f.id_departement
                          FROM filiere f
                          JOIN departement d ON f.id_departement = d.id_departement
                          WHERE d.id_responsable = ?
                          ORDER BY f.nom_filiere ASC";
    $stmt_f = $conn->prepare($sql_filieres_resp);
    $stmt_f->execute([$id_responsable]);
    $liste_filieres = $stmt_f->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des IDs des filières gérées pour sécuriser les requêtes suivantes
    $ids_filieres_gerees = array_column($liste_filieres, 'id_filiere');

    // B. Récupérer la liste distincte des années académiques existantes dans le département pour le filtre
    $liste_annees = [];
    if (!empty($ids_filieres_gerees)) {
        $in_clause = implode(',', array_fill(0, count($ids_filieres_gerees), '?'));
        $sql_annees = "SELECT DISTINCT i.annee_academique 
                       FROM inscription i
                       JOIN etudiants e ON i.id_etudiants = e.id_etudiants
                       WHERE e.id_filiere IN ($in_clause) AND i.annee_academique IS NOT NULL
                       ORDER BY i.annee_academique DESC";
        $stmt_a = $conn->prepare($sql_annees);
        $stmt_a->execute($ids_filieres_gerees);
        $liste_annees = $stmt_a->fetchAll(PDO::FETCH_ASSOC);
    }

    // C. Construire la requête d'affichage des étudiants validés de son département
    $sql_etud = "SELECT e.id_etudiants, e.nom, e.prenom, e.niveau, f.nom_filiere, i.annee_academique 
                 FROM etudiants e
                 JOIN filiere f ON e.id_filiere = f.id_filiere
                 JOIN departement d ON f.id_departement = d.id_departement
                 LEFT JOIN inscription i ON e.id_etudiants = i.id_etudiants
                 WHERE d.id_responsable = ? AND e.statut_inscription = 'Validé définitivement'";

    $params = [$id_responsable];

    if ($filtre_filiere > 0) {
        $sql_etud .= " AND e.id_filiere = ?";
        $params[] = $filtre_filiere;
    }
    if (!empty($filtre_niveau)) {
        $sql_etud .= " AND e.niveau = ?";
        $params[] = $filtre_niveau;
    }
    if (!empty($filtre_annee)) {
        $sql_etud .= " AND i.annee_academique = ?";
        $params[] = $filtre_annee;
    }

    $sql_etud .= " ORDER BY f.nom_filiere ASC, e.niveau ASC, e.nom ASC, e.prenom ASC";
    $stmt_e = $conn->prepare($sql_etud);
    $stmt_e->execute($params);
    $etudiants = $stmt_e->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Classes | Espace Responsable</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
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

        .search-container {
            max-width: 350px;
            position: relative;
        }
        .search-container input {
            padding-left: 40px;
            border-radius: 10px;
            border: 1px solid var(--bordure);
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
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h1 class="fw-bold h3 mb-1">Listes des Classes Émises</h1>
                <p class="text-muted">Espace de suivi et d'édition des listes de classe par année universitaire</p>
            </div>
            <div>
                <a href="index.php" class="btn btn-outline-secondary px-4 shadow-sm">
                    <i class="fa-solid fa-house me-2"></i>Accueil Dashboard
                </a>
            </div>
        </div>

        <form method="GET" action="" class="row g-3 mb-4 bg-white p-3 rounded-4 border shadow-sm">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Filtrer par Filière :</label>
                <select name="filiere" class="form-select" style="border-radius: 10px;">
                    <option value="0">Toutes vos filières</option>
                    <?php foreach($liste_filieres as $lf): ?>
                        <option value="<?= $lf['id_filiere'] ?>" <?= $filtre_filiere == $lf['id_filiere'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lf['nom_filiere']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label small fw-bold">Filtrer par Niveau :</label>
                <select name="niveau" class="form-select" style="border-radius: 10px;">
                    <option value="">Tous les niveaux</option>
                    <option value="L1" <?= $filtre_niveau === 'L1' ? 'selected' : '' ?>>L1</option>
                    <option value="L2" <?= $filtre_niveau === 'L2' ? 'selected' : '' ?>>L2</option>
                    <option value="L3" <?= $filtre_niveau === 'L3' ? 'selected' : '' ?>>L3</option>
                    <option value="M1" <?= $filtre_niveau === 'M1' ? 'selected' : '' ?>>M1</option>
                    <option value="M2" <?= $filtre_niveau === 'M2' ? 'selected' : '' ?>>M2</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">Année Universitaire :</label>
                <select name="annee" class="form-select" style="border-radius: 10px;">
                    <option value="">Toutes les années</option>
                    <?php foreach($liste_annees as $la): ?>
                        <option value="<?= htmlspecialchars($la['annee_academique']) ?>" <?= $filtre_annee === $la['annee_academique'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($la['annee_academique']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" style="background-color: var(--primaire); border: none; border-radius: 10px;">
                    <i class="fa-solid fa-filter me-1"></i> Appliquer
                </button>
            </div>
        </form>

        <div class="white-card">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <div class="search-container w-100">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="studentSearch" class="form-control" placeholder="Rechercher sur cette liste (Nom, Prénom...)" onkeyup="filtrerEtudiants()">
                </div>
                
                <button onclick="imprimerListeActive()" class="btn btn-print px-4 py-2 shadow-sm">
                    <i class="fa-solid fa-print me-2"></i>Imprimer cette liste de classe
                </button>
            </div>

            <?php if (!empty($etudiants)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle student-table">
                    <thead>
                        <tr>
                            <th># ID</th>
                            <th>Nom & Prénoms</th>
                            <th>Filière</th>
                            <th>Niveau</th>
                            <th>Année Académique</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($etudiants as $e): ?>
                            <tr class="student-row">
                                <td class="text-muted fw-bold student-id">#<?= $e['id_etudiants'] ?></td>
                                <td class="fw-semibold student-name">
                                    <span class="text-uppercase"><?= htmlspecialchars($e['nom']) ?></span> 
                                    <span class="text-capitalize text-muted"><?= htmlspecialchars($e['prenom']) ?></span>
                                </td>
                                <td><small class="badge bg-light text-dark border"><?= htmlspecialchars($e['nom_filiere']) ?></small></td>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($e['niveau']) ?></td>
                                <td><?= htmlspecialchars($e['annee_academique'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <tr id="no-result-row" style="display: none;">
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-circle-exclamation me-2"></i>Aucun étudiant ne correspond à cette recherche.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="py-5 text-center text-muted">
                    <i class="fa-solid fa-folder-open fa-3x mb-3"></i>
                    <p class="h5">Aucun étudiant validé ne correspond aux critères sélectionnés.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Moteur d'impression dynamique envoyant les variables d'URL GET actives
    function imprimerListeActive() {
        const filiere = "<?= urlencode($filtre_filiere) ?>";
        const niveau = "<?= urlencode($filtre_niveau) ?>";
        const annee = "<?= urlencode($filtre_annee) ?>";
        
        const url = `imprimer_liste.php?filiere=${filiere}&niveau=${niveau}&annee=${annee}`;
        window.open(url, '_blank');
    }

    // Filtrage dynamique côté client pour affinage instantané
    function filtrerEtudiants() {
        const input = document.getElementById('studentSearch');
        const filter = input.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.student-row');
        const noResultRow = document.getElementById('no-result-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const nameText = row.querySelector('.student-name').innerText.toLowerCase();
            const idText = row.querySelector('.student-id').innerText.toLowerCase();

            if (nameText.includes(filter) || idText.includes(filter)) {
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
    </script>
</body>
</html>