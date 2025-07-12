<?php
require_once __DIR__ . '/../config/db.php';

function findUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    return $stmt->fetch();
}