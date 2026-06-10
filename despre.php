<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despre - Biblioteca Online</title>

    <link rel="stylesheet" href="./CSS/style.css?v=20">

    <style>
        .about-page {
            padding: 55px 20px 85px;
        }

        .about-hero {
            background:
                linear-gradient(to right, rgba(18, 42, 26, 0.82), rgba(18, 42, 26, 0.35)),
                url('https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=1200') center/cover no-repeat;
            border-radius: 20px;
            padding: 75px 55px;
            color: #ffffff;
            margin-bottom: 38px;
            box-shadow: var(--shadow-soft);
        }

        .about-hero h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 54px;
            line-height: 1.05;
            margin-bottom: 18px;
        }

        .about-hero p {
            max-width: 760px;
            font-size: 16px;
            line-height: 1.8;
            opacity: 0.95;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 32px;
            margin-bottom: 35px;
        }

        .about-card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 34px;
            box-shadow: 0 10px 28px rgba(18, 42, 26, 0.06);
            margin-bottom: 35px;
        }

        .about-card h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 34px;
            color: var(--green-dark);
            margin-bottom: 16px;
        }

        .about-card p {
            color: var(--text-muted);
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 14px;
        }

        .about-list {
            list-style: none;
            margin-top: 18px;
        }

        .about-list li {
            background: #fffdf8;
            border: 1px solid #e4d7ca;
            border-radius: 10px;
            padding: 13px 15px;
            margin-bottom: 10px;
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 35px;
        }

        .stat-box {
            background: var(--bg-white);
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 8px 22px rgba(18, 42, 26, 0.05);
        }

        .stat-box strong {
            display: block;
            font-family: 'Cormorant Garamond', serif;
            font-size: 38px;
            color: var(--green-dark);
            margin-bottom: 6px;
        }

        .stat-box span {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            margin-top: 22px;
        }

        .step-card {
            background: #fffdf8;
            border-radius: 15px;
            padding: 26px;
            box-shadow: 0 8px 22px rgba(18, 42, 26, 0.05);
            border: 1px solid rgba(18, 42, 26, 0.06);
        }

        .step-number {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--green-dark);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .step-card h3 {
            color: var(--green-dark);
            font-size: 18px;
            margin-bottom: 10px;
        }

        .step-card p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.7;
            margin: 0;
        }

        body.dark-theme .about-list li,
        body.dark-theme .step-card {
            background: #18231b;
            border-color: rgba(255, 255, 255, 0.12);
            color: #eef5ef;
        }

        body.dark-theme .step-number {
            background: #d7ad63;
            color: #101611;
        }

        @media (max-width: 950px) {
            .about-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .steps-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .about-hero {
                padding: 45px 26px;
            }

            .about-hero h1 {
                font-size: 38px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .about-card {
                padding: 26px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="container nav-container">
        <a href="index.php" class="logo">
            <span class="logo-icon">📖</span>
            Biblioteca Online
        </a>

        <ul class="nav-links">
            <li><a href="index.php" data-i18n="nav.home">Acasă</a></li>
            <li><a href="carti.php" data-i18n="nav.books">Cărți</a></li>
            <li><a href="categorii.php?cat=Toate" data-i18n="nav.categories">Categorii</a></li>
            <li><a href="despre.php" class="active" data-i18n="nav.about">Despre</a></li>
            <li><a href="contact.php" data-i18n="nav.contact">Contact</a></li>
        </ul>

        <form class="nav-search-bar" action="carti.php" method="GET">
            <input 
                type="text" 
                name="q" 
                placeholder="Caută cărți, autori, categorii..."
                data-i18n-placeholder="search.placeholder"
            >
        </form>

        <div class="nav-actions">
           <?php if (isset($_SESSION["user_id"])): ?>
    <a href="utilizator.php" class="btn-auth">
        Salut, <?php echo htmlspecialchars($_SESSION["user_nume"]); ?>
    </a>

    <?php if (isset($_SESSION["user_rol"]) && $_SESSION["user_rol"] === "admin"): ?>
        <a href="dashboard.php" class="btn-auth">Dashboard</a>
    <?php endif; ?>

    <a href="logout.php" class="btn-member">Logout</a>
<?php else: ?>
                <a href="autentificare.php" class="btn-auth" data-i18n="auth.login">Autentificare</a>
                <a href="inregistrare.php" class="btn-member" data-i18n="auth.member">Devino membru</a>
            <?php endif; ?>
        </div>

        <div class="site-tools">
            <button type="button" id="themeToggle" class="tool-btn">🌙</button>

            <div class="lang-switch">
                <button type="button" data-lang="ro">RO</button>
                <button type="button" data-lang="en">EN</button>
                <button type="button" data-lang="ru">RU</button>
            </div>
        </div>
    </div>
</header>

<main class="container about-page">

    <section class="about-hero">
        <h1>Despre Biblioteca Online</h1>
        <p>
            Biblioteca Online este un spațiu digital dedicat cititorilor care doresc să descopere,
            să consulte și să rezerve cărți într-un mod simplu, rapid și accesibil.
        </p>
    </section>

    <section class="about-grid">
        <div class="about-card">
            <h2>Cine suntem?</h2>

            <p>
                Biblioteca Online reprezintă o platformă modernă destinată persoanelor pasionate de lectură,
                elevilor, studenților și tuturor celor care doresc să aibă acces rapid la informații despre cărți.
            </p>

            <p>
                Prin intermediul bibliotecii, utilizatorii pot explora diferite categorii de cărți,
                pot vedea recomandări de lectură și pot accesa detalii despre fiecare volum disponibil.
                Scopul bibliotecii este de a încuraja lectura și de a face procesul de căutare a unei cărți
                mai ușor și mai plăcut.
            </p>
        </div>

        <div class="about-card">
            <h2>Ce oferim?</h2>

            <ul class="about-list">
                <li>✓ Acces rapid la catalogul de cărți</li>
                <li>✓ Cărți organizate pe categorii</li>
                <li>✓ Recomandări de lectură pentru utilizatori</li>
                <li>✓ Informații despre titlu, autor și categorie</li>
                <li>✓ Posibilitatea de rezervare a cărților</li>
                <li>✓ Cont personal pentru fiecare utilizator</li>
                <li>✓ Contact direct cu echipa bibliotecii</li>
            </ul>
        </div>
    </section>

    <section class="stats-grid">
        <div class="stat-box">
            <strong>100+</strong>
            <span>Cărți disponibile</span>
        </div>

        <div class="stat-box">
            <strong>3+</strong>
            <span>Categorii principale</span>
        </div>

        <div class="stat-box">
            <strong>24/7</strong>
            <span>Acces online</span>
        </div>

        <div class="stat-box">
            <strong>Online</strong>
            <span>Rezervare rapidă</span>
        </div>
    </section>

    <section class="about-card">
        <h2>Misiunea bibliotecii</h2>

        <p>
            Misiunea Bibliotecii Online este de a promova lectura și de a oferi utilizatorilor
            o modalitate comodă de a descoperi cărți potrivite intereselor lor.
            Platforma facilitează accesul la informații despre cărți și ajută cititorii să aleagă
            mai ușor următoarea lectură.
        </p>

        <p>
            Biblioteca pune accent pe accesibilitate, organizare clară și o experiență plăcută pentru utilizator.
            Fiecare carte este prezentată într-un mod simplu, cu informații esențiale precum titlul,
            autorul, categoria, evaluarea și descrierea.
        </p>
    </section>

    <section class="about-card">
        <h2>Cum poți folosi biblioteca?</h2>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Explorează catalogul</h3>
                <p>
                    Accesează pagina „Cărți” pentru a vedea toate volumele disponibile în bibliotecă.
                </p>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Alege o categorie</h3>
                <p>
                    Folosește secțiunea „Categorii” pentru a găsi mai rapid cărțile care te interesează.
                </p>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Rezervă cartea dorită</h3>
                <p>
                    După autentificare, poți rezerva o carte direct din pagina de detalii a acesteia.
                </p>
            </div>
        </div>
    </section>

    <section class="about-card">
        <h2>Program și contact</h2>

        <p>
            Biblioteca Online poate fi accesată permanent, iar solicitările utilizatorilor pot fi trimise
            prin formularul de contact disponibil pe site.
        </p>

        <ul class="about-list">
            <li>📍 Adresă: Str. Calea Ieșilor nr. 16</li>
            <li>✉️ Email: contact@biblioteca.md</li>
            <li>☎️ Telefon: +373 600 00 000</li>
            <li>🕘 Program fizic: Luni – Vineri, 09:00 – 17:00</li>
        </ul>
    </section>

</main>

<footer>
    <div class="container footer-grid">
        <div class="footer-col">
            <div class="logo">📖 Biblioteca Online</div>

            <p data-i18n="footer.text">
                Locul unde fiecare carte deschide o nouă lume. Platforma oferă acces rapid la lectură,
                recomandări și informații despre cărți.
            </p>
        </div>

        <div class="footer-col">
            <h5 data-i18n="footer.contact">Contact</h5>

            <ul>
                <li>📍 Str. Calea Ieșilor nr. 16</li>
                <li>✉️ contact@biblioteca.md</li>
                <li>☎️ +373 600 00 000</li>
            </ul>
        </div>

        <div class="footer-col">
            <h5 data-i18n="footer.quick">Linkuri rapide</h5>

            <ul>
                <li><a href="index.php" data-i18n="nav.home">Acasă</a></li>
                <li><a href="carti.php" data-i18n="nav.books">Cărți</a></li>
                <li><a href="categorii.php?cat=Toate" data-i18n="nav.categories">Categorii</a></li>
                <li><a href="contact.php" data-i18n="nav.contact">Contact</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h5 data-i18n="footer.info">Informații utile</h5>

            <ul>
                <li>Termeni și condiții</li>
                <li>Politică de confidențialitate</li>
                <li>Regulament utilizare</li>
                <li>Ajutor utilizator</li>
            </ul>
        </div>

        <div class="footer-col">
            <h5 data-i18n="footer.news">Abonează-te la noutăți</h5>

            <p style="margin-bottom: 12px;" data-i18n="footer.newsText">
                Primește recomandări și informații despre cărțile noi adăugate în bibliotecă.
            </p>

            <form class="newsletter-form" action="abonare.php" method="POST">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Adresa ta de email..."
                    data-i18n-placeholder="footer.email"
                    required
                >

                <button type="submit" data-i18n="footer.subscribe">Abonează-mă</button>
            </form>
        </div>
    </div>

    <div class="footer-bottom">
        <p data-i18n="footer.rights">&copy; 2026 Biblioteca Online. Toate drepturile rezervate.</p>
    </div>
</footer>

<script src="js/script.js?v=14"></script>

</body>
</html>