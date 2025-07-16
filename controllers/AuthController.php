<?php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../config/db.php';

function handleLoginRequest() {
    global $pdo;

    $data = sanitizeInput(getRequestBody());

    if (empty($data['email']) || empty($data['password'])) {
        respond('01', 'Missing email or password');
        return;
    }

    $email = trim($data['email']);
    $password = trim($data['password']);

    $user = getUserByEmail($email);

    if ($user && password_verify($password, $user['hashed_password'])) {
        $token = generateJWT([
            'sub' => $user['user_id'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);

        $stmt = $pdo->prepare("UPDATE users SET token = :token WHERE user_id = :id");
        $stmt->execute([
            ':token' => $token,
            ':id'    => $user['user_id']
        ]);

        $responseUser = [
            'user_id'    => $user['user_id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'company_id' => $user['company_id'],
            'role'       => $user['role'],
            'created_at' => $user['created_at'],
            'token'      => $token
        ];

        respond('1', 'Login successful', $responseUser);
        return;
    }

    respond('01', 'Invalid sign-in credentials');
}

function handleSessionCheck($token) {
    global $pdo;

    $payload = verifyJWT($token);
    if (!$payload) {
        respond('01', 'Invalid or expired token');
        return;
    }

    $user = getUserByToken($token);
    if (!$user) {
        respond('01', 'Token not found or revoked');
        return;
    }

    $responseUser = [
        'user_id'    => $user['user_id'],
        'name'       => $user['name'],
        'email'      => $user['email'],
        'company_id' => $user['company_id'],
        'role'       => $user['role'],
        'created_at' => $user['created_at']
    ];

    respond('1', 'User is authenticated', $responseUser);
}

function generateJWT(array $payload, int $exp = 86200): string {
    $header = base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
    $iat = time();
    $payload = base64UrlEncode(json_encode($payload + [
        'iat' => $iat,
        'exp' => $iat + $exp
    ]));

    $signature = hash_hmac(
        'sha256',
        "$header.$payload",
        $_ENV['JWT_SECRET'],
        true
    );

    return "$header.$payload." . base64UrlEncode($signature);
}

function verifyJWT(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$header, $payload, $signature] = $parts;
    $expectedSig = base64UrlEncode(hash_hmac('sha256', "$header.$payload", $_ENV['JWT_SECRET'], true));

    if (!hash_equals($expectedSig, $signature)) return null;

    $data = json_decode(base64_decode($payload), true);
    if (!$data || time() > $data['exp']) return null;

    return $data;
}

function base64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}