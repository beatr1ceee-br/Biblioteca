<?php
session_start();

$jsonFile = __DIR__ . "/data/items.json";
$books = [];

if (file_exists($jsonFile)) {
    $jsonData = file_get_contents($jsonFile);
    $decodedBooks = json_decode($jsonData, true);

    if (is_array($decodedBooks)) {
        $books = $decodedBooks;
    }
}

$q = trim($_GET["q"] ?? "");
$categorie = trim($_GET["cat"] ?? "Toate");

$cartiFiltrate = array_filter($books, function ($carte) use ($q, $categorie) {
    $nume = strtolower($carte["nume"] ?? "");
    $autor = strtolower($carte["autor"] ?? "");
    $cat = strtolower($carte["categorie"] ?? "");
    $query = strtolower($q);

    $potrivireCautare = true;
    $potrivireCategorie = true;

    if ($query !== "") {
        $potrivireCautare =
            strpos($nume, $query) !== false ||
            strpos($autor, $query) !== false ||
            strpos($cat, $query) !== false;
    }

    if ($categorie !== "" && $categorie !== "Toate") {
        $potrivireCategorie = ($carte["categorie"] ?? "") === $categorie;
    }

    return $potrivireCautare && $potrivireCategorie;
});

$categorii = [];

foreach ($books as $book) {
    if (!empty($book["categorie"]) && !in_array($book["categorie"], $categorii)) {
        $categorii[] = $book["categorie"];
    }
}

