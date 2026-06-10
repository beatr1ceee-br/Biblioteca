<?php
session_start();

$dataDir = __DIR__ . "/data";
$abonariFile = $dataDir . "/abonari.json";

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

if (!file_exists($abonariFile)) {
    file_put_contents($abonariFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$email = trim($_POST["email"] ?? "");

if ($email === "") {
    header("Location: index.php?abonare=eroare");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: index.php?abonare=eroare");
    exit;
}

$abonari = json_decode(file_get_contents($abonariFile), true);

if (!is_array($abonari)) {
    $abonari = [];
}

foreach ($abonari as $abonare) {
    if (
        isset($abonare["email"]) &&
        strtolower($abonare["email"]) === strtolower($email)
    ) {
        header("Location: index.php?abonare=exista");
        exit;
    }
}

$abonari[] = [
    "id" => time(),
    "email" => $email,
    "data_abonare" => date("Y-m-d H:i:s"),
    "status" => "activ"
];

file_put_contents(
    $abonariFile,
    json_encode($abonari, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

header("Location: index.php?abonare=succes");
exit;
?>