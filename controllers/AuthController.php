<?php
require_once __DIR__ . '/../models/UserModel.php';

function loginController($data) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($data['email'], $data['password'])) {
        return [
            'status' => 400,
            'body' => ['success' => false, 'message' => 'Missing email or password']
        ];
    }

    $email = trim($data['email']);
    $password = trim($data['password']);

    $user = findUserByEmail($email);

    if ($user && password_verify($password, $user['hashed_password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];

        return [
            'status' => 200,
            'body' => [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['user_id'],
                    'email' => $user['email']
                ]
            ]
        ];
    }

    return [
        'status' => 401,
        'body' => ['success' => false, 'message' => 'Invalid email or password']
    ];
}

function logoutController() {
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

    return [
        'status' => 200,
        'body' => ['success' => true, 'message' => 'Logged out']
    ];
}

function sessionCheckController() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['user_id'])) {
        return [
            'status' => 200,
            'body' => [
                'loggedIn' => true,
                'email' => $_SESSION['email']
            ]
        ];
    }

    return [
        'status' => 200,
        'body' => ['loggedIn' => false]
    ];
}