<?php
require_once __DIR__ . '/../config/db.php';

function getUserByEmail($email) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT user_id, name, email, hashed_password, company_id, role, created_at
        FROM users
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserByToken($token) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT user_id, name, email, company_id, role, created_at
        FROM users
        WHERE token = ?
        LIMIT 1
    ");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUserToken(int $user_id, string $token): bool {
    global $pdo;

    $stmt = $pdo->prepare("UPDATE users SET token = :token WHERE user_id = :id");
    return $stmt->execute([
        ':token' => $token,
        ':id'    => $user_id
    ]);
}