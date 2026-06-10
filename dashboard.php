<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: autentificare.php");
    exit;
}

$rol = $_SESSION["user_rol"] ?? "utilizator";

if ($rol !== "admin" && $rol !== "administrator") {
    $accessDenied = true;
} else {
    $accessDenied = false;
}

$dataDir = __DIR__ . "/data";
$itemsFile = $dataDir . "/items.json";
$usersFile = $dataDir . "/users.json";
$rezervariFile = $dataDir . "/rezervari.json";
$contactFile = $dataDir . "/contact.json";

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

if (!file_exists($itemsFile)) {
    file_put_contents($itemsFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$carti = json_decode(file_get_contents($itemsFile), true);
if (!is_array($carti)) {
    $carti = [];
}

$mesaj = "";

function salveazaCarti($itemsFile, $carti) {
    file_put_contents(
        $itemsFile,
        json_encode(array_values($carti), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

if (!$accessDenied && $_SERVER["REQUEST_METHOD"] === "POST") {
    $actiune = $_POST["actiune"] ?? "";

    if ($actiune === "adauga") {
        $nume = trim($_POST["nume"] ?? "");
        $autor = trim($_POST["autor"] ?? "");
        $categorie = trim($_POST["categorie"] ?? "");
        $sectiune = trim($_POST["sectiune"] ?? "");
        $nota = trim($_POST["nota"] ?? "");
        $imagine = trim($_POST["imagine"] ?? "");
        $descriere = trim($_POST["descriere"] ?? "");

        if ($nume !== "" && $autor !== "" && $categorie !== "" && $sectiune !== "") {
            $maxId = 0;

            foreach ($carti as $carte) {
                if (isset($carte["id"]) && (int)$carte["id"] > $maxId) {
                    $maxId = (int)$carte["id"];
                }
            }

            $carti[] = [
                "id" => $maxId + 1,
                "nume" => $nume,
                "autor" => $autor,
                "categorie" => $categorie,
                "sectiune" => $sectiune,
                "nota" => $nota !== "" ? $nota : "5.0",
                "imagine" => $imagine,
                "descriere" => $descriere
            ];

            salveazaCarti($itemsFile, $carti);

            header("Location: dashboard.php?status=adaugat");
            exit;
        } else {
            $mesaj = "Completează câmpurile obligatorii: titlu, autor, categorie și secțiune.";
        }
    }

    if ($actiune === "modifica") {
        $id = (int)($_POST["id"] ?? 0);

        foreach ($carti as &$carte) {
            if (isset($carte["id"]) && (int)$carte["id"] === $id) {
                $carte["nume"] = trim($_POST["nume"] ?? "");
                $carte["autor"] = trim($_POST["autor"] ?? "");
                $carte["categorie"] = trim($_POST["categorie"] ?? "");
                $carte["sectiune"] = trim($_POST["sectiune"] ?? "");
                $carte["nota"] = trim($_POST["nota"] ?? "5.0");
                $carte["imagine"] = trim($_POST["imagine"] ?? "");
                $carte["descriere"] = trim($_POST["descriere"] ?? "");
                break;
            }
        }

        unset($carte);

        salveazaCarti($itemsFile, $carti);

        header("Location: dashboard.php?status=modificat");
        exit;
    }

    if ($actiune === "sterge") {
        $id = (int)($_POST["id"] ?? 0);

        $carti = array_filter($carti, function ($carte) use ($id) {
            return isset($carte["id"]) && (int)$carte["id"] !== $id;
        });

        salveazaCarti($itemsFile, $carti);

        header("Location: dashboard.php?status=sters");
        exit;
    }
}

$carteEditare = null;

if (!$accessDenied && isset($_GET["edit"])) {
    $editId = (int)$_GET["edit"];

    foreach ($carti as $carte) {
        if (isset($carte["id"]) && (int)$carte["id"] === $editId) {
            $carteEditare = $carte;
            break;
        }
    }
}

$status = $_GET["status"] ?? "";

$utilizatori = [];
$rezervari = [];
$mesajeContact = [];

if (file_exists($usersFile)) {
    $utilizatori = json_decode(file_get_contents($usersFile), true);
    if (!is_array($utilizatori)) {
        $utilizatori = [];
    }
}

if (file_exists($rezervariFile)) {
    $rezervari = json_decode(file_get_contents($rezervariFile), true);
    if (!is_array($rezervari)) {
        $rezervari = [];
    }
}

if (file_exists($contactFile)) {
    $mesajeContact = json_decode(file_get_contents($contactFile), true);
    if (!is_array($mesajeContact)) {
        $mesajeContact = [];
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Biblioteca Online</title>

    <link rel="stylesheet" href="./CSS/style.css?v=20">

    <style>
        .dashboard-page {
            padding: 50px 20px 85px;
        }

        .dashboard-hero {
            background: var(--bg-white);
            border-radius: 18px;
            padding: 34px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-soft);
        }

        .dashboard-hero h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 46px;
            color: var(--green-dark);
            margin-bottom: 10px;
        }

        .dashboard-hero p {
            color: var(--text-muted);
            line-height: 1.7;
            font-size: 15px;
        }

        .stats-admin {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 30px;
        }

        .admin-stat {
            background: var(--bg-white);
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 8px 22px rgba(18, 42, 26, 0.05);
            text-align: center;
        }

        .admin-stat strong {
            display: block;
            font-family: 'Cormorant Garamond', serif;
            font-size: 38px;
            color: var(--green-dark);
            margin-bottom: 6px;
        }

        .admin-stat span {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 700;
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 28px;
            align-items: flex-start;
        }

        .admin-card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 10px 28px rgba(18, 42, 26, 0.06);
            margin-bottom: 28px;
        }

        .admin-card h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            color: var(--green-dark);
            margin-bottom: 18px;
        }

        .admin-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .admin-form label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .admin-form input,
        .admin-form textarea,
        .admin-form select {
            width: 100%;
            border: 1px solid #dfd2c5;
            border-radius: 9px;
            background: #fffdf8;
            color: var(--text-dark);
            padding: 13px 14px;
            font-size: 14px;
            outline: none;
        }

        .admin-form textarea {
            min-height: 120px;
            resize: vertical;
        }

        .admin-form input:focus,
        .admin-form textarea:focus,
        .admin-form select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(179, 139, 67, 0.12);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .admin-btn {
            border: none;
            border-radius: 8px;
            padding: 12px 18px;
            background: var(--green-dark);
            color: #ffffff;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s ease;
            display: inline-block;
            text-align: center;
        }

        .admin-btn:hover {
            background: var(--gold);
        }

        .admin-btn.secondary {
            background: #c9a985;
        }

        .admin-btn.danger {
            background: #a33a2e;
        }

        .admin-btn.danger:hover {
            background: #7f2c23;
        }

        .books-admin-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 14px;
        }

        .books-admin-table th,
        .books-admin-table td {
            border-bottom: 1px solid #e4d7ca;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            vertical-align: middle;
        }

        .books-admin-table th {
            background: #fff7ec;
            color: var(--green-dark);
            font-weight: 800;
        }

        .books-admin-table td {
            color: var(--text-dark);
        }

        .books-admin-table img {
            width: 44px;
            height: 62px;
            object-fit: cover;
            border-radius: 5px;
            background: #f4eee3;
        }

        .table-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .mini-link,
        .mini-btn {
            border: none;
            border-radius: 7px;
            padding: 8px 11px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            display: inline-block;
        }

        .mini-link {
            background: var(--green-dark);
            color: #ffffff;
        }

        .mini-btn {
            background: #a33a2e;
            color: #ffffff;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 22px;
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

        .access-denied {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 36px;
            box-shadow: var(--shadow-soft);
        }

        .access-denied h1 {
            color: #a33a2e;
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
            margin-bottom: 14px;
        }

        .access-denied p {
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 20px;
        }

        body.dark-theme .admin-form input,
        body.dark-theme .admin-form textarea,
        body.dark-theme .admin-form select {
            background: #111a14;
            color: #eef5ef;
            border-color: rgba(255, 255, 255, 0.14);
        }

        body.dark-theme .books-admin-table th {
            background: #111a14;
            color: #d7ad63;
        }

        body.dark-theme .books-admin-table td {
            color: #eef5ef;
            border-color: rgba(255, 255, 255, 0.12);
        }

        @media (max-width: 1100px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .stats-admin {
                grid-template-columns: repeat(2, 1fr);
            }

            .admin-table-wrapper {
                overflow-x: auto;
            }

            .books-admin-table {
                min-width: 850px;
            }
        }

        @media (max-width: 650px) {
            .stats-admin {
                grid-template-columns: 1fr;
            }

            .dashboard-hero h1 {
                font-size: 36px;
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
            <a href="utilizator.php" class="btn-auth">
                Salut, <?php echo htmlspecialchars($_SESSION["user_nume"] ?? "Utilizator"); ?>
            </a>

            <a href="logout.php" class="btn-member">Logout</a>
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

<main class="container dashboard-page">

    <?php if ($accessDenied): ?>

        <section class="access-denied">
            <h1>Acces restricționat</h1>

            <p>
                Această pagină este destinată administratorului. Contul tău nu are permisiunea
                necesară pentru a modifica datele din aplicație.
            </p>

            <p>
                Pentru testare, setează în <strong>data/users.json</strong> câmpul:
                <strong>"rol": "admin"</strong> pentru utilizatorul tău.
            </p>

            <a href="index.php" class="admin-btn">Înapoi la pagina principală</a>
        </section>

    <?php else: ?>

        <section class="dashboard-hero">
            <h1>Dashboard administrator</h1>
            <p>
                Din această pagină administratorul poate gestiona catalogul Bibliotecii Online:
                adăugare, modificare și ștergere cărți din fișierul <strong>data/items.json</strong>.
            </p>
        </section>

        <section class="stats-admin">
            <div class="admin-stat">
                <strong><?php echo count($carti); ?></strong>
                <span>Cărți în catalog</span>
            </div>

            <div class="admin-stat">
                <strong><?php echo count($utilizatori); ?></strong>
                <span>Utilizatori</span>
            </div>

            <div class="admin-stat">
                <strong><?php echo count($rezervari); ?></strong>
                <span>Rezervări</span>
            </div>

            <div class="admin-stat">
                <strong><?php echo count($mesajeContact); ?></strong>
                <span>Mesaje contact</span>
            </div>
        </section>

        <?php if ($status === "adaugat"): ?>
            <div class="alert alert-success">Cartea a fost adăugată cu succes.</div>
        <?php elseif ($status === "modificat"): ?>
            <div class="alert alert-success">Cartea a fost modificată cu succes.</div>
        <?php elseif ($status === "sters"): ?>
            <div class="alert alert-success">Cartea a fost ștearsă cu succes.</div>
        <?php endif; ?>

        <?php if ($mesaj !== ""): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($mesaj); ?>
            </div>
        <?php endif; ?>

        <section class="admin-layout">

            <div class="admin-card">
                <h2>
                    <?php echo $carteEditare ? "Modifică o carte" : "Adaugă o carte"; ?>
                </h2>

                <form method="POST" class="admin-form">
                    <?php if ($carteEditare): ?>
                        <input type="hidden" name="actiune" value="modifica">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($carteEditare["id"]); ?>">
                    <?php else: ?>
                        <input type="hidden" name="actiune" value="adauga">
                    <?php endif; ?>

                    <div>
                        <label>Titlul cărții *</label>
                        <input 
                            type="text" 
                            name="nume" 
                            value="<?php echo htmlspecialchars($carteEditare["nume"] ?? ""); ?>"
                            required
                        >
                    </div>

                    <div>
                        <label>Autor *</label>
                        <input 
                            type="text" 
                            name="autor" 
                            value="<?php echo htmlspecialchars($carteEditare["autor"] ?? ""); ?>"
                            required
                        >
                    </div>

                    <div>
                        <label>Categorie *</label>
                        <input 
                            type="text" 
                            name="categorie" 
                            placeholder="Ex: Fictiune, Istorie, Dezvoltare"
                            value="<?php echo htmlspecialchars($carteEditare["categorie"] ?? ""); ?>"
                            required
                        >
                    </div>

                    <div>
                        <label>Secțiune *</label>
                        <select name="sectiune" required>
                            <?php $sectiuneCurenta = $carteEditare["sectiune"] ?? "recomandate"; ?>

                            <option value="recomandate" <?php echo $sectiuneCurenta === "recomandate" ? "selected" : ""; ?>>
                                recomandate
                            </option>

                            <option value="pentru_tine" <?php echo $sectiuneCurenta === "pentru_tine" ? "selected" : ""; ?>>
                                pentru_tine
                            </option>
                        </select>
                    </div>

                    <div>
                        <label>Notă</label>
                        <input 
                            type="text" 
                            name="nota" 
                            placeholder="Ex: 4.8"
                            value="<?php echo htmlspecialchars($carteEditare["nota"] ?? "5.0"); ?>"
                        >
                    </div>

                    <div>
                        <label>Imagine</label>
                        <input 
                            type="text" 
                            name="imagine" 
                            placeholder="images/carte.jpg sau link imagine"
                            value="<?php echo htmlspecialchars($carteEditare["imagine"] ?? ""); ?>"
                        >
                    </div>

                    <div>
                        <label>Descriere</label>
                        <textarea name="descriere"><?php echo htmlspecialchars($carteEditare["descriere"] ?? ""); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="admin-btn">
                            <?php echo $carteEditare ? "Salvează modificările" : "Adaugă cartea"; ?>
                        </button>

                        <?php if ($carteEditare): ?>
                            <a href="dashboard.php" class="admin-btn secondary">Anulează editarea</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <h2>Lista cărților</h2>

                <div class="admin-table-wrapper">
                    <table class="books-admin-table">
                        <thead>
                            <tr>
                                <th>Imagine</th>
                                <th>ID</th>
                                <th>Titlu</th>
                                <th>Autor</th>
                                <th>Categorie</th>
                                <th>Secțiune</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($carti) === 0): ?>
                                <tr>
                                    <td colspan="7">Nu există cărți în catalog.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($carti as $carte): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($carte["imagine"])): ?>
                                                <img 
                                                    src="<?php echo htmlspecialchars($carte["imagine"]); ?>" 
                                                    alt="Carte"
                                                >
                                            <?php endif; ?>
                                        </td>

                                        <td><?php echo htmlspecialchars($carte["id"] ?? ""); ?></td>

                                        <td>
                                            <strong>
                                                <?php echo htmlspecialchars($carte["nume"] ?? "Fără titlu"); ?>
                                            </strong>
                                        </td>

                                        <td><?php echo htmlspecialchars($carte["autor"] ?? "Autor necunoscut"); ?></td>

                                        <td><?php echo htmlspecialchars($carte["categorie"] ?? "-"); ?></td>

                                        <td><?php echo htmlspecialchars($carte["sectiune"] ?? "-"); ?></td>

                                        <td>
                                            <div class="table-actions">
                                                <a 
                                                    href="dashboard.php?edit=<?php echo urlencode($carte["id"] ?? ""); ?>" 
                                                    class="mini-link"
                                                >
                                                    Editează
                                                </a>

                                                <form method="POST" onsubmit="return confirm('Sigur dorești să ștergi această carte?');">
                                                    <input type="hidden" name="actiune" value="sterge">
                                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($carte["id"] ?? ""); ?>">

                                                    <button type="submit" class="mini-btn">
                                                        Șterge
                                                    </button>
                                                </form>

                                                <a 
                                                    href="detalii.php?id=<?php echo urlencode($carte["id"] ?? ""); ?>" 
                                                    class="mini-link"
                                                >
                                                    Vezi
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>

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

<script src="js/script.js?v=10"></script>

</body>
</html>