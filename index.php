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

$recomandate = array_filter($books, function ($b) {
    return isset($b["sectiune"]) && $b["sectiune"] === "recomandate";
});

$pentruTine = array_filter($books, function ($b) {
    return isset($b["sectiune"]) && $b["sectiune"] === "pentru_tine";
});
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Online</title>

    <link rel="stylesheet" href="./CSS/style.css?v=20">
</head>
<body>

<header>
    <div class="container nav-container">
        <a href="index.php" class="logo">
            <span class="logo-icon">📖</span>
            Biblioteca Online
        </a>

        <ul class="nav-links">
            <li><a href="index.php" class="active" data-i18n="nav.home">Acasă</a></li>
            <li><a href="carti.php" data-i18n="nav.books">Cărți</a></li>
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

<?php if (isset($_GET["abonare"])): ?>
    <div class="container" style="margin-top:20px;">
        <?php if ($_GET["abonare"] === "succes"): ?>
            <div style="background:#eef8ef;color:#236b35;border:1px solid #a8d8b3;padding:14px 16px;border-radius:10px;font-weight:700;">
                Te-ai abonat cu succes la noutăți!
            </div>
        <?php elseif ($_GET["abonare"] === "exista"): ?>
            <div style="background:#fff8e8;color:#8a6a3f;border:1px solid #e6c98a;padding:14px 16px;border-radius:10px;font-weight:700;">
                Acest email este deja abonat.
            </div>
        <?php else: ?>
            <div style="background:#fff0ee;color:#a33a2e;border:1px solid #e5b6ae;padding:14px 16px;border-radius:10px;font-weight:700;">
                Introdu o adresă de email validă.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<section class="hero-section container">
    <div class="hero-banner">
        <div class="hero-content">
            <h2 data-i18n="hero.title">Bine ai venit la Biblioteca Online</h2>

            <p data-i18n="hero.text">
                Descoperă cărți extraordinare, cunoaștere valoroasă și bucuria lecturii în fiecare zi.
            </p>

            <div class="hero-buttons">
                <a href="carti.php" class="btn-primary" data-i18n="hero.explore">Explorează cărțile</a>

                <?php if (isset($_SESSION["user_id"])): ?>
                    <a href="utilizator.php" class="btn-secondary" data-i18n="hero.myPage">Pagina mea</a>
                <?php else: ?>
                    <a href="inregistrare.php" class="btn-secondary" data-i18n="hero.member">Devino membru</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <form class="search-box-floating" action="carti.php" method="GET">
        <div 
            style="font-size: 13px; font-weight:600; color:var(--green-dark);"
            data-i18n="search.label"
        >
            Găsește cartea potrivită pentru tine
        </div>

        <div class="search-input-wrapper">
            <input 
                type="text" 
                name="q" 
                placeholder="Caută după titlu, autor sau cuvânt cheie..."
                data-i18n-placeholder="search.heroPlaceholder"
            >
        </div>

        <button type="submit" class="btn-search" data-i18n="search.button">Caută</button>
    </form>
</section>

