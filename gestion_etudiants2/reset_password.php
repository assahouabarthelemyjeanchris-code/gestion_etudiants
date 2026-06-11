<?php 
include_once "DB_connexion.php";

$token = isset($_GET['token']) ? $_GET['token'] : ''; 
$display_name = "";
$role_title = ""; // Variable pour stocker le titre (Admin ou Responsable)

if (!empty($token)) {
    // Configuration des tables avec leur intitulé correspondant
    $tables = [
        'administrateur' => 'Administrateur',
        'responsable_departement' => 'Responsable'
    ];

    foreach ($tables as $table => $title) {
        // On vérifie si le jeton existe dans cette table
        $sql = "SELECT nom, prenom FROM $table WHERE reset_token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            $display_name = $user['prenom'] . " " . $user['nom'];
            $role_title = $title; // On récupère le titre (Administrateur ou Responsable)
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GDE | Réinitialisation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root { --accent: #6366f1; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
            background-image: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%);
            height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0;
        }
        .reset-card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            width: 100%; max-width: 400px;
            animation: fadeIn 0.6s ease-out;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Style amélioré pour le badge de rôle */
        .user-info {
            margin-bottom: 25px;
        }
        .role-badge {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: var(--accent);
            color: white;
            padding: 4px 12px;
            border-radius: 50px;
            font-weight: 800;
            display: inline-block;
            margin-bottom: 8px;
        }
        .user-name {
            display: block;
            color: #0f172a;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .btn-primary { background: #000; border: none; padding: 14px; border-radius: 12px; font-weight: 700; transition: 0.3s; }
        .btn-primary:hover { background: #1e293b; transform: translateY(-2px); }
        .form-control { border-radius: 12px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .cancel-link { color: #64748b; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: 0.2s; }
        .cancel-link:hover { color: #ef4444; }
    </style>
</head>
<body>

    <div class="reset-card text-center">
        <h3 class="fw-bold mb-3">Réinitialisation</h3>
        
        <?php if(empty($token) || empty($display_name)): ?>
            <div class="alert alert-danger small mt-3">
                <i class="fa-solid fa-triangle-exclamation"></i> Jeton invalide ou expiré.
            </div>
            <a href="connexion.php" class="btn btn-dark w-100 mt-2">Retour</a>
        <?php else: ?>
            <div class="user-info">
                <span class="role-badge"><?= $role_title ?></span>
                <span class="user-name"><?= htmlspecialchars($display_name) ?></span>
            </div>
            
            <p class="text-muted small mb-4">Veuillez définir votre nouveau mot de passe.</p>

            <form action="req/update_password_final.php" method="POST" class="text-start">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nouveau mot de passe</label>
                    <input type="password" name="new_pass" class="form-control" required minlength="6">
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Confirmer le mot de passe</label>
                    <input type="password" name="confirm_pass" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    Enregistrer le mot de passe
                </button>
            </form>
        <?php endif; ?>

        <div class="mt-2 border-top pt-3">
            <a href="connexion.php" class="cancel-link">
                <i class="fa-solid fa-arrow-left me-1"></i> Annuler et se connecter
            </a>
        </div>
    </div>

</body>
</html>