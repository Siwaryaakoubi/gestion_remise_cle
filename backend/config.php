<?php
$host = 'localhost';
$dbname = 'gestion_remise_cle';
$user = 'root'; // Changez si nécessaire
$password = ''; // Changez si nécessaire

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
