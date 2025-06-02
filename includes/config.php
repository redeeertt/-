<?php
session_start();
$host = 'localhost';
$db   = 'music_site';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Включить вывод ошибок
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>