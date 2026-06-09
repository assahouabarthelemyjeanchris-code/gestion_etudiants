<?php 
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $id = $_GET['id'];

    $sql = "SELECT * FROM responsablefiliere WHERE id_responsablefiliere = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $resp = $stmt->fetch();

    $filieres = $conn->query("SELECT nom_filiere FROM filiere ORDER BY nom_filiere ASC")->fetchAll();

    if (!$resp) {
        header("Location: index.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $tel = $_POST['telephone'];
        $filiere = $_POST['filiere'];
        $uname = $_POST['uname'];

        $sql_update = "UPDATE responsablefiliere SET nom=?, prenom=?, email=?, telephone=?, filiere=?, nomutilisateur=? WHERE id_responsablefiliere=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$nom, $prenom, $email, $tel, $filiere, $uname, $id]);

        header("Location: index.php?menu=responsables&success=Modifications enregistrées");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Responsable | GDE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary: #4f46e5; 
            --bg: #f8fafc; 
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg); 
            color: var(--text-main); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh;
            margin: 0;
        }

        .edit-card { 
            background: var(--card-bg); 
            border: 1px solid var(--border); 
            border-radius: 24px; 
            padding: 40px; 
            width: 100%; 
            max-width: 600px; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .form-control, .form-select { 
            background: #ffffff; 
            border: 1.5px solid var(--border); 
            color: var(--text-main); 
            border-radius: 12px; 
            padding: 12px 16px;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus { 
            background: #fff; 
            border-color: var(--primary); 
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); 
            outline: none;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            letter-spacing: -0.01em;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .back-link {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding-left: 0;
        }
    </style>
</head>
<body>

    <div class="edit-card">
        <div class="d-flex align-items-center mb-5">
            <a href="index.php?menu=responsables" class="back-link me-3">
                <i class="fa-solid fa-arrow-left-long fa-xl"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-0" style="letter-spacing: -1px;">Modifier le profil</h3>
                <p class="text-muted small mb-0">Mise à jour des accès de la filière</p>
            </div>
        </div>

        <form method="POST">
            <div class="row g-4">
                <div class="col-md-12">
                    <label class="form-label">Nom d'utilisateur</label>
                    <div class="input-group">
                        <span class="input-group-text">@</span>
                        <input type="text" name="uname" class="form-control" value="<?= htmlspecialchars($resp['nomutilisateur']) ?>" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($resp['nom']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($resp['prenom']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email professionnel</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($resp['email']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($resp['telephone']) ?>" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Filière assignée</label>
                    <select name="filiere" class="form-select" required>
                        <?php foreach($filieres as $f): ?>
                            <option value="<?= $f['nom_filiere'] ?>" <?= ($f['nom_filiere'] == $resp['filiere']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['nom_filiere']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12 mt-5">
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        Enregistrer les modifications
                    </button>
                    <div class="text-center">
                        <a href="index.php?menu=responsables" class="text-muted small text-decoration-none">
                            <i class="fa-solid fa-xmark me-1"></i> Annuler et revenir
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

</body>
</html>
<?php 
} else { header("Location: ../connexion.php"); exit; } ?>