<?php
session_start();

$eroare = "";
$eroareKey = "";

$dataDir = __DIR__ . "/data";
$usersFile = $dataDir . "/users.json";

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

if (!file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $parola = trim($_POST["parola"] ?? "");

    if ($email === "" || $parola === "") {
        $eroare = "Completează emailul și parola!";
        $eroareKey = "login.error.empty";
    } else {
        $users = json_decode(file_get_contents($usersFile), true);

        if (!is_array($users)) {
            $users = [];
        }

        foreach ($users as $user) {
            if (
                isset($user["email"], $user["parola"]) &&
                strtolower($user["email"]) === strtolower($email) &&
                password_verify($parola, $user["parola"])
            ) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_nume"] = $user["nume"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["user_rol"] = $user["rol"] ?? "utilizator";

                header("Location: utilizator.php");
                exit;
            }
        }

        $eroare = "Email sau parolă incorectă!";
        $eroareKey = "login.error.invalid";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare - Biblioteca Online</title>

    <link rel="stylesheet" href="./CSS/style.css?v=20">

    <style>
        .auth-topbar {
            width: 100%;
            min-height: 88px;
            background: var(--bg-white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 32px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04);
            gap: 22px;
            flex-wrap: wrap;
        }

        .auth-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--green-dark);
            display: flex;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
        }

        .auth-logo span {
            font-size: 30px;
        }

        .auth-menu {
            display: flex;
            align-items: center;
            gap: 28px;
            font-size: 16px;
            font-weight: 700;
            flex-wrap: wrap;
        }

        .auth-menu a {
            color: var(--text-dark);
            transition: 0.2s ease;
        }

        .auth-menu a:hover,
        .auth-menu a.active {
            color: var(--gold);
        }

        .auth-page {
            min-height: calc(100vh - 88px);
            padding: 48px 0 80px;
        }

        .auth-card {
            width: calc(100% - 320px);
            max-width: 1580px;
            margin: 0 auto 28px;
            background: var(--bg-white);
            border-radius: 16px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
        }

        .auth-info-card {
            padding: 34px 36px;
        }

        .auth-info-card h2,
        .auth-form-card h1 {
            font-size: 32px;
            color: var(--gold-light);
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #e4d7ca;
        }

        .auth-info-card p {
            font-size: 18px;
            line-height: 1.7;
            color: var(--text-dark);
            max-width: 1150px;
            margin-bottom: 6px;
        }

        .auth-form-card {
            padding: 34px 36px 26px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 22px;
            background: #fff0ee;
            color: #a33a2e;
            border: 1px solid #e5b6ae;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 26px;
        }

        .login-form input {
            width: 100%;
            height: 58px;
            border: 1px solid #dfd2c5;
            border-radius: 8px;
            background: #fffdf9;
            padding: 0 18px;
            font-size: 16px;
            color: var(--text-dark);
            outline: none;
            font-family: 'Inter', Arial, sans-serif;
        }

        .login-form input::placeholder {
            color: var(--text-muted);
        }

        .login-form input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 4px rgba(196, 162, 132, 0.13);
        }

        .login-form button {
            width: 185px;
            height: 56px;
            border: none;
            border-radius: 8px;
            background: #c9a985;
            color: #ffffff;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .login-form button:hover {
            background: var(--green-dark);
        }

        .register-text {
            margin-top: 24px;
            font-size: 17px;
            color: var(--text-dark);
        }

        .register-text a {
            color: var(--green-dark);
            font-weight: 800;
        }

        .register-text a:hover {
            color: var(--gold);
        }

        body.dark-theme .auth-topbar,
        body.dark-theme .auth-card {
            background: #18231b !important;
            color: #eef5ef !important;
        }

        body.dark-theme .auth-menu a,
        body.dark-theme .auth-info-card p,
        body.dark-theme .register-text {
            color: #b8c6ba !important;
        }

        body.dark-theme .auth-logo,
        body.dark-theme .auth-info-card h2,
        body.dark-theme .auth-form-card h1,
        body.dark-theme .register-text a {
            color: #d7ad63 !important;
        }

        body.dark-theme .login-form input {
            background: #111a14 !important;
            color: #eef5ef !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
        }

        body.dark-theme .login-form input::placeholder {
            color: #b8c6ba !important;
        }

        @media (max-width: 1200px) {
            .auth-card {
                width: calc(100% - 80px);
            }

            .auth-menu {
                gap: 22px;
                font-size: 15px;
            }
        }

        @media (max-width: 850px) {
            .auth-topbar {
                flex-direction: column;
                text-align: center;
            }

            .auth-menu {
                justify-content: center;
            }

            .site-tools {
                justify-content: center;
            }

            .auth-card {
                width: calc(100% - 32px);
            }

            .auth-info-card,
            .auth-form-card {
                padding: 26px 22px;
            }

            .login-form button {
                width: 100%;
            }
        }

        @media (max-width: 520px) {
            .auth-logo {
                font-size: 28px;
            }

            .auth-menu {
                font-size: 15px;
                gap: 14px;
            }

            .auth-info-card h2,
            .auth-form-card h1 {
                font-size: 26px;
            }

            .auth-info-card p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<header class="auth-topbar">
    <a href="index.php" class="auth-logo">
        <span>📖</span>
        Biblioteca Online
    </a>

    <nav class="auth-menu">
        <a href="index.php" data-i18n="nav.home">Acasă</a>
        <a href="carti.php" data-i18n="nav.books">Cărți</a>
        <a href="categorii.php?cat=Toate" data-i18n="nav.categories">Categorii</a>
        <a href="despre.php" data-i18n="nav.about">Despre</a>
        <a href="contact.php" data-i18n="nav.contact">Contact</a>
        <a href="inregistrare.php" data-i18n="auth.member">Devino membru</a>
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

<main class="auth-page">

    <section class="auth-card auth-info-card">
        <h2 data-i18n="login.title">Autentificare</h2>

        <p data-i18n="login.text">
            Intră în contul tău pentru a rezerva cărți, a salva volume favorite și a accesa recomandările personalizate.
        </p>
    </section>

    <section class="auth-card auth-form-card">
        <h1 data-i18n="login.formTitle">Intră în cont</h1>

        <?php if ($eroare !== ""): ?>
            <div 
                class="alert" 
                <?php if ($eroareKey !== ""): ?>
                    data-i18n="<?php echo htmlspecialchars($eroareKey); ?>"
                <?php endif; ?>
            >
                <?php echo htmlspecialchars($eroare); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <input
                type="email"
                name="email"
                placeholder="Emailul tău"
                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                data-i18n-placeholder="login.emailPlaceholder"
            >

            <input
                type="password"
                name="parola"
                placeholder="Parola ta"
                data-i18n-placeholder="login.passwordPlaceholder"
            >

            <button type="submit" data-i18n="login.button">Autentifică-te</button>
        </form>

        <p class="register-text">
            <span data-i18n="login.noAccount">Nu ai cont?</span>
            <a href="inregistrare.php" data-i18n="login.createAccount">Creează unul aici</a>
        </p>
    </section>

</main>

<script src="js/script.js?v=13"></script>

</body>
</html>