<main class="container main-layout">

    <div class="content-area">

        <section>
            <div class="section-header">
                <h3 data-i18n="section.recommended">Cărți recomandate</h3>
                <a href="carti.php" class="view-all" data-i18n="view.all">Vezi toate →</a>
            </div>

            <div class="books-row">
                <?php if (count($recomandate) === 0): ?>
                    <p>Nu există cărți recomandate în data/items.json.</p>
                <?php else: ?>
                    <?php foreach ($recomandate as $carte): ?>
                        <div class="book-card">
                            <img 
                                src="<?php echo htmlspecialchars($carte["imagine"] ?? ""); ?>" 
                                alt="<?php echo htmlspecialchars($carte["nume"] ?? "Carte"); ?>"
                            >

                            <div class="book-title">
                                <?php echo htmlspecialchars($carte["nume"] ?? "Fără titlu"); ?>
                            </div>

                            <div class="book-author">
                                <?php echo htmlspecialchars($carte["autor"] ?? "Autor necunoscut"); ?>
                            </div>

                            <a 
                                href="detalii.php?id=<?php echo urlencode($carte["id"] ?? ""); ?>" 
                                class="btn-card-details"
                            >
                                Detalii
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="recommendations-list">
            <div class="section-header">
                <h3 data-i18n="section.forYou">Recomandările noastre pentru tine</h3>
                <a href="carti.php" class="view-all" data-i18n="view.all">Vezi toate →</a>
            </div>

            <div class="books-list-vertical">
                <?php if (count($pentruTine) === 0): ?>
                    <p>Nu există recomandări în data/items.json.</p>
                <?php else: ?>
                    <?php foreach ($pentruTine as $carte): ?>
                        <div class="book-item-horizontal">
                            <img 
                                src="<?php echo htmlspecialchars($carte["imagine"] ?? ""); ?>" 
                                alt="<?php echo htmlspecialchars($carte["nume"] ?? "Carte"); ?>"
                            >

                            <div class="book-info">
                                <div>
                                    <div class="book-title">
                                        <?php echo htmlspecialchars($carte["nume"] ?? "Fără titlu"); ?>
                                    </div>

                                    <div class="book-author">
                                        <?php echo htmlspecialchars($carte["autor"] ?? "Autor necunoscut"); ?>
                                    </div>
                                </div>

                                <div class="rating">
                                    ⭐⭐⭐⭐⭐ <?php echo htmlspecialchars($carte["nota"] ?? "5.0"); ?>
                                </div>

                                <a 
                                    href="detalii.php?id=<?php echo urlencode($carte["id"] ?? ""); ?>" 
                                    class="btn-card-details" 
                                    style="align-self: flex-start; margin-top: 5px;"
                                >
                                    Detalii
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

    </div>

    <aside class="sidebar">

        <div>
            <div class="section-header">
                <h3 data-i18n="section.categories">Categorii populare</h3>
                <a href="categorii.php?cat=Toate" class="view-all" data-i18n="view.all">Vezi toate →</a>
            </div>

            <div class="categories-grid">
                <?php
                $categoriiConfig = [
                    "Fictiune" => "📚 Ficțiune",
                    "Dezvoltare" => "🌱 Dezvoltare personală",
                    "Istorie" => "🏛️ Istorie"
                ];

                foreach ($categoriiConfig as $cheieCat => $numeAfisat):
                    $numarCarti = count(array_filter($books, function ($b) use ($cheieCat) {
                        return isset($b["categorie"]) && $b["categorie"] === $cheieCat;
                    }));
                ?>
                    <a 
                        href="categorii.php?cat=<?php echo urlencode($cheieCat); ?>" 
                        class="category-card"
                    >
                        <span class="category-title">
                            <?php echo $numeAfisat; ?>
                        </span>

                        <span class="category-count">
                            <?php echo $numarCarti; ?>
                            <?php echo ($numarCarti == 1) ? "carte" : "cărți"; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="member-box">
            <h4 data-i18n="member.title">Devino membru</h4>

            <p data-i18n="member.text">
                Alătură-te comunității noastre de cititori și bucură-te de beneficii exclusive:
            </p>

            <ul>
                <li data-i18n="member.li1">✓ Acces la mii de cărți online</li>
                <li data-i18n="member.li2">✓ Reduceri și oferte speciale</li>
                <li data-i18n="member.li3">✓ Recomandări personalizate</li>
            </ul>

            <?php if (isset($_SESSION["user_id"])): ?>
                <a href="utilizator.php" class="btn-join-now" data-i18n="hero.myPage">Pagina mea</a>
            <?php else: ?>
                <a href="inregistrare.php" class="btn-join-now" data-i18n="member.join">Înscrie-te acum</a>
            <?php endif; ?>
        </div>

    </aside>
</main>

<footer>
    <div class="container footer-grid">
        <div class="footer-col">
            <div class="logo">
                📖 Biblioteca Online
            </div>

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