<?php
require_once __DIR__ . '/../../controllers/UsersController.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';
require_once __DIR__ . '/../middleware.php';

checkAuthorization();
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'PUT':
        handleUpdateProfile();
        break;

    default:
        respond('02', 'Method Not Allowed');
}