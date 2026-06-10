<?php
session_start();

$mesajSucces = "";
$mesajEroare = "";

$dataDir = __DIR__ . "/data";
$contactFile = $dataDir . "/contact.json";

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

if (!file_exists($contactFile)) {
    file_put_contents($contactFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nume = trim($_POST["nume"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subiect = trim($_POST["subiect"] ?? "");
    $mesaj = trim($_POST["mesaj"] ?? "");

    if ($nume === "" || $email === "" || $subiect === "" || $mesaj === "") {
        $mesajEroare = "Completează toate câmpurile formularului!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mesajEroare = "Introduceți o adresă de email validă!";
    } elseif (strlen($mesaj) < 10) {
        $mesajEroare = "Mesajul trebuie să conțină cel puțin 10 caractere!";
    } else {
        $mesaje = json_decode(file_get_contents($contactFile), true);

        if (!is_array($mesaje)) {
            $mesaje = [];
        }

        $mesaje[] = [
            "id" => time(),
            "nume" => $nume,
            "email" => $email,
            "subiect" => $subiect,
            "mesaj" => $mesaj,
            "data_trimiterii" => date("Y-m-d H:i:s"),
            "status" => "nou"
        ];

        file_put_contents(
            $contactFile,
            json_encode($mesaje, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $mesajSucces = "Mesajul a fost trimis cu succes!";
        $_POST = [];
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Biblioteca Online</title>

    <link rel="stylesheet" href="./CSS/style.css?v=20">

    <style>
        .contact-page {
            padding: 55px 20px 85px;
        }

        .contact-hero {
            background:
                linear-gradient(to right, rgba(18, 42, 26, 0.82), rgba(18, 42, 26, 0.35)),
                url('https://images.unsplash.com/photo-1497366754035-f200968a6e72?q=80&w=1200') center/cover no-repeat;
            border-radius: 20px;
            padding: 70px 55px;
            color: #ffffff;
            margin-bottom: 38px;
            box-shadow: var(--shadow-soft);
        }

        .contact-hero h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 54px;
            line-height: 1.05;
            margin-bottom: 18px;
        }

        .contact-hero p {
            max-width: 760px;
            font-size: 16px;
            line-height: 1.8;
            opacity: 0.95;
        }

        .contact-layout {
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: 32px;
            align-items: flex-start;
        }

        .contact-card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 34px;
            box-shadow: 0 10px 28px rgba(18, 42, 26, 0.06);
        }

        .contact-card h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 34px;
            color: var(--green-dark);
            margin-bottom: 18px;
        }

        .contact-card p {
            color: var(--text-muted);
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 14px;
        }

        .contact-info-list {
            list-style: none;
            margin-top: 20px;
        }

        .contact-info-list li {
            background: #fffdf8;
            border: 1px solid #e4d7ca;
            border-radius: 10px;
            padding: 14px 15px;
            margin-bottom: 10px;
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 600;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            border: 1px solid #dfd2c5;
            border-radius: 9px;
            background: #fffdf8;
            color: var(--text-dark);
            padding: 15px 16px;
            font-size: 15px;
            outline: none;
        }

        .contact-form textarea {
            min-height: 170px;
            resize: vertical;
        }

        .contact-form input:focus,
        .contact-form textarea:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(179, 139, 67, 0.12);
        }

        .contact-form button {
            width: 190px;
            height: 50px;
            border: none;
            border-radius: 8px;
            background: var(--green-dark);
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .contact-form button:hover {
            background: var(--gold);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 14px;
        }

        .alert-success {
            background: #eef8ef;
            color: #236b35;
            border: 1px solid #a8d8b3;
        }

        .alert-error {
            background: #fff0ee;
            color: #a33a2e;
            border: 1px solid #e5b6ae;
        }

        body.dark-theme .contact-info-list li {
            background: #18231b;
            border-color: rgba(255, 255, 255, 0.12);
            color: #eef5ef;
        }

        body.dark-theme .contact-form input,
        body.dark-theme .contact-form textarea {
            background: #111a14;
            color: #eef5ef;
            border-color: rgba(255, 255, 255, 0.14);
        }

        @media (max-width: 900px) {
            .contact-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .contact-hero {
                padding: 45px 26px;
            }

            .contact-hero h1 {
                font-size: 38px;
            }

            .contact-card {
                padding: 26px;
            }

            .contact-form button {
                width: 100%;
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
            <li><a href="despre.php" data-i18n="nav.about">Despre</a></li>
            <li><a href="contact.php" class="active" data-i18n="nav.contact">Contact</a></li>
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

<main class="container contact-page">

    <section class="contact-hero">
        <h1>Contact</h1>
        <p>
            Ai o întrebare, o sugestie sau ai nevoie de ajutor? Trimite-ne un mesaj,
            iar echipa Bibliotecii Online va analiza solicitarea ta.
        </p>
    </section>

    <section class="contact-layout">
        <div class="contact-card">
            <h2>Informații de contact</h2>

            <p>
                Suntem disponibili pentru întrebări legate de catalog, rezervări,
                conturi de utilizator sau alte informații despre bibliotecă.
            </p>

            <ul class="contact-info-list">
                <li>📍 Adresă: Str. Calea Ieșilor nr. 16</li>
                <li>✉️ Email: contact@biblioteca.md</li>
                <li>☎️ Telefon: +373 600 00 000</li>
                <li>🕘 Program: Luni – Vineri, 09:00 – 17:00</li>
            </ul>
        </div>

        <div class="contact-card">
            <h2>Trimite-ne un mesaj</h2>

            <?php if ($mesajSucces !== ""): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($mesajSucces); ?>
                </div>
            <?php endif; ?>

            <?php if ($mesajEroare !== ""): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($mesajEroare); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="contact-form">
                <input 
                    type="text" 
                    name="nume" 
                    placeholder="Numele tău"
                    value="<?php echo htmlspecialchars($_POST["nume"] ?? ""); ?>"
                >

                <input 
                    type="email" 
                    name="email" 
                    placeholder="Emailul tău"
                    value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                >

                <input 
                    type="text" 
                    name="subiect" 
                    placeholder="Subiectul mesajului"
                    value="<?php echo htmlspecialchars($_POST["subiect"] ?? ""); ?>"
                >

                <textarea 
                    name="mesaj" 
                    placeholder="Scrie mesajul tău..."
                ><?php echo htmlspecialchars($_POST["mesaj"] ?? ""); ?></textarea>

                <button type="submit">Trimite mesajul</button>
            </form>
        </div>
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