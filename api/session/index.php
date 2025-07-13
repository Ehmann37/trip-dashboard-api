<?php
require_once __DIR__ . '/../../controllers/SessionController.php';
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        handleLoginRequest();
        break;

    case 'GET':
        session_start();

        $action = $_GET['action'] ?? null;

        if ($action === 'logout') {
            handleLogout();
        } else {
            handleSessionCheck();
        }
        break;

    default:
        respond(405, 'Method Not Allowed');
}