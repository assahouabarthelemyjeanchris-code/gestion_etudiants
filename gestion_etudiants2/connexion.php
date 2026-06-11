<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail GDE | Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="icon" href="UIYA.jpg">
    <style>
        :root {
            --fond: #f8fafc;
            --fond-secondaire: #f1f5f9;
            --blanc: #ffffff;
            --primaire: #a21c3b;
            --primaire-hover: #82122c;
            --texte: #0f172a;
            --texte-secondaire: #64748b;
            --bordure: #e2e8f0;
            --ombre-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--fond);
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
        }
        
        .login-container {
            background: var(--blanc);
            border-radius: 8px;
            box-shadow: var(--ombre-md);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        
        .brand-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo {
            width: 60px;
            margin-bottom: 20px;
            border-radius: 15px;
        }
        
        .brand-section h3 {
            font-size: 1.8rem;
            color: var(--primaire);
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        
        .brand-section p {
            color: var(--texte-secondaire);
            font-size: 0.95rem;
            margin: 0;
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-floating {
            display: flex;
            flex-direction: column;
        }
        
        .form-floating > .form-control {
            padding: 12px;
            border: 1px solid var(--bordure);
            border-radius: 4px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background-color: var(--blanc);
            height: auto;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primaire);
            box-shadow: 0 0 0 3px rgba(162, 28, 59, 0.1);
            background-color: var(--blanc);
        }
        
        .role-switcher {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .role-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border: 1px solid var(--bordure);
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--fond);
        }
        
        .role-option input[type="radio"] {
            display: inline-block;
            cursor: pointer;
            width: 18px;
            height: 18px;
            accent-color: var(--primaire);
        }
        
        .role-option:hover {
            border-color: var(--primaire);
            background: var(--fond-secondaire);
        }
        
        .role-option input[type="radio"]:checked + label {
            color: var(--primaire);
            font-weight: 600;
            background: transparent;
            box-shadow: none;
        }
        
        .role-option label {
            display: inline-block;
            flex: 1;
            margin: 0;
            padding: 0;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--texte);
        }
        
        .password-wrapper { 
            position: relative; 
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--texte-secondaire);
            cursor: pointer;
            z-index: 10;
        }

        .btn-submit {
            padding: 12px 16px;
            background: var(--primaire);
            color: var(--blanc);
            border: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            width: 100%;
        }
        
        .btn-submit:hover {
            background: var(--primaire-hover);
            transform: translateY(-2px);
            box-shadow: var(--ombre-md);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .footer-links {
            text-align: center;
            font-size: 0.85rem;
            color: var(--texte-secondaire);
            margin-top: 20px;
        }

        .footer-links a {
            color: var(--texte-secondaire);
            text-decoration: none;
            transition: 0.2s;
        }

        .footer-links a:hover {
            color: var(--primaire);
        }

        .error-toast {
            background: #fff1f2;
            color: #e11d48;
            padding: 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: center;
            border: 1px solid #ffe4e6;
        }

        .success-toast {
            background: #f0fdf4;
            color: #16a34a;
            padding: 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: center;
            border: 1px solid #dcfce7;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 25px;
            }
            
            .brand-section h3 {
                font-size: 1.5rem;
            }
            
            .brand-section p {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="brand-section">
            <h3>Espace Connexion</h3>
            <p class="text-muted small">Connectez-vous pour gérer votre espace.</p>
        </div>

        <?php if (isset($_GET['error'])) { ?>
            <div class="error-toast">
                <i class="fa-solid fa-circle-exclamation me-2"></i> <?=$_GET['error']?>
            </div>
        <?php } ?>

        <?php if (isset($_GET['success'])) { ?>
            <div class="success-toast">
                <i class="fa-solid fa-circle-check me-2"></i> <?=$_GET['success']?>
            </div>
        <?php } ?>

        <form method="post" action="req/connexion.php">
            
            <div class="role-switcher">
                <div class="role-option">
                    <input type="radio" name="role" value="1" id="admin" checked>
                    <label for="admin">ADMINISTRATION</label>
                </div>
                <div class="role-option">
                    <input type="radio" name="role" value="2" id="resp">
                    <label for="resp">RESPONSABLE</label>
                </div>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="uname" id="uName" placeholder="Identifiant" required>
                <label for="uName">Identifiant</label>
            </div>

            <div class="password-wrapper mb-2">
                <div class="form-floating">
                    <input type="password" class="form-control" name="pass" id="passInput" placeholder="Mot de passe" required>
                    <label for="passInput">Mot de passe</label>
                </div>
                <i class="fa-solid fa-eye toggle-password" id="toggleIcon"></i>
            </div>

            <div class="text-end mb-4">
                <a href="forgot_password.php" class="text-decoration-none small fw-bold" style="color: var(--primaire); font-size: 0.75rem;">
                    Mot de passe oublié ?
                </a>
            </div>

            <button type="submit" class="btn-submit">
                Se connecter
            </button>

            <div class="footer-links">
                <a href="index.php"><i class="fa-solid fa-arrow-left me-1"></i> Retour à l'accueil</a>
            </div>
        </form>
    </div>

    <script>
        const passInput = document.getElementById('passInput');
        const toggleIcon = document.getElementById('toggleIcon');

        toggleIcon.addEventListener('click', () => {
            const isPass = passInput.type === 'password';
            passInput.type = isPass ? 'text' : 'password';
            toggleIcon.className = isPass ? 'fa-solid fa-eye-slash toggle-password' : 'fa-solid fa-eye toggle-password';
        });

        document.querySelector('form').onsubmit = function() {
            const btn = document.querySelector('.btn-submit');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Vérification...';
            btn.style.opacity = '0.7';
        };
    </script>
</body>
</html>