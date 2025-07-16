<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        handleLoginRequest();
        break;

    case 'GET':
        session_start();

        $action = $_GET['token'] ?? null;

        if ($action) {
            handleSessionCheck($action);

        } else {
            respond('01', "hehe");

        }
        break;

    default:
        respond('02', 'Method Not Allowed');
}