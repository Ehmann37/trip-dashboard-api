<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = getRequestBody();
        $response = loginController($data);
        http_response_code($response['status']);
        echo json_encode($response['body']);
        break;

    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] === 'logout') {
            session_start();
            $response = logoutController();
        } else {
            session_start();
            $response = sessionCheckController();
        }

        http_response_code($response['status']);
        echo json_encode($response['body']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}