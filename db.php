<?php
$host = 'localhost';
$dbname = 'dbqvs4dpuugq83';
$username = 'unuw9ry46la8t';
$password = '4cgdhp7dokz1';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
