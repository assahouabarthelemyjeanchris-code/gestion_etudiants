<?php 
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if (!isset($_GET['id'])) {
        header("Location: index.php?menu=departements");
        exit;
    }

    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM departement WHERE id_departement = ?");
    $stmt->execute([$id]);
    $dept = $stmt->fetch();

    if (!$dept) {
        header("Location: index.php?menu=departements");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom_departement = $_POST['nom_departement'];

        try {
            $sql_update = "UPDATE departement SET nom_departement = ? WHERE id_departement = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([$nom_departement, $id]);
            header("Location: index.php?menu=departements&success=Département mis à jour avec succès");
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de la mise à jour.";
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Département</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body { background-color: #fafefc; color: #090b3c; }
        .card-custom { background: #fffffe; border-radius: 20px; border: 1px solid #d9d6df; box-shadow: 0 4px 12px rgba(9,11,60,0.05); }
        .btn-primary { background: #a21c3b; border: none; }
        .btn-primary:hover { background: #391f20; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container card-custom p-5" style="max-width: 500px;">
        <div class="d-flex align-items-center mb-4">
            <a href="index.php?menu=departements" class="btn btn-light rounded-circle me-3 border"><i class="fa-solid fa-arrow-left"></i></a>
            <h3 class="fw-bold mb-0">Modifier le Département</h3>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="form-label fw-semibold">Nom du département</label>
                <input type="text" name="nom_departement" class="form-control" value="<?= htmlspecialchars($dept['nom_departement']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold text-white rounded-3 shadow-sm">
                Enregistrer les modifications
            </button>
        </form>
    </div>
</body>
</html>
<?php 
} else { header("Location: ../connexion.php"); exit; } ?>