<?php
require_once __DIR__ . '/../config/db.php';

function findUserByEmail($email) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT user_id, name, email, hashed_password, company_id, created_at
        FROM users
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}