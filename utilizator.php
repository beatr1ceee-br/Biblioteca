<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: autentificare.php");
    exit;
}

$userId = $_SESSION["user_id"];

$dataDir = __DIR__ . "/data";
$usersFile = $dataDir . "/users.json";
$itemsFile = $dataDir . "/items.json";
$rezervariFile = $dataDir . "/rezervari.json";

$userCurent = null;
$carti = [];
$rezervari = [];
$rezervarileMele = [];

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

if (!file_exists($rezervariFile)) {
    file_put_contents($rezervariFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if (!file_exists($usersFile)) {
    session_destroy();
    header("Location: autentificare.php");
    exit;
}

$users = json_decode(file_get_contents($usersFile), true);

if (!is_array($users)) {
    $users = [];
}

foreach ($users as $user) {
    if (isset($user["id"]) && $user["id"] == $userId) {
        $userCurent = $user;
        break;
    }
}

if (!$userCurent) {
    session_destroy();
    header("Location: autentificare.php");
    exit;
}

if (file_exists($itemsFile)) {
    $carti = json_decode(file_get_contents($itemsFile), true);

    if (!is_array($carti)) {
        $carti = [];
    }
}

$cartiDupaId = [];

foreach ($carti as $carte) {
    if (isset($carte["id"])) {
        $cartiDupaId[$carte["id"]] = $carte;
    }
}

$mesaj = "";
$mesajKey = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["anuleaza_rezervare"])) {
    $idRezervare = $_POST["anuleaza_rezervare"];

    $rezervari = json_decode(file_get_contents($rezervariFile), true);

    if (!is_array($rezervari)) {
        $rezervari = [];
    }

    foreach ($rezervari as &$rezervare) {
        if (
            isset($rezervare["id"], $rezervare["user_id"]) &&
            $rezervare["id"] == $idRezervare &&
            $rezervare["user_id"] == $userId
        ) {
            $rezervare["status"] = "anulată";
            $rezervare["data_anulare"] = date("Y-m-d H:i:s");
            $mesaj = "Rezervarea a fost anulată cu succes.";
            $mesajKey = "user.cancelSuccess";
            break;
        }
    }

    unset($rezervare);

    file_put_contents(
        $rezervariFile,
        json_encode($rezervari, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

$rezervari = json_decode(file_get_contents($rezervariFile), true);

if (!is_array($rezervari)) {
    $rezervari = [];
}

foreach ($rezervari as $rezervare) {
    if (isset($rezervare["user_id"]) && $rezervare["user_id"] == $userId) {
        $rezervarileMele[] = $rezervare;
    }
}

$numeUtilizator = $userCurent["nume"] ?? "Utilizator";
$emailUtilizator = $userCurent["email"] ?? "Email indisponibil";
$rolUtilizator = $userCurent["rol"] ?? "utilizator";
$dataInregistrare = $userCurent["data_inregistrare"] ?? ($userCurent["data_creare"] ?? "Necunoscut");
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina utilizatorului - Biblioteca Online</title>

    <link rel="stylesheet" href="./CSS/style.css?v=20">

    <style>
        .topbar {
            width: 100%;
            min-height: 88px;
            background: var(--bg-white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 32px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04);
            gap: 20px;
            flex-wrap: wrap;
        }

        .topbar .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--green-dark);
            display: flex;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 22px;
            font-size: 16px;
            font-weight: 700;
            flex-wrap: wrap;
        }

        .menu a {
            color: var(--text-dark);
            transition: 0.2s ease;
        }

        .menu a:hover,
        .menu a.active {
            color: var(--gold);
        }

        .logout-btn {
            background: var(--green-dark);
            color: #ffffff !important;
            padding: 11px 20px;
            border-radius: 8px;
            font-weight: 800;
        }

        .logout-btn:hover {
            background: var(--gold);
        }

        .page {
            padding: 48px 0 80px;
        }

        .profile-container {
            width: calc(100% - 320px);
            max-width: 1580px;
            margin: 0 auto;
        }

        .profile-card {
            background: var(--bg-white);
            border-radius: 16px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
            padding: 34px 36px;
            margin-bottom: 28px;
        }

        .profile-card h1,
        .profile-card h2 {
            font-size: 32px;
            color: var(--gold-light);
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #e4d7ca;
        }

        .welcome-text {
            font-size: 18px;
            line-height: 1.7;
            color: var(--text-dark);
            max-width: 1100px;
        }

        .user-info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-top: 26px;
        }

        .info-box {
            background: #fffdf9;
            border: 1px solid #dfd2c5;
            border-radius: 12px;
            padding: 20px;
        }

        .info-box span {
            display: block;
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 700;
        }

        .info-box strong {
            font-size: 16px;
            color: var(--green-dark);
            word-break: break-word;
        }

        .actions-row {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 28px;
        }

        .action-btn {
            background: #c9a985;
            color: #ffffff;
            padding: 13px 22px;
            border-radius: 8px;
            font-weight: 800;
            transition: 0.2s ease;
        }

        .action-btn:hover {
            background: var(--green-dark);
        }

        .alert-success {
            background: #eef8ef;
            color: #236b35;
            border: 1px solid #a8d8b3;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-weight: 700;
        }

        .reservations-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 22px;
            margin-top: 26px;
        }

        .reservation-card {
            background: #fffdf9;
            border: 1px solid #e4d7ca;
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
        }

        .reservation-card h3 {
            font-size: 18px;
            color: var(--green-dark);
            margin-bottom: 8px;
        }

        .reservation-card p {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .reservation-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            margin: 8px 0 14px;
        }

        .status-active {
            background: #eef8ef;
            color: #236b35;
            border: 1px solid #a8d8b3;
        }

        .status-cancelled {
            background: #fff0ee;
            color: #a33a2e;
            border: 1px solid #e5b6ae;
        }

        .cancel-btn {
            border: none;
            background: #a33a2e;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 800;
            cursor: pointer;
            margin-left: 6px;
        }

        .cancel-btn:hover {
            background: #7f2c23;
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 22px;
            margin-top: 26px;
        }

        .profile-book-card {
            background: #fffdf9;
            border: 1px solid #e4d7ca;
            border-radius: 14px;
            padding: 14px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
            transition: 0.2s ease;
        }

        .profile-book-card:hover {
            transform: translateY(-4px);
        }

        .profile-book-card img {
            width: 100%;
            height: 235px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 14px;
            background: #f4f2ec;
        }

        .profile-book-card h3 {
            font-size: 16px;
            color: var(--green-dark);
            margin-bottom: 6px;
            min-height: 40px;
        }

        .profile-book-card p {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .book-category {
            display: inline-block;
            font-size: 12px;
            background: #f0e5d6;
            color: #8a6a3f;
            padding: 5px 10px;
            border-radius: 20px;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .details-btn {
            display: inline-block;
            background: var(--green-dark);
            color: #ffffff;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 800;
        }

        .details-btn:hover {
            background: #c9a985;
        }

        .empty-message {
            font-size: 17px;
            color: var(--text-muted);
            margin-top: 20px;
            line-height: 1.6;
        }

        body.dark-theme .topbar {
            background: #101611 !important;
        }

        body.dark-theme .profile-card,
        body.dark-theme .info-box,
        body.dark-theme .reservation-card,
        body.dark-theme .profile-book-card {
            background: #18231b !important;
            color: #eef5ef !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        body.dark-theme .welcome-text,
        body.dark-theme .menu a,
        body.dark-theme .reservation-card p,
        body.dark-theme .profile-book-card p,
        body.dark-theme .empty-message,
        body.dark-theme .info-box span {
            color: #b8c6ba !important;
        }

        body.dark-theme .profile-card h1,
        body.dark-theme .profile-card h2,
        body.dark-theme .info-box strong,
        body.dark-theme .reservation-card h3,
        body.dark-theme .profile-book-card h3,
        body.dark-theme .topbar .logo {
            color: #d7ad63 !important;
        }

        body.dark-theme .book-category {
            background: #243b2a;
            color: #d7ad63;
        }

        @media (max-width: 1200px) {
            .profile-container {
                width: calc(100% - 80px);
            }

            .user-info-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 850px) {
            .topbar {
                flex-direction: column;
                text-align: center;
            }

            .menu {
                justify-content: center;
            }

            .site-tools {
                justify-content: center;
            }

            .profile-container {
                width: calc(100% - 32px);
            }

            .profile-card {
                padding: 26px 22px;
            }

            .user-info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 520px) {
            .topbar .logo {
                font-size: 28px;
            }

            .profile-card h1,
            .profile-card h2 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>

<header class="topbar">
    <a href="index.php" class="logo">
        <span>📖</span>
        Biblioteca Online
    </a>

    <nav class="menu">
        <a href="index.php" data-i18n="nav.home">Acasă</a>
        <a href="utilizator.php" class="active" data-i18n="hero.myPage">Pagina mea</a>
        <a href="carti.php" data-i18n="nav.books">Cărți</a>
        <a href="categorii.php?cat=Toate" data-i18n="nav.categories">Categorii</a>
        <a href="despre.php" data-i18n="nav.about">Despre</a>
        <a href="contact.php" data-i18n="nav.contact">Contact</a>

        <?php if (isset($_SESSION["user_rol"]) && $_SESSION["user_rol"] === "admin"): ?>
            <a href="dashboard.php" data-i18n="auth.dashboard">Dashboard</a>
        <?php endif; ?>

        <a href="logout.php" class="logout-btn" data-i18n="auth.logout">Logout</a>
    </nav>

    <div class="site-tools">
        <button type="button" id="themeToggle" class="tool-btn">🌙</button>

        <div class="lang-switch">
            <button type="button" data-lang="ro">RO</button>
            <button type="button" data-lang="en">EN</button>
            <button type="button" data-lang="ru">RU</button>
        </div>
    </div>
</header>

<main class="page">
    <div class="profile-container">

        <?php if ($mesaj !== ""): ?>
            <div 
                class="alert-success"
                <?php if ($mesajKey !== ""): ?>
                    data-i18n="<?php echo htmlspecialchars($mesajKey); ?>"
                <?php endif; ?>
            >
                <?php echo htmlspecialchars($mesaj); ?>
            </div>
        <?php endif; ?>

        <section class="profile-card">
            <h1 data-i18n="user.title">Pagina utilizatorului</h1>

            <p class="welcome-text">
                <span data-i18n="user.welcome">Bine ai venit în pagina ta personală din aplicația Biblioteca Online.</span>
            </p>

            <div class="user-info-grid">
                <div class="info-box">
                    <span data-i18n="user.id">ID utilizator</span>
                    <strong><?php echo htmlspecialchars($userId); ?></strong>
                </div>

                <div class="info-box">
                    <span data-i18n="user.name">Nume</span>
                    <strong><?php echo htmlspecialchars($numeUtilizator); ?></strong>
                </div>

                <div class="info-box">
                    <span data-i18n="user.email">Email</span>
                    <strong><?php echo htmlspecialchars($emailUtilizator); ?></strong>
                </div>

                <div class="info-box">
                    <span data-i18n="user.role">Rol</span>
                    <strong><?php echo htmlspecialchars($rolUtilizator); ?></strong>
                </div>
            </div>

            <div class="actions-row">
                <a href="carti.php" class="action-btn" data-i18n="user.viewBooks">Vezi toate cărțile</a>
                <a href="index.php" class="action-btn" data-i18n="user.backHome">Înapoi la pagina principală</a>
            </div>
        </section>

        <section class="profile-card">
            <h2 data-i18n="user.reservations">Rezervările mele</h2>

            <?php if (count($rezervarileMele) === 0): ?>
                <p class="empty-message" data-i18n="user.noReservations">
                    Nu ai încă nicio rezervare. Intră în catalog și rezervă prima ta carte.
                </p>
            <?php else: ?>
                <div class="reservations-list">
                    <?php foreach ($rezervarileMele as $rezervare): ?>
                        <?php
                            $bookId = $rezervare["book_id"] ?? null;
                            $carteRezervata = $bookId && isset($cartiDupaId[$bookId]) ? $cartiDupaId[$bookId] : null;
                            $status = $rezervare["status"] ?? "activă";
                        ?>

                        <div class="reservation-card">
                            <h3>
                                <?php echo htmlspecialchars($rezervare["book_title"] ?? ($carteRezervata["nume"] ?? "Carte necunoscută")); ?>
                            </h3>

                            <p>
                                <strong data-i18n="details.author">Autor</strong>:
                                <?php echo htmlspecialchars($rezervare["book_author"] ?? ($carteRezervata["autor"] ?? "Autor necunoscut")); ?>
                            </p>

                            <p>
                                <strong data-i18n="user.reservationDate">Data rezervării</strong>:
                                <?php echo htmlspecialchars($rezervare["data_rezervare"] ?? "Necunoscută"); ?>
                            </p>

                            <span class="reservation-status <?php echo ($status === "activă") ? "status-active" : "status-cancelled"; ?>">
                                <?php echo htmlspecialchars(ucfirst($status)); ?>
                            </span>

                            <br>

                            <?php if ($carteRezervata): ?>
                                <a href="detalii.php?id=<?php echo htmlspecialchars($bookId); ?>" class="details-btn" data-i18n="book.details">
                                    Detalii
                                </a>
                            <?php endif; ?>

                            <?php if ($status === "activă"): ?>
                                <form method="POST" style="display:inline;">
                                    <button
                                        type="submit"
                                        name="anuleaza_rezervare"
                                        value="<?php echo htmlspecialchars($rezervare["id"]); ?>"
                                        class="cancel-btn"
                                        data-i18n="user.cancel"
                                    >
                                        Anulează
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="profile-card">
            <h2 data-i18n="user.jsonData">Cărți disponibile în bibliotecă</h2>

            <?php if (count($carti) === 0): ?>
                <p class="empty-message" data-i18n="user.noBooks">
                    Nu există cărți salvate în fișierul data/items.json.
                </p>
            <?php else: ?>
                <div class="books-grid">
                    <?php foreach ($carti as $carte): ?>
                        <div class="profile-book-card">
                            <?php if (!empty($carte["imagine"])): ?>
                                <img
                                    src="<?php echo htmlspecialchars($carte["imagine"]); ?>"
                                    alt="<?php echo htmlspecialchars($carte["nume"] ?? "Carte"); ?>"
                                >
                            <?php endif; ?>

                            <h3><?php echo htmlspecialchars($carte["nume"] ?? "Fără titlu"); ?></h3>

                            <p><?php echo htmlspecialchars($carte["autor"] ?? "Autor necunoscut"); ?></p>

                            <?php if (!empty($carte["categorie"])): ?>
                                <span class="book-category">
                                    <?php echo htmlspecialchars($carte["categorie"]); ?>
                                </span>
                            <?php endif; ?>

                            <br>

                            <a
                                href="detalii.php?id=<?php echo htmlspecialchars($carte["id"] ?? ""); ?>"
                                class="details-btn"
                                data-i18n="book.details"
                            >
                                Detalii
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>
</main>

<script src="js/script.js?v=14"></script>

</body>
</html>