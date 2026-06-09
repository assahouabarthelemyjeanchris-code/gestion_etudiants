<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Récupération de mot de passe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); width: 100%; max-width: 400px; padding: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <h4 class="fw-bold text-center mb-3">Récupération</h4>
        <p class="text-muted small text-center">Entrez votre email pour recevoir un code de réinitialisation.</p>
        
        <form action="req/process_forgot.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Votre Email</label>
                <input type="email" name="email" class="form-control" required placeholder="exemple@domaine.com">
            </div>
            <button type="submit" class="btn btn-dark w-100 py-2 fw-bold" style="border-radius: 12px;">Envoyer le code</button>
        </form>
        <div class="text-center mt-3">
            <a href="connexion.php"><i class="fa-solid fa-arrow-left me-1"></i> Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>