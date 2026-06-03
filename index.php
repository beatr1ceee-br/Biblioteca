<?php
// Încărcăm datele din fișierul JSON
$jsonFile = 'data/items.json';
$books = [];

if (file_exists($jsonFile)) {
    $jsonData = file_get_contents($jsonFile);
    $books = json_decode($jsonData, true);
}

// Funcție utilă pentru filtrarea cărților pe secțiuni
$recomandate = array_filter($books, function($b) { return $b['sectiune'] === 'recomandate'; });
$pentruTine = array_filter($books, function($b) { return $b['sectiune'] === 'pentru_tine'; });
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Online</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header>
        <div class="container nav-container">
            <div class="logo">
                📖 Biblioteca Online
            </div>
            <ul class="nav-links">
                <li><a href="#" class="active">Acasă</a></li>
                <li><a href="#">Cărți</a></li>
                <li><a href="#">Categorii</a></li>
                <li><a href="#">Despre</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
            <div class="nav-search-bar">
                <input type="text" placeholder="Caută cărți, autori, categorii...">
            </div>
            <div class="nav-actions">
                <a href="#" class="btn-auth">Autentificare</a>
                <a href="#" class="btn-member">Devino membru</a>
            </div>
        </div>
    </header>

    <section class="hero-section class container">
        <div class="hero-banner">
            <div class="hero-content">
                <h2>Bine ai venit la Biblioteca Online</h2>
                <p>Descoperă cărți extraordinare, cunoaștere valoroasă și bucuria lecturii în fiecare zi.</p>
                <div class="hero-buttons">
                    <a href="#" class="btn-primary">Explorează cărțile</a>
                    <a href="#" class="btn-secondary">Devino membru</a>
                </div>
            </div>
        </div>

        <div class="search-box-floating">
            <div style="font-size: 13px; font-weight:600; color:var(--green-dark);">Găsește cartea potrivită pentru tine</div>
            <div class="search-input-wrapper">
                <input type="text" placeholder="Caută după titlu, autor sau cuvânt cheie...">
            </div>
            <button class="btn-search">Caută</button>
        </div>
    </section>

    <main class="container main-layout">
        
        <div class="content-area">
            
            <section>
                <div class="section-header">
                    <h3>Cărți recomandate</h3>
                    <a href="#" class="view-all">Vezi toate →</a>
                </div>
                <div class="books-row">
                    <?php foreach ($recomandate as $carte): ?>
                        <div class="book-card">
                            <img src="<?php echo htmlspecialchars($carte['imagine']); ?>" alt="<?php echo htmlspecialchars($carte['nume']); ?>">
                            <div class="book-title"><?php echo htmlspecialchars($carte['nume']); ?></div>
                            <div class="book-author"><?php echo htmlspecialchars($carte['autor']); ?></div>
                           <a href="detalii.php?id=<?php echo $carte['id']; ?>" class="btn-card-details">Detalii</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="recommendations-list">
                <div class="section-header">
                    <h3>Recomandările noastre pentru tine</h3>
                    <a href="#" class="view-all">Vezi toate →</a>
                </div>
                <div class="books-list-vertical">
                    <?php foreach ($pentruTine as $carte): ?>
                        <div class="book-item-horizontal">
                            <img src="<?php echo htmlspecialchars($carte['imagine']); ?>" alt="<?php echo htmlspecialchars($carte['nume']); ?>">
                            <div class="book-info">
                                <div>
                                    <div class="book-title"><?php echo htmlspecialchars($carte['nume']); ?></div>
                                    <div class="book-author"><?php echo htmlspecialchars($carte['autor']); ?></div>
                                </div>
                                <div class="rating">⭐⭐⭐⭐⭐ <?php echo htmlspecialchars($carte['nota'] ?? '5.0'); ?></div>
                                <a href="#" class="btn-card-details" style="align-self: flex-start; margin-top: 5px;">Detalii</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>

        <aside class="sidebar">
            
    <div>
        <div class="section-header">
            <h3>Categorii populare</h3>
            <a href="categorii.php?cat=Toate" class="view-all">Vezi toate →</a>
        </div>
        <div class="categories-grid">
            <?php
            // Definim categoriile noastre și emoji-urile corespunzătoare
            $categoriiConfig = [
                'Fictiune' => '📚 Ficțiune',
                'Dezvoltare' => '🌱 Dezvoltare personală',
                'Istorie' => '🏛️ Istorie'
            ];

            // Generăm dinamic fiecare card de categorie
            foreach ($categoriiConfig as $cheieCat => $numeAfisat): 
                // Numărăm câte cărți din JSON aparțin acestei categorii
                $numarCarti = count(array_filter($books, function($b) use ($cheieCat) {
                    return isset($b['categorie']) && $b['categorie'] === $cheieCat;
                }));
            ?>
                <a href="categorii.php?cat=<?php echo $cheieCat; ?>" class="category-card" style="display: flex; text-decoration: none; color: inherit;">
                    <span class="category-title"><?php echo $numeAfisat; ?></span>
                    <span class="category-count"><?php echo $numarCarti; ?> <?php echo ($numarCarti == 1) ? 'carte' : 'cărți'; ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

            <div class="member-box">
                <h4>Devino membru</h4>
                <p>Alătură-te comunității noastre de cititori și bucură-te de beneficii exclusive:</p>
                <ul>
                    <li>✓ Acces la mii de cărți online</li>
                    <li>✓ Reduceri și oferte speciale</li>
                    <li>✓ Recomandări personalizate</li>
                </ul>
                <a href="#" class="btn-join-now">Înscrie-te acum</a>
            </div>

        </aside>
    </main>

    <footer>
        <div class="container footer-grid">
            <div class="footer-col">
                <div class="logo" style="margin-bottom: 15px;">Biblioteca Online</div>
                <p style="font-size: 12px; opacity:0.7;">Locul unde fiecare carte deschide o nouă lume.</p>
            </div>
            <div class="footer-col">
                <h5>Contact</h5>
                <ul>
                    <li>📍 Str. Calea Ieșilor nr. 16</li>
                    <li>✉️ contact@biblioteca.md</li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Linkuri rapide</h5>
                <ul>
                    <li>Acasă</li>
                    <li>Cărți</li>
                    <li>Categorii</li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Informații utile</h5>
                <ul>
                    <li>Termeni și condiții</li>
                    <li>Politică confidențialitate</li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Abonează-te la noutăți</h5>
                <form class="newsletter-form" onsubmit="event.preventDefault();">
                    <input type="email" placeholder="Adresa ta de email...">
                    <button type="submit">Abonează-mă</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Biblioteca Online. Toate drepturile rezervate.</p>
        </div>
    </footer>

</body>
</html>