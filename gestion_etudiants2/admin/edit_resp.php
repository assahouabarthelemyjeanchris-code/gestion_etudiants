<?php 
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $id = $_GET['id'];

    // Récupération du responsable
    $sql = "SELECT * FROM responsable_departement WHERE id_responsable = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $resp = $stmt->fetch();

    // Récupération de tous les départements pour la liste déroulante
    $departements = $conn->query("SELECT * FROM departement ORDER BY nom_departement ASC")->fetchAll();

    // Trouver le département actuel de ce responsable (s'il en a un)
    $stmt_current_dept = $conn->prepare("SELECT id_departement FROM departement WHERE id_responsable = ?");
    $stmt_current_dept->execute([$id]);
    $current_dept = $stmt_current_dept->fetch();
    $current_dept_id = $current_dept ? $current_dept['id_departement'] : null;

    if (!$resp) {
        header("Location: index.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $tel = $_POST['telephone'];
        $id_departement = $_POST['id_departement'];
        $uname = $_POST['uname'];

        try {
            $conn->beginTransaction();

            // 1. Mise à jour des informations personnelles
            $sql_update = "UPDATE responsable_departement SET nom=?, prenom=?, email=?, telephone=?, nomutilisateur=? WHERE id_responsable=?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([$nom, $prenom, $email, $tel, $uname, $id]);

            // 2. Retirer ce responsable de tout autre département
            $sql_remove = "UPDATE departement SET id_responsable = NULL WHERE id_responsable = ?";
            $stmt_remove = $conn->prepare($sql_remove);
            $stmt_remove->execute([$id]);

            // 3. Assigner ce responsable au nouveau département choisi
            $sql_assign = "UPDATE departement SET id_responsable = ? WHERE id_departement = ?";
            $stmt_assign = $conn->prepare($sql_assign);
            $stmt_assign->execute([$id, $id_departement]);

            $conn->commit();
            header("Location: index.php?menu=responsables&success=Mise à jour réussie");
            exit;
        } catch (PDOException $e) {
            $conn->rollBack();
            $error = "Erreur lors de la mise à jour.";
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Responsable</title>
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

    <div class="container card-custom p-5" style="max-width: 700px;">
        <div class="d-flex align-items-center mb-4">
            <a href="index.php?menu=responsables" class="btn btn-light rounded-circle me-3 border"><i class="fa-solid fa-arrow-left"></i></a>
            <h3 class="fw-bold mb-0">Modifier le profil Responsable</h3>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nom d'utilisateur</label>
                    <input type="text" name="uname" class="form-control" value="<?= htmlspecialchars($resp['nomutilisateur']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Adresse Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($resp['email']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nom</label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($resp['nom']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Prénom</label>
                    <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($resp['prenom']) ?>" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($resp['telephone']) ?>" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Département Assigné</label>
                    <select name="id_departement" class="form-select" required>
                        <option value="">-- Choisir un département --</option>
                        <?php foreach($departements as $d): ?>
                            <option value="<?= $d['id_departement'] ?>" <?= ($d['id_departement'] == $current_dept_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['nom_departement']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12 mt-4">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold text-white rounded-3 shadow-sm">
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>

</body>
</html>
<?php 
} else { header("Location: ../connexion.php"); exit; } ?>