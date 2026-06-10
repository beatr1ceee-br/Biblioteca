<?php
session_start();

$dataDir = __DIR__ . "/data";
$itemsFile = $dataDir . "/items.json";
$rezervariFile = $dataDir . "/rezervari.json";

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

if (!file_exists($rezervariFile)) {
    file_put_contents($rezervariFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if (!isset($_GET["id"])) {
    header("Location: carti.php");
    exit;
}

$bookId = (int)$_GET["id"];
$book = null;
$mesaj = "";
$mesajKey = "";
$mesajTip = "";

$books = [];

if (file_exists($itemsFile)) {
    $jsonData = file_get_contents($itemsFile);
    $books = json_decode($jsonData, true);

    if (!is_array($books)) {
        $books = [];
    }
}

foreach ($books as $b) {
    if (isset($b["id"]) && (int)$b["id"] === $bookId) {
        $book = $b;
        break;
    }
}

if (!$book) {
    header("Location: carti.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["rezerva"])) {
    if (!isset($_SESSION["user_id"])) {
        header("Location: autentificare.php");
        exit;
    }

    $rezervari = json_decode(file_get_contents($rezervariFile), true);

    if (!is_array($rezervari)) {
        $rezervari = [];
    }

    $existaRezervare = false;

    foreach ($rezervari as $rezervare) {
        if (
            isset($rezervare["user_id"], $rezervare["book_id"], $rezervare["status"]) &&
            $rezervare["user_id"] == $_SESSION["user_id"] &&
            $rezervare["book_id"] == $bookId &&
            $rezervare["status"] === "activă"
        ) {
            $existaRezervare = true;
            break;
        }
    }

    if ($existaRezervare) {
        $mesaj = "Ai deja o rezervare activă pentru această carte.";
        $mesajKey = "details.error.alreadyReserved";
        $mesajTip = "error";
    } else {
        $rezervari[] = [
            "id" => time(),
            "user_id" => $_SESSION["user_id"],
            "user_nume" => $_SESSION["user_nume"] ?? "Utilizator",
            "book_id" => $bookId,
            "book_title" => $book["nume"] ?? "Carte necunoscută",
            "book_author" => $book["autor"] ?? "Autor necunoscut",
            "data_rezervare" => date("Y-m-d H:i:s"),
            "status" => "activă"
        ];

        file_put_contents(
            $rezervariFile,
            json_encode($rezervari, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $mesaj = "Cartea a fost rezervată cu succes!";
        $mesajKey = "details.success.reserved";
        $mesajTip = "success";
    }
}

$numeCarte = $book["nume"] ?? "Carte fără titlu";
$autorCarte = $book["autor"] ?? "Autor necunoscut";
$imagineCarte = $book["imagine"] ?? "";
$categorieCarte = $book["categorie"] ?? "Necunoscută";
$sectiuneCarte = $book["sectiune"] ?? "General";
$notaCarte = $book["nota"] ?? "5.0";
$descriereCarte = $book["descriere"] ?? "Această carte face parte din colecția Bibliotecii Online. Utilizatorii pot consulta informațiile despre carte și o pot rezerva după autentificare.";
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($numeCarte); ?> - Biblioteca Online</title>

    <link rel="stylesheet" href="./CSS/style.css?v=20">

    <style>
        .details-page {
            padding: 50px 20px 85px;
        }

        .details-wrapper {
            background: var(--bg-white);
            border-radius: 18px;
            box-shadow: var(--shadow-soft);
            padding: 38px;
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 42px;
            align-items: flex-start;
        }

        .details-img {
            background: #fffdf9;
            border: 1px solid #e4d7ca;
            border-radius: 16px;
            padding: 18px;
        }

        .details-img img {
            width: 100%;
            height: 520px;
            object-fit: cover;
            border-radius: 12px;
            background: #f4eee3;
        }

        .book-placeholder {
            width: 100%;
            height: 520px;
            border-radius: 12px;
            background: #f4eee3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 76px;
        }

        .details-content h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 48px;
            color: var(--green-dark);
            line-height: 1.08;
            margin-bottom: 12px;
        }

        .details-author {
            font-size: 20px;
            color: var(--gold);
            font-weight: 800;
            margin-bottom: 24px;
        }

        .details-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin: 28px 0;
        }

        .detail-info-box {
            background: #fffdf9;
            border: 1px solid #dfd2c5;
            border-radius: 12px;
            padding: 18px;
        }

        .detail-info-box span {
            display: block;
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 700;
        }

        .detail-info-box strong {
            font-size: 16px;
            color: var(--green-dark);
        }

        .description-box {
            margin-top: 26px;
        }

        .description-box h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 34px;
            color: var(--green-dark);
            margin-bottom: 12px;
        }

        .description-box p {
            font-size: 16px;
            color: var(--text-muted);
            line-height: 1.8;
        }

        .details-actions {
            margin-top: 32px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .reserve-btn,
        .back-btn,
        .login-btn {
            border: none;
            border-radius: 9px;
            padding: 14px 24px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            display: inline-block;
            transition: 0.2s ease;
        }

        .reserve-btn,
        .login-btn {
            background: var(--green-dark);
            color: #ffffff;
        }

        .reserve-btn:hover,
        .login-btn:hover {
            background: var(--gold);
        }

        .back-btn {
            background: #c9a985;
            color: #ffffff;
        }

        .back-btn:hover {
            background: var(--green-dark);
        }

        .details-alert {
            padding: 15px 18px;
            border-radius: 10px;
            font-weight: 800;
            margin-bottom: 24px;
        }

        .details-alert.success {
            background: #eef8ef;
            color: #236b35;
            border: 1px solid #a8d8b3;
        }

        .details-alert.error {
            background: #fff0ee;
            color: #a33a2e;
            border: 1px solid #e5b6ae;
        }

        body.dark-theme .details-wrapper {
            background: #18231b !important;
        }

        body.dark-theme .details-img,
        body.dark-theme .detail-info-box {
            background: #111a14 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        body.dark-theme .details-content h1,
        body.dark-theme .description-box h2,
        body.dark-theme .detail-info-box strong {
            color: #d7ad63 !important;
        }

        body.dark-theme .description-box p,
        body.dark-theme .detail-info-box span {
            color: #b8c6ba !important;
        }

        @media (max-width: 950px) {
            .details-wrapper {
                grid-template-columns: 1fr;
            }

            .details-img img,
            .book-placeholder {
                height: 420px;
            }

            .details-info-grid {
                grid-template-columns: 1fr;
            }

            .details-content h1 {
                font-size: 38px;
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
            <li><a href="carti.php" class="active" data-i18n="nav.books">Cărți</a></li>
            <li><a href="categorii.php?cat=Toate" data-i18n="nav.categories">Categorii</a></li>
            <li><a href="despre.php" data-i18n="nav.about">Despre</a></li>
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
                    <span data-i18n="auth.hello">Salut</span>,
                    <?php echo htmlspecialchars($_SESSION["user_nume"] ?? "Utilizator"); ?>
                </a>

                <?php if (isset($_SESSION["user_rol"]) && $_SESSION["user_rol"] === "admin"): ?>
                    <a href="dashboard.php" class="btn-auth" data-i18n="auth.dashboard">Dashboard</a>
                <?php endif; ?>

                <a href="logout.php" class="btn-member" data-i18n="auth.logout">Logout</a>
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

<main class="container details-page">

    <?php if ($mesaj !== ""): ?>
        <div 
            class="details-alert <?php echo htmlspecialchars($mesajTip); ?>"
            data-i18n="<?php echo htmlspecialchars($mesajKey); ?>"
        >
            <?php echo htmlspecialchars($mesaj); ?>
        </div>
    <?php endif; ?>

    <section class="details-wrapper">
        <div class="details-img">
            <?php if ($imagineCarte !== ""): ?>
                <img 
                    src="<?php echo htmlspecialchars($imagineCarte); ?>" 
                    alt="<?php echo htmlspecialchars($numeCarte); ?>"
                >
            <?php else: ?>
                <div class="book-placeholder">📖</div>
            <?php endif; ?>
        </div>

        <div class="details-content">
            <h1><?php echo htmlspecialchars($numeCarte); ?></h1>

            <p class="details-author">
                <?php echo htmlspecialchars($autorCarte); ?>
            </p>

            <div class="details-info-grid">
                <div class="detail-info-box">
                    <span data-i18n="details.category">Categorie</span>
                    <strong><?php echo htmlspecialchars($categorieCarte); ?></strong>
                </div>

                <div class="detail-info-box">
                    <span data-i18n="details.section">Secțiune</span>
                    <strong><?php echo htmlspecialchars($sectiuneCarte); ?></strong>
                </div>

                <div class="detail-info-box">
                    <span data-i18n="details.rating">Notă</span>
                    <strong>⭐ <?php echo htmlspecialchars($notaCarte); ?></strong>
                </div>
            </div>

            <div class="description-box">
                <h2 data-i18n="details.description">Descriere</h2>

                <p>
                    <?php echo htmlspecialchars($descriereCarte); ?>
                </p>
            </div>

            <div class="details-actions">
                <?php if (isset($_SESSION["user_id"])): ?>
                    <form method="POST">
                        <button type="submit" name="rezerva" class="reserve-btn" data-i18n="details.reserve">
                            Rezervă cartea
                        </button>
                    </form>
                <?php else: ?>
                    <a href="autentificare.php" class="login-btn" data-i18n="details.loginToReserve">
                        Autentifică-te pentru rezervare
                    </a>
                <?php endif; ?>

                <a href="carti.php" class="back-btn" data-i18n="details.backToBooks">
                    Înapoi la cărți
                </a>
            </div>
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
                <li data-i18n="footer.terms">Termeni și condiții</li>
                <li data-i18n="footer.privacy">Politică de confidențialitate</li>
                <li data-i18n="footer.rules">Regulament utilizare</li>
                <li data-i18n="footer.help">Ajutor utilizator</li>
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