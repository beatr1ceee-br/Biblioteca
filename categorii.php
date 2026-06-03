<?php
$jsonFile = 'data/items.json';
$books = [];
$categorieSelectata = $_GET['cat'] ?? 'Toate';

if (file_exists($jsonFile)) {
    $jsonData = file_get_contents($jsonFile);
    $books = json_decode($jsonData, true);
}

// Filtrare cărți după categorie dacă este selectată una anume
if ($categorieSelectata !== 'Toate') {
    $books = array_filter($books, function($b) use ($categorieSelectata) {
        return isset($b['categorie']) && $b['categorie'] === $categorieSelectata;
    });
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorii Literare</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header>
        <div class="container nav-container">
            <div class="logo">📖 Biblioteca Online</div>
            <ul class="nav-links">
                <li><a href="index.php">Acasă</a></li>
                <li><a href="index.php">Cărți</a></li>
                <li><a href="categorii.php" class="active">Categorii</a></li>
                <li><a href="#">Despre</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>
    </header>

    <main class="container" style="padding: 40px 0;">
        <h2 style="font-family:'Cormorant Garamond', serif; font-size:32px; color:var(--green-dark); margin-bottom:30px;">
            Filtrare după Categorie: <?php echo htmlspecialchars($categorieSelectata); ?>
        </h2>
        
        <div style="display:flex; gap:15px; margin-bottom:40px;">
            <a href="categorii.php?cat=Toate" class="btn-card-details">Toate</a>
            <a href="categorii.php?cat=Fictiune" class="btn-card-details">Ficțiune</a>
            <a href="categorii.php?cat=Istorie" class="btn-card-details">Istorie</a>
            <a href="categorii.php?cat=Dezvoltare" class="btn-card-details">Dezvoltare Personală</a>
        </div>

        <div class="books-list-vertical" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            <?php if(!empty($books)): ?>
                <?php foreach ($books as $carte): ?>
                    <div class="book-item-horizontal" style="align-items: center;">
                        <img src="<?php echo htmlspecialchars($carte['imagine']); ?>" alt="..." style="width:70px; height:100px;">
                        <div class="book-info">
                            <div>
                                <div class="book-title"><?php echo htmlspecialchars($carte['nume']); ?></div>
                                <div class="book-author"><?php echo htmlspecialchars($carte['autor']); ?></div>
                            </div>
                            <a href="detalii.php?id=<?php echo $carte['id']; ?>" class="btn-card-details" style="margin-top:10px;">Vezi Detalii</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nu există cărți disponibile în această categorie momentan.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>