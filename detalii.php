<?php
// Încărcăm datele din fișierul JSON
$jsonFile = 'data/items.json';
$book = null;

if (isset($_GET['id'])) {
    $bookId = (int)$_GET['id'];
    
    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        $books = json_decode($jsonData, true);
        
        // Căutăm cartea care are ID-ul corespunzător
        foreach ($books as $b) {
            if ((int)$b['id'] === $bookId) {
                $book = $b;
                break;
            }
        }
    }
}

// Dacă nu a fost găsită nicio carte cu acest ID, redirecționăm către pagina principală
if (!$book) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['nume']); ?> - Detalii Carte</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Stiluri specifice pentru pagina de detalii, adăugate pentru integrare rapidă */
        .details-wrapper {
            background-color: var(--bg-white);
            border-radius: 15px;
            padding: 40px;
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }
        .details-img img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .details-content h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 38px;
            color: var(--green-dark);
            margin-bottom: 10px;
        }
        .details-author {
            font-size: 18px;
            color: var(--gold);
            font-style: italic;
            margin-bottom: 25px;
        }
        .details-meta {
            margin-bottom: 25px;
            padding: 15px 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            gap: 30px;
            font-size: 14px;
        }
        .details-description {
            font-size: 15px;
            line-height: 1.7;
            color: var(--text-dark);
            margin-bottom: 30px;
        }
        .btn-back {
            display: inline-block;
            background-color: var(--green-dark);
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <header>
        <div class="container nav-container">
            <div class="logo">📖 Biblioteca Online</div>
            <ul class="nav-links">
                <li><a href="index.php">Acasă</a></li>
                <li><a href="index.php">Cărți</a></li>
                <li><a href="categorii.php">Categorii</a></li>
                <li><a href="#">Despre</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>
    </header>

    <main class="container">
        <div class="details-wrapper">
            <div class="details-img">
                <img src="<?php echo htmlspecialchars($book['imagine']); ?>" alt="<?php echo htmlspecialchars($book['nume']); ?>">
            </div>
            <div class="details-content">
                <h2><?php echo htmlspecialchars($book['nume']); ?></h2>
                <div class="details-author">de <?php echo htmlspecialchars($book['autor']); ?></div>
                
                <div class="details-meta">
                    <div><strong>Secțiune:</strong> <?php echo ucfirst(htmlspecialchars($book['sectiune'])); ?></div>
                    <?php if (isset($book['nota'])): ?>
                        <div><strong>Evaluare:</strong> ⭐ <?php echo htmlspecialchars($book['nota']); ?>/5.0</div>
                    <?php endif; ?>
                </div>

                <p class="details-description">
                    <?php 
                    // Dacă ai descrieri în JSON le afișăm, altfel punem un text placeholder frumos
                    echo htmlspecialchars($book['descriere'] ?? "Această carte remarcabilă scrisă de " . $book['autor'] . " face parte din colecția noastră selectă. Vă invităm să vizitați sediul fizic al bibliotecii noastre pentru a o împrumuta și a vă bucura de o lectură captivantă."); 
                    ?>
                </p>
                
                <a href="index.php" class="btn-back">← Înapoi la catalog</a>
            </div>
        </div>
    </main>

</body>
</html>