sort($categorii);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cărți - Biblioteca Online</title>

    <link rel="stylesheet" href="./CSS/style.css?v=20">

    <style>
        .books-page {
            padding: 50px 20px 80px;
        }

        .page-title-box {
            background: var(--bg-white);
            padding: 35px;
            border-radius: 16px;
            margin-bottom: 35px;
            box-shadow: 0 10px 28px rgba(18, 42, 26, 0.06);
        }

        .page-title-box h1 {
            font-family: 'Cormorant Garamond', serif;
            color: var(--green-dark);
            font-size: 44px;
            margin-bottom: 10px;
        }

        .page-title-box p {
            color: var(--text-muted);
            line-height: 1.7;
        }

        .books-toolbar {
            background: var(--bg-white);
            padding: 22px;
            border-radius: 14px;
            margin-bottom: 35px;
            box-shadow: 0 8px 22px rgba(18, 42, 26, 0.05);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .books-toolbar input,
        .books-toolbar select {
            height: 48px;
            border: 1px solid #ded0bd;
            border-radius: 8px;
            padding: 0 15px;
            font-size: 15px;
            outline: none;
            background: #fffdf8;
            color: var(--text-dark);
        }

        .books-toolbar input {
            flex: 1;
            min-width: 240px;
        }

        .books-toolbar button {
            height: 48px;
            border: none;
            background: var(--green-dark);
            color: white;
            padding: 0 26px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .books-toolbar button:hover {
            background: var(--gold);
        }

        .books-toolbar a {
            height: 48px;
            display: inline-flex;
            align-items: center;
            background: #c9a985;
            color: white;
            padding: 0 22px;
            border-radius: 8px;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .books-toolbar a:hover {
            background: var(--green-dark);
        }

        .result-info {
            color: var(--text-muted);
            font-size: 15px;
            margin-bottom: 22px;
        }

        .all-books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 24px;
        }

        .all-book-card {
            background: var(--bg-white);
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            box-shadow: 0 8px 22px rgba(18, 42, 26, 0.06);
            transition: 0.2s ease;
            border: 1px solid rgba(18, 42, 26, 0.04);
        }

        .all-book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(18, 42, 26, 0.11);
        }

        .all-book-card img {
            width: 100%;
            height: 245px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 14px;
            background: #f4eee3;
        }

        .all-book-card h3 {
            color: var(--green-dark);
            font-size: 16px;
            margin-bottom: 6px;
            min-height: 42px;
        }

        .all-book-card p {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .book-cat {
            display: inline-block;
            background: #f1e4d2;
            color: #8d6b3e;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .all-book-card .details {
            display: inline-block;
            background: var(--green-dark);
            color: white;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .all-book-card .details:hover {
            background: var(--gold);
        }

        .empty-box {
            background: var(--bg-white);
            padding: 30px;
            border-radius: 14px;
            color: var(--text-muted);
            font-size: 17px;
            box-shadow: 0 8px 22px rgba(18, 42, 26, 0.05);
        }

        body.dark-theme .books-toolbar input,
        body.dark-theme .books-toolbar select {
            background: #111a14 !important;
            color: #eef5ef !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
        }

        body.dark-theme .all-book-card {
            background: #18231b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        body.dark-theme .all-book-card h3 {
            color: #d7ad63 !important;
        }

        body.dark-theme .book-cat {
            background: #243b2a !important;
            color: #d7ad63 !important;
        }

        body.dark-theme .result-info {
            color: #b8c6ba !important;
        }

        @media (max-width: 700px) {
            .page-title-box h1 {
                font-size: 34px;
            }

            .books-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .books-toolbar input,
            .books-toolbar select,
            .books-toolbar button,
            .books-toolbar a {
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
                value="<?php echo htmlspecialchars($q); ?>"
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

<main class="container books-page">

    <section class="page-title-box">
        <h1 data-i18n="books.title">Catalogul de cărți</h1>

        <p data-i18n="books.text">
            Aici poți vedea toate cărțile disponibile în Biblioteca Online.
            Caută după titlu, autor sau categorie și accesează detaliile fiecărei cărți.
        </p>
    </section>

    <form class="books-toolbar" method="GET" action="carti.php">
        <input 
            type="text" 
            name="q" 
            placeholder="Caută după titlu, autor sau categorie..."
            value="<?php echo htmlspecialchars($q); ?>"
            data-i18n-placeholder="books.searchPlaceholder"
        >

        <select name="cat">
            <option 
                value="Toate" 
                <?php echo ($categorie === "Toate") ? "selected" : ""; ?>
                data-i18n="books.allCategories"
            >
                Toate categoriile
            </option>

            <?php foreach ($categorii as $cat): ?>
                <option 
                    value="<?php echo htmlspecialchars($cat); ?>"
                    <?php echo ($categorie === $cat) ? "selected" : ""; ?>
                >
                    <?php echo htmlspecialchars($cat); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" data-i18n="search.button">Caută</button>

        <a href="carti.php" data-i18n="books.reset">Resetează</a>
    </form>

    <p class="result-info">
        <span data-i18n="books.found">Cărți găsite</span>:
        <strong><?php echo count($cartiFiltrate); ?></strong>
    </p>

    <?php if (count($cartiFiltrate) === 0): ?>
        <div class="empty-box" data-i18n="books.empty">
            Nu au fost găsite cărți pentru criteriile introduse.
        </div>
    <?php else: ?>
        <div class="all-books-grid">
            <?php foreach ($cartiFiltrate as $carte): ?>
                <div class="all-book-card">
                    <?php if (!empty($carte["imagine"])): ?>
                        <img 
                            src="<?php echo htmlspecialchars($carte["imagine"]); ?>" 
                            alt="<?php echo htmlspecialchars($carte["nume"] ?? "Carte"); ?>"
                        >
                    <?php endif; ?>

                    <h3>
                        <?php echo htmlspecialchars($carte["nume"] ?? "Fără titlu"); ?>
                    </h3>

                    <p>
                        <?php echo htmlspecialchars($carte["autor"] ?? "Autor necunoscut"); ?>
                    </p>

                    <?php if (!empty($carte["categorie"])): ?>
                        <span class="book-cat">
                            <?php echo htmlspecialchars($carte["categorie"]); ?>
                        </span>
                    <?php endif; ?>

                    <br>

                    <a 
                        href="detalii.php?id=<?php echo urlencode($carte["id"] ?? ""); ?>" 
                        class="details"
                        data-i18n="book.details"
                    >
                        Detalii
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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