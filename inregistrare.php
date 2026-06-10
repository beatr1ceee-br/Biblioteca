<?php
session_start();

$eroare = "";
$succes = "";
$eroareKey = "";
$succesKey = "";

$dataDir = __DIR__ . "/data";
$usersFile = $dataDir . "/users.json";

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

if (!file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nume = trim($_POST["nume"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $parola = trim($_POST["parola"] ?? "");
    $confirmaParola = trim($_POST["confirma_parola"] ?? "");

    if ($nume === "" || $email === "" || $parola === "" || $confirmaParola === "") {
        $eroare = "Completați toate câmpurile!";
        $eroareKey = "register.error.empty";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $eroare = "Introduceți o adresă de email validă!";
        $eroareKey = "register.error.email";
    } elseif (strlen($parola) < 6) {
        $eroare = "Parola trebuie să conțină minimum 6 caractere!";
        $eroareKey = "register.error.passwordLength";
    } elseif ($parola !== $confirmaParola) {
        $eroare = "Parolele nu coincid!";
        $eroareKey = "register.error.passwordMatch";
    } else {
        $users = json_decode(file_get_contents($usersFile), true);

        if (!is_array($users)) {
            $users = [];
        }

        foreach ($users as $user) {
            if (isset($user["email"]) && strtolower($user["email"]) === strtolower($email)) {
                $eroare = "Acest email este deja înregistrat!";
                $eroareKey = "register.error.exists";
                break;
            }
        }

        if ($eroare === "") {
            $users[] = [
                "id" => time(),
                "nume" => $nume,
                "email" => $email,
                "parola" => password_hash($parola, PASSWORD_DEFAULT),
                "rol" => "utilizator",
                "data_inregistrare" => date("Y-m-d H:i:s")
            ];

            file_put_contents(
                $usersFile,
                json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            $succes = "Contul a fost creat cu succes! Te poți autentifica.";
            $succesKey = "register.success.created";

            $_POST = [];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare - Biblioteca Online</title>

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
            margin-bottom: 8px;
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
        }

        .alert-error {
            background: #fff0ee;
            color: #a33a2e;
            border: 1px solid #e5b6ae;
        }

        .alert-success {
            background: #eef8ef;
            color: #236b35;
            border: 1px solid #a8d8b3;
        }

        .register-form {
            display: flex;
            flex-direction: column;
            gap: 26px;
        }

        .register-form input {
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

        .register-form input::placeholder {
            color: var(--text-muted);
        }

        .register-form input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 4px rgba(196, 162, 132, 0.13);
        }

        .register-form button {
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

        .register-form button:hover {
            background: var(--green-dark);
        }

        .login-text {
            margin-top: 24px;
            font-size: 17px;
            color: var(--text-dark);
        }

        .login-text a {
            color: var(--green-dark);
            font-weight: 800;
        }

        .login-text a:hover {
            color: var(--gold);
        }

        body.dark-theme .auth-topbar,
        body.dark-theme .auth-card {
            background: #18231b !important;
            color: #eef5ef !important;
        }

        body.dark-theme .auth-menu a,
        body.dark-theme .auth-info-card p,
        body.dark-theme .login-text {
            color: #b8c6ba !important;
        }

        body.dark-theme .auth-logo,
        body.dark-theme .auth-info-card h2,
        body.dark-theme .auth-form-card h1,
        body.dark-theme .login-text a {
            color: #d7ad63 !important;
        }

        body.dark-theme .register-form input {
            background: #111a14 !important;
            color: #eef5ef !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
        }

        body.dark-theme .register-form input::placeholder {
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

            .register-form button {
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
        <a href="autentificare.php" data-i18n="auth.login">Autentificare</a>
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
        <h2 data-i18n="register.infoTitle">Biblioteca Online</h2>

        <p data-i18n="register.infoText1">
            Creează un cont pentru a avea acces la rezervări, cărți favorite și recomandări personalizate.
        </p>

        <p data-i18n="register.infoText2">
            Platforma îți permite să găsești mai ușor cărțile dorite și să le salvezi pentru lectură.
        </p>
    </section>

    <section class="auth-card auth-form-card">
        <h1 data-i18n="register.title">Înregistrare</h1>

        <?php if ($eroare !== ""): ?>
            <div 
                class="alert alert-error"
                <?php if ($eroareKey !== ""): ?>
                    data-i18n="<?php echo htmlspecialchars($eroareKey); ?>"
                <?php endif; ?>
            >
                <?php echo htmlspecialchars($eroare); ?>
            </div>
        <?php endif; ?>

        <?php if ($succes !== ""): ?>
            <div 
                class="alert alert-success"
                <?php if ($succesKey !== ""): ?>
                    data-i18n="<?php echo htmlspecialchars($succesKey); ?>"
                <?php endif; ?>
            >
                <?php echo htmlspecialchars($succes); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="register-form">
            <input
                type="text"
                name="nume"
                placeholder="Numele tău"
                value="<?php echo htmlspecialchars($_POST['nume'] ?? ''); ?>"
                data-i18n-placeholder="register.namePlaceholder"
            >

            <input
                type="email"
                name="email"
                placeholder="Emailul tău"
                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                data-i18n-placeholder="register.emailPlaceholder"
            >

            <input
                type="password"
                name="parola"
                placeholder="Parolă"
                data-i18n-placeholder="register.passwordPlaceholder"
            >

            <input
                type="password"
                name="confirma_parola"
                placeholder="Confirmă parola"
                data-i18n-placeholder="register.confirmPasswordPlaceholder"
            >

            <button type="submit" data-i18n="register.button">Creează contul</button>
        </form>

        <p class="login-text">
            <span data-i18n="register.hasAccount">Ai deja cont?</span>
            <a href="autentificare.php" data-i18n="register.loginHere">Autentifică-te aici</a>
        </p>
    </section>

</main>

<script src="js/script.js?v=14"></script>

</body>
</html>