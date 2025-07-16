<?php
require_once __DIR__ . '/../config/db.php';

function getUserById($user_id) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT user_id, name, email, role FROM users WHERE user_id = :id");
    $stmt->execute([':id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUser(int $user_id, array $fields): bool {
    global $pdo;

    if (empty($fields)) return false;

    $setParts = [];
    $params = [':id' => $user_id];

    foreach ($fields as $column => $value) {
        $paramKey = ":$column";
        $setParts[] = "$column = $paramKey";
        $params[$paramKey] = $value;
    }

    $setClause = implode(', ', $setParts);
    $sql = "UPDATE users SET $setClause WHERE user_id = :id";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function emailExists(string $email, int $excludeUserId): bool {
    global $pdo;

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND user_id != :exclude_id");
    $stmt->execute([
        ':email' => $email,
        ':exclude_id' => $excludeUserId
    ]);

    return $stmt->fetchColumn() > 0;
}

function getHashedPasswordById(int $user_id): ?string {
    global $pdo;

    $stmt = $pdo->prepare("SELECT hashed_password FROM users WHERE user_id = :id");
    $stmt->execute([':id' => $user_id]);
    return $stmt->fetchColumn() ?: null;
}

function validatePasswordChange(int $user_id, string $currentPass, string $newPass): array {
    $currentHashed = getHashedPasswordById($user_id);

    if (!$currentHashed || !password_verify($currentPass, $currentHashed)) {
        return ['success' => false, 'message' => 'Current password is incorrect'];
    }

    if (password_verify($newPass, $currentHashed)) {
        return ['success' => false, 'message' => 'New password must be different from current password'];
    }

    if (strlen($newPass) < 8) {
        return ['success' => false, 'message' => 'New password must be at least 8 characters'];
    }

    

    return ['success' => true, 'hashed_password' => password_hash($newPass, PASSWORD_DEFAULT)];
}