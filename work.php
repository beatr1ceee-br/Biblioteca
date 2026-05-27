<?php
$mesaj = "Antrenament PHP - Ziua 1: Conexiunea funcționează perfect!";

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Antrenament PHP</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 40px; text-align: center; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: inline-block; }
        h1 { color: #2c3e50; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Rezultat în Aplicația Web:</h1>
        <p><strong><?php echo $mesaj; ?></strong></p>
    </div>
</body>
</html>

<?php
echo "<script>console.log('Mesaj din PHP în consolă: " . addslashes($mesaj) . "');</script>";
?>