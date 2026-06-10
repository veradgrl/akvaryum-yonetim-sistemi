<?php
//!--------------------------------------
//! config/db.php (Canlı Sunucu Ayarları)
//!--------------------------------------
$host = 'localhost'; // Genellikle 'localhost' veya '127.0.0.1' kalır
$db_name = ' '; 
$username = ' ';   
$password = ' ';     
$charset = ' ';

try {
    $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

/*
$host = 'localhost';
$db_name = 'akvaryum_db';
$username = 'root'; // Hosting alanına taşırken verilen kullanıcı adı yazılacak 
$password = '';     // Hosting alanına taşırken verilen şifre yazılacak 
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
} 
    */



?> 