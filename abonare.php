<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preluăm și curățăm adresa de email pentru siguranță
    $email = filter_input(INPUT_POST, 'email_utilizator', FILTER_SANITIZE_EMAIL);

    // Aici, în mod normal, emailul s-ar salva într-o bază de date sau într-un fișier text log.
    // Pentru acest stadiu al proiectului, simulăm o înregistrare cu succes.
    $succes = false;
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $succes = true;
    }
} else {
    // Dacă pagina este accesată direct, trimitem utilizatorul înapoi la index
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmare Abonare</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .message-box {
            max-width: 500px;
            margin: 100px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-top: 4px solid var(--green-dark);
        }
        .message-box h2 {
            font-family: 'Cormorant Garamond', serif;
            color: var(--green-dark);
            font-size: 28px;
            margin-bottom: 15px;
        }
        .message-box p {
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 25px;
            line-height: 1.6;
        }
    </style>
</head>
<body>

    <div class="message-box">
        <?php if ($succes): ?>
            <h2>Abonare reușită! 🎉</h2>
            <p>Adresa ta de email <strong><?php echo htmlspecialchars($email); ?></strong> a fost adăugată cu succes în comunitatea noastră. Vei primi cele mai noi recomandări literare direct în inbox.</p>
        <?php else: ?>
            <h2>Eroare la procesare ❌</h2>
            <p>Adresa de email introdusă nu pare să fie validă. Te rugăm să încerci din nou.</p>
        <?php endif; ?>
        <a href="index.php" class="btn-primary" style="display:inline-block;">Revino la prima pagină</a>
    </div>

</body>
</html>