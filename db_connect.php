<?php

date_default_timezone_set('Asia/Jakarta');
setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'Indonesian');

$host = 'localhost';
$db_name = 'sistem_lampid_cibungbulang';
$username = 'root';
$password = '';

try {
    $db = new PDO(
        "mysql:host={$host};dbname={$db_name};charset=utf8mb4",
        $username,
        $password
    );
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>