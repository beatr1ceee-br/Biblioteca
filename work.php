<?php

$mesaj_ziua2 = "Work PHP - Ziua 2: Conexiunea funcționează perfect!";

$numere = [12, 7, 23, 44, 8, 19, 90, 5, 14, 33];

$pare = 0;
$impare = 0;

for ($i = 0; $i < count($numere); $i++) {
    
    if ($numere[$i] % 2 == 0) {
        $pare++; 
    } else {
        $impare++; 
    }
}

$rezultat_text = "Din cele 10 numere verificate, avem: " . $pare . " numere pare și " . $impare . " numere impare.";
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Work PHP - Ziua 3</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 40px; display: flex; flex-direction: column; align-items: center; gap: 20px; }
        .box { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); width: 100%; max-width: 500px; }
        h1 { color: #2c3e50; font-size: 20px; margin-bottom: 15px; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
        p { color: #34495e; line-height: 1.5; }
        .numere-lista { background: #eef2f7; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 15px; margin-bottom: 10px; letter-spacing: 2px; text-align: center; }
        .accent { color: #27ae60; font-weight: bold; }
    </style>
</head>
<body>

    <div class="box">
        <h1>Ziua 2: Mesaj Web & Consolă</h1>
        <p><strong><?php echo $mesaj_ziua2; ?></strong></p>
    </div>

    <div class="box">
        <h1>Ziua 3: Control Flow (if & for)</h1>
        <p>Șirul de numere analizat este:</p>
        <div class="numere-lista">
            <?php echo implode(", ", $numere); ?>
        </div>
        <p class="accent"> <?php echo $rezultat_text; ?></p>
    </div>

</body>
</html>

<?php

echo "<script>";
echo "console.log('--- ZIUA 3 REZULTAT ---');";
echo "console.log('Șirul: [" . implode(", ", $numere) . "]');";
echo "console.log('" . addslashes($rezultat_text) . "');";
echo "</script>";
?>