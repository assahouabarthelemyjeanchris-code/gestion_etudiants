<?php include "DB_connexion.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GDE | Excellence Académique</title>
    <link rel="icon" href="UIYA.jpg">
    <style>
        /* STYLES SPECIFIQUES AU SITE DE PRESENTATION */
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: var(--texte);
            background: var(--fond);
        }

        :root {
            --blanc: #ffffff;
            --texte: #1e293b;
            --texte-secondaire: #64748b;
            --fond: #f8fafc;
            --fond-secondaire: #f1f5f9;
            --bordure: #e2e8f0;
            --ombre: 0 1px 3px rgba(0,0,0,0.05);
            --ombre-md: 0 4px 6px -1px rgba(0,0,0,0.05);
            --primaire: #a21c3b;
            --primaire-hover: #85142f;
        }

        /* HEADER PRESENTATION */
        .header-presentation {
            position: sticky;
            top: 0;
            z-index: 200;
            background: var(--blanc);
            border-bottom: 1px solid var(--bordure);
            box-shadow: var(--ombre);
        }

        .header-presentation-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            height: 70px;
        }

        .header-logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-logo-img {
            height: 50px;
            width: auto;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 40px;
            flex: 1;
            margin-left: 50px;
        }

        .header-nav a {
            text-decoration: none;
            color: var(--texte);
            font-weight: 500;
            transition: color 0.3s ease;
            font-size: 0.95rem;
        }

        .header-nav a:hover {
            color: var(--primaire);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .header-phone {
            color: var(--texte);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
            white-space: nowrap;
        }

        .header-phone:hover {
            color: var(--primaire);
        }

        .btn-login {
            background: var(--primaire);
            color: var(--blanc);
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-login:hover {
            background: var(--primaire-hover);
            transform: translateY(-2px);
            box-shadow: var(--ombre-md);
        }

        .footer-section .btn-login {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 24px;
            font-size: 0.95rem;
        }

        /* HERO SECTION */
        .hero {
            background: #a21c3b;
            color: var(--blanc);
            padding: 100px 30px;
            text-align: center;
        }

        .hero-content {
            max-width: 900px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3rem;
            margin: 0 0 20px 0;
            font-weight: 700;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.3rem;
            margin: 0;
            font-weight: 300;
            opacity: 0.95;
        }

        /* LOGO UNIVERSITE SECTION */
        .universite-section {
            background: var(--fond);
            padding: 80px 30px;
            text-align: center;
        }

        .universite-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .universite-title {
            color: var(--texte);
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 15px 0;
        }

        .universite-subtitle {
            color: var(--texte-secondaire);
            font-size: 1.1rem;
            margin: 0;
            font-weight: 500;
        }

        /* DIPLOMES SECTION */
        .diplomes-section {
            background: var(--blanc);
            padding: 80px 30px;
        }

        .diplomes-title {
            text-align: center;
            font-size: 2rem;
            color: var(--texte);
            margin: 0 0 60px 0;
            font-weight: 700;
        }

        .diplomes-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .diplome-card {
            background: var(--fond);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            box-sizing: border-box;
        }

        .diplome-card:hover {
            border-color: var(--primaire);
            transform: translateY(-5px);
            box-shadow: var(--ombre-md);
        }

        .diplome-icon {
            width: 60px;
            height: 60px;
            background: var(--primaire);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.8rem;
        }

        .diplome-card h3 {
            color: var(--texte);
            font-size: 1.3rem;
            margin: 0 0 15px 0;
            font-weight: 600;
        }

        .diplome-card p {
            color: var(--texte-secondaire);
            margin: 0;
            line-height: 1.6;
        }

        /* EXCELLENCE SECTION */
        .excellence-section {
            background: var(--fond-secondaire);
            padding: 80px 30px;
        }

        .excellence-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .excellence-title {
            color: var(--texte);
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 30px 0;
        }

        .excellence-text {
            color: var(--texte-secondaire);
            font-size: 1rem;
            line-height: 1.8;
            margin: 0;
        }

        /* SERVICES SECTION */
        .services-section {
            background: var(--blanc);
            padding: 80px 30px;
        }

        .services-title {
            text-align: center;
            font-size: 2rem;
            color: var(--texte);
            margin: 0 0 60px 0;
            font-weight: 700;
        }

        .services-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .service-card {
            background: var(--fond);
            border-radius: 12px;
            padding: 30px;
            border-left: 4px solid var(--primaire);
            box-sizing: border-box;
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-2px);
        }

        .service-card h4 {
            color: var(--texte);
            margin: 0 0 10px 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .service-card p {
            color: var(--texte-secondaire);
            margin: 0;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* CONTACT FORM SECTION ADAPTATION */
        .contact-section {
            background: var(--fond-secondaire);
            padding: 80px 30px;
        }

        .contact-content {
            max-width: 1000px;
            margin: 0 auto;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
            margin-top: 40px;
        }

        .contact-info-block {
            background: var(--blanc);
            border-radius: 12px;
            padding: 40px;
            border: 1px solid var(--bordure);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .contact-info-item {
            margin-bottom: 30px;
        }

        .contact-info-item:last-child {
            margin-bottom: 0;
        }

        .contact-info-item h5 {
            color: var(--primaire);
            margin: 0 0 10px 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .contact-info-item p {
            color: var(--texte-secondaire);
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .contact-form-block {
            background: var(--blanc);
            border-radius: 12px;
            padding: 40px;
            border: 1px solid var(--bordure);
            box-sizing: border-box;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 6px;
            border: 1px solid var(--bordure);
            box-sizing: border-box;
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primaire);
        }

        /* APROPOS SECTION */
        .apropos-section {
            background: var(--fond);
            padding: 80px 30px;
        }

        .apropos-content {
            max-width: 900px;
            margin: 0 auto;
        }

        .apropos-title {
            color: var(--texte);
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 30px 0;
        }

        .apropos-text {
            color: var(--texte-secondaire);
            font-size: 1rem;
            line-height: 1.8;
            margin: 0 0 20px 0;
        }

        .apropos-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .detail-item {
            background: var(--blanc);
            border-radius: 8px;
            padding: 20px;
            border: 1px solid var(--bordure);
            box-sizing: border-box;
        }

        .detail-item h5 {
            color: var(--primaire);
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .detail-item p {
            color: var(--texte-secondaire);
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .detail-item a {
            color: var(--texte-secondaire);
            text-decoration: none;
        }

        /* INSCRIPTION SECTION */
        .inscription-section {
            background: #a21c3b;
            color: var(--blanc);
            padding: 80px 30px;
            text-align: center;
        }

        .inscription-content {
            max-width: 700px;
            margin: 0 auto;
        }

        .inscription-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 0 20px 0;
        }

        .inscription-text {
            font-size: 1.1rem;
            margin: 0 0 40px 0;
            opacity: 0.95;
            line-height: 1.6;
        }

        .btn-inscription {
            background: var(--blanc);
            color: #a21c3b;
            padding: 18px 50px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.15rem;
            transition: all 0.3s ease;
            border: 2px solid var(--blanc);
            cursor: pointer;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-inscription:hover {
            background: transparent;
            color: var(--blanc);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        /* FOOTER */
        .footer {
            background: var(--texte);
            color: var(--blanc);
            padding: 60px 30px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 40px;
        }

        .footer-section h5 {
            font-weight: 700;
            margin: 0 0 20px 0;
            font-size: 1.1rem;
        }

        .footer-section p {
            margin: 10px 0;
            line-height: 1.8;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .footer-section a {
            color: var(--blanc);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: var(--primaire);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 40px;
            padding-top: 30px;
            text-align: center;
            opacity: 0.8;
        }

        /* ANIMATIONS */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* TABLETTES & PETITS ÉCRANS ORDINATEUR */
        @media (max-width: 1024px) {
            .header-nav { gap: 20px; margin-left: 20px; }
            .header-actions { gap: 15px; }
            .contact-grid { grid-template-columns: 1fr; }
        }

        /* MOBILES & RESPONSIVE GLOBAL */
        @media (max-width: 768px) {
            .header-presentation { position: static; }
            .header-presentation-content {
                flex-direction: column;
                height: auto;
                padding: 15px;
                gap: 15px;
                text-align: center;
            }
            .header-nav {
                margin-left: 0;
                justify-content: center;
                width: 100%;
                gap: 20px;
            }
            .header-actions {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }
            .header-phone, .btn-login {
                width: 100%;
                box-sizing: border-box;
                text-align: center;
            }

            .hero { padding: 60px 20px; }
            .hero h1 { font-size: 2rem; }
            .hero p { font-size: 1.1rem; }

            .universite-section, .diplomes-section, .excellence-section, .services-section, .contact-section, .apropos-section, .inscription-section {
                padding: 50px 20px;
            }
            .diplomes-title, .services-title, .universite-title { font-size: 1.6rem; }
            .diplome-card, .service-card, .contact-info-block, .contact-form-block { padding: 25px; }
            
            .footer-content { grid-template-columns: 1fr; gap: 30px; text-align: center; }
            .footer-section .btn-login { display: block; max-width: 250px; margin: 15px auto 0; }
        }
    </style>
</head>
<body>
    <header class="header-presentation">
        <div class="header-presentation-content">
            <div class="header-logo-section">
                <img src="UIYA.jpg" alt="Logo" class="header-logo-img">
            </div>
            <nav class="header-nav">
                <a href="#accueil">Accueil</a>
                <a href="#formations">Nos Facultés</a>
                <a href="#contact">Contact</a>
            </nav>
            <div class="header-actions">
                <a href="tel:+2250700000000" class="header-phone">+225 07 00 00 00 00</a>
                <a href="inscription_view.php" class="btn-login" style="background: #a21c3b; padding: 10px 24px; font-weight: 600;">S'inscrire Maintenant</a>
            </div>
        </div>
    </header>

    <section class="hero" id="accueil">
        <div class="hero-content">
            <h1 style="text-transform: none;">L'UIYA, UNE ELITE ENGAGER ET RESPONSABLE.</h1>
            <p>Rejoignez une institution dynamique où l'innovation rencontre la tradition académique pour forger les leaders de demain.</p>
        </div>
    </section>

    <section class="universite-section">
        <div class="universite-content">
            <h2 class="universite-title">UNIVERSITÉ INTERNATIONALE DE YAMOUSSOUKRO</h2>
            <p class="universite-subtitle">Excellence & Savoir</p>
        </div>
    </section>

    <section class="diplomes-section" id="formations">
        <h2 class="diplomes-title">Nos Pôles d'Excellence</h2>
        <div class="diplomes-grid">
            <div class="diplome-card">
                <div class="diplome-icon">⚖️</div>
                <h3>Droit & Sciences</h3>
                <p>Maîtrisez les enjeux juridiques nationaux et internationaux.</p>
            </div>
            <div class="diplome-card">
                <div class="diplome-icon">📊</div>
                <h3>Économie & Gestion</h3>
                <p>Devenez expert en finance, management et stratégie.</p>
            </div>
            <div class="diplome-card">
                <div class="diplome-icon">💻</div>
                <h3>Génie Logiciel</h3>
                <p>Innovez par le code et les systèmes d'information.</p>
            </div>
        </div>
    </section>

    <section class="excellence-section">
        <div class="excellence-content">
            <h2 class="excellence-title">Un environnement dédié à la recherche de l'excellence</h2>
            <p class="excellence-text">
                L'UIYA offre un cadre académique stimulant où les étudiants bénéficient d'enseignants qualifiés, d'infrastructures modernes et de ressources pédagogiques complètes pour développer leurs compétences et atteindre leurs objectifs professionnels.
            </p>
        </div>
    </section>

    <section class="services-section">
        <h2 class="services-title">Nos Produits & Services</h2>
        <div class="services-grid">
            <div class="service-card">
                <h4>Formation Académique</h4>
                <p>Programmes de Licence et Master adaptés aux standards internationaux avec un corps professoral expérimenté.</p>
            </div>
            <div class="service-card">
                <h4>Recherche & Innovation</h4>
                <p>Centres de recherche dotés d'équipements modernes pour favoriser l'innovation et la production scientifique.</p>
            </div>
            <div class="service-card">
                <h4>Services Étudiants</h4>
                <p>Support académique, orientation professionnelle et services administratifs pour une meilleure expérience étudiante.</p>
            </div>
            <div class="service-card">
                <h4>Partenariats Internationaux</h4>
                <p>Collaborations avec des universités mondiales pour des échanges académiques et des opportunités de mobility.</p>
            </div>
            <div class="service-card">
                <h4>Placements Professionnels</h4>
                <p>Programme d'insertion professionnelle avec partenaires industriels pour faciliter l'emploi des diplômés.</p>
            </div>
            <div class="service-card">
                <h4>Vie Étudiante</h4>
                <p>Activités culturelles, sportives et sociales pour un développement personnel et une intégration communautaire.</p>
            </div>
        </div>
    </section>

    <section class="contact-section" id="contact">
        <div class="contact-content">
            <h2 class="diplomes-title" style="margin-bottom: 20px;">Parlons de votre avenir</h2>
            <p style="text-align: center; color: var(--texte-secondaire); margin-bottom: 40px;">Notre équipe pédagogique vous accompagne dans vos démarches d'orientation.</p>
            
            <div class="contact-grid">
                <div class="contact-info-block">
                    <div class="contact-info-item">
                        <h5>Campus Principal</h5>
                        <p>Yamoussoukro, Côte d'Ivoire</p>
                    </div>
                    <div class="contact-info-item">
                        <h5>Secrétariat</h5>
                        <p>+225 07 00 00 00 00</p>
                    </div>
                </div>

                <div class="contact-form-block">
                    <h4 style="color: var(--texte); margin: 0 0 20px 0; font-weight: 600;">Envoyez-nous un message</h4>
                    
                    <form action="traitement_contact.php" method="POST">
                        <div class="form-group">
                            <input type="text" name="nom" class="form-input" placeholder="Nom Complet" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="form-input" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <textarea name="message" class="form-input" rows="5" placeholder="Votre message ou question..." style="resize: vertical;" required></textarea>
                        </div>
                        <button type="submit" class="btn-login" style="width: 100%; padding: 14px; font-weight: 600; border-radius: 6px;">Envoyer la requête</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="apropos-section" id="apropos">
        <div class="apropos-content">
            <h2 class="apropos-title">À propos de l'UIYA</h2>
            <p class="apropos-text">
                L'Université Internationale de Yamoussoukro (UIYA) est une institution d'enseignement supérieur basée dans la commune de Yamoussoukro, en Côte d'Ivoire. Fondée sur des principes d'excellence académique et d'innovation, l'UIYA s'engage à former des professionnels compétents, créatifs et responsables.
            </p>
            <p class="apropos-text">
                Notre établissement propose des formations de qualité couvrant diverses disciplines, avec un accent particulier sur les technologies de l'information, l'ingénierie et les sciences de gestion.
            </p>
            <div class="apropos-details">
                <div class="detail-item">
                    <h5>Localisation</h5>
                    <p>Yamoussoukro, 227 logement sur l'axe INP-HB face à la villa 216, dernière rue côté Nord-est</p>
                </div>
                <div class="detail-item">
                    <h5>Téléphones</h5>
                    <p>+225 05 85 05 45 93<br>+225 07 88 84 53 38<br>+225 05 56 06 95 65</p>
                </div>
                <div class="detail-item">
                    <h5>Email</h5>
                    <p><a href="mailto:info@uiya.ci">info@uiya.ci</a></p>
                </div>
            </div>
        </div>
    </section>

    <section class="inscription-section">
        <div class="inscription-content">
            <h2 class="inscription-title">Rejoignez-nous</h2>
            <p class="inscription-text">
                Inscrivez-vous dès maintenant et commencez votre parcours vers l'excellence académique et professionnelle.
            </p>
            <a href="inscription_view.php" class="btn-inscription">Débuter l'inscription</a>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h5>Université Internationale de yamoussoukro</h5>
                <p>Une institution dynamique pour former les leaders de demain.</p>
            </div>
            <div class="footer-section">
                <h5>Navigation</h5>
                <p><a href="#accueil">Accueil</a></p>
                <p><a href="#formations">Nos Facultés</a></p>
                <p><a href="#contact">Contact</a></p>
            </div>
            <div class="footer-section">
                <h5>Contact</h5>
                <p><a href="tel:+2250700000000">+225 07 00 00 00 00</a></p>
                <p>Yamoussoukro - Côte d'Ivoire</p>
            </div>
            <div class="footer-section">
                <h5>Espace Sécurisé</h5>
                <p>Accès réservé aux membres inscrits sur le portail.</p>
                <a href="connexion.php" class="btn-login">Connexion Portail</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Université Internationale de Yamoussoukro. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.diplome-card, .service-card');
            sections.forEach((section, index) => {
                section.style.animation = `fadeInUp 0.5s ease ${index * 0.1}s forwards`;
                section.style.opacity = '0';
            });
        });

        // Smooth scroll pour les liens d'ancrage
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>