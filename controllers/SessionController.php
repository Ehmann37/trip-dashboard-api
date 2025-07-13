<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';

function handleLoginRequest() {
    $data = sanitizeInput(getRequestBody());

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($data['email'], $data['password'])) {
        respond(200, 'Missing email or password');
    }

    $email = trim($data['email']);
    $password = trim($data['password']);

    $user = findUserByEmail($email);

    if ($user && password_verify($password, $user['hashed_password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];


        respond(200, 'Login successful', [
            'user' => [
                'id' => $user['user_id'],
                'email' => $user['email']
            ]
        ]);
    }

    respond(200, 'Invalid email or password');
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

    respond(200, 'Logged out successfully', ['success' => true]);
}

function handleSessionCheck() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['user_id'])) {
        respond(200, 'User is logged in', [
            'loggedIn' => true,
            'email' => $_SESSION['email']
        ]);
    } else {
        respond(200, 'User is not logged in', ['loggedIn' => false]);
    }
}