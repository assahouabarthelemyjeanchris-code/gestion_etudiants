<?php 
session_start();
require_once "../DB_connexion.php";

// Vérification de la session (on utilise l'ID du responsable)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../connexion.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_username     = trim($_POST['username']);
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Récupération du mot de passe actuel (Notez le nom de la colonne : motdepasse)
    $stmt = $conn->prepare("SELECT motdepasse FROM responsable_departement WHERE id_responsable = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user && password_verify($current_password, $user['motdepasse'])) {
        
        // 2. Mise à jour du nom d'utilisateur (nomutilisateur)
        if (!empty($new_username)) {
            $upd = $conn->prepare("UPDATE responsable_departement SET nomutilisateur = ? WHERE id_responsable = ?");
            $upd->execute([$new_username, $user_id]);
            $_SESSION['username'] = $new_username; // Mise à jour de la session
            $success = "Identifiants mis à jour.";
        }

        // 3. Mise à jour du mot de passe si rempli
        if (!empty($new_password)) {
            if ($new_password === $confirm_password) {
                // Hachage sécurisé
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE responsable_departement SET motdepasse = ? WHERE id_responsable = ?");
                $upd->execute([$hashed, $user_id]);
                $success = "Modifications enregistrées avec succès.";
            } else {
                $error = "Les nouveaux mots de passe ne correspondent pas.";
            }
        }
    } else {
        $error = "Le mot de passe actuel est incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sécurité | GDE Portal</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1a202c;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 30px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #718096;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: white;
            border-color: #3182ce;
            box-shadow: 0 0 0 4px rgba(49, 130, 206, 0.1);
        }

        .password-group { position: relative; }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #a0aec0;
            z-index: 10;
        }

        .btn-update {
            background: #1a202c;
            color: white;
            border: none;
            padding: 0.9rem;
            border-radius: 12px;
            font-weight: 700;
            margin-top: 1rem;
            transition: 0.3s;
        }

        .btn-update:hover {
            background: #000;
            transform: translateY(-2px);
        }

        .icon-box {
            width: 50px; height: 50px;
            background: white;
            border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            color: #3182ce;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="container" style="max-width: 480px;">
    <div class="glass-card">
        
        <div class="text-center mb-4">
            <div class="icon-box">
                <i class="fa-solid fa-shield-halved fs-4"></i>
            </div>
            <h3 class="fw-800 mb-1">Sécurité</h3>
            <p class="small text-muted">Modifier vos accès de Responsable</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger small rounded-3"><?= $error ?></div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success small rounded-3"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nom d'utilisateur actuel</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 rounded-start-3"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" name="username" class="form-control rounded-end-3" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" required>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <div class="mb-3">
                <label class="form-label">Mot de passe actuel</label>
                <div class="password-group">
                    <input type="password" name="current_password" class="form-control pass-input" placeholder="Confirmation requise" required>
                    <i class="fa-regular fa-eye toggle-password"></i>
                </div>
            </div>

            <div class="row g-2 mb-4">
                <div class="col-6">
                    <label class="form-label">Nouveau mot de passe</label>
                    <div class="password-group">
                        <input type="password" name="new_password" class="form-control pass-input" placeholder="Optionnel">
                        <i class="fa-regular fa-eye toggle-password"></i>
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label">Confirmer mot de passe</label>
                    <div class="password-group">
                        <input type="password" name="confirm_password" class="form-control pass-input" placeholder="Optionnel">
                        <i class="fa-regular fa-eye toggle-password"></i>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-update w-100 mb-3">
                Enregistrer les changements
            </button>
            
            <div class="text-center">
                <a href="index.php" class="text-decoration-none text-muted small fw-bold">
                    <i class="fa-solid fa-chevron-left me-1"></i> Annuler et retourner
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Script d'affichage/masquage des mots de passe
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.pass-input');
            if (input.type === "password") {
                input.type = "text";
                this.classList.replace('fa-regular', 'fa-solid'); // Change l'icône
                this.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = "password";
                this.classList.replace('fa-solid', 'fa-regular');
                this.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
</script>

</body>
</html>