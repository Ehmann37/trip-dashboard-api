<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';

function handleLoginRequest() {
    $data = sanitizeInput(getRequestBody());

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($data['email'], $data['password'])) {
        respond(400, 'Missing email or password');
        return;
    }

    $email = trim($data['email']);
    $password = trim($data['password']);

    $user = findUserByEmail($email);

    if ($user && password_verify($password, $user['hashed_password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'] ?? '';
        $_SESSION['email'] = $user['email'];
        $_SESSION['company_id'] = $user['company_id'] ?? null;
        $_SESSION['created_at'] = $user['created_at'] ?? null;

        $responseUser = [
            'user_id' => $user['user_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'company_id' => $user['company_id'],
            'created_at' => $user['created_at']
        ];

        respond(200, 'Login successful', $responseUser);
        return;
    }

    respond(200, 'Invalid sign-in credentials');
}

function handleLogout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    respond(200, 'Logged out successfully');
}

function handleSessionCheck() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['user_id'])) {
        $user = [
            'user_id' => $_SESSION['user_id'],
            'name' => $_SESSION['name'] ?? '',
            'email' => $_SESSION['email'],
            'company_id' => $_SESSION['company_id'] ?? null,
            'created_at' => $_SESSION['created_at'] ?? null
        ];

        respond(200, 'User is logged in', $user);
    } else {
        respond(200, 'User is not logged in', ['loggedIn' => false]);
    }
}