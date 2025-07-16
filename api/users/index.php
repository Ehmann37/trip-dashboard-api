<?php
require_once __DIR__ . '/../../controllers/UsersController.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetProfile();
        break;

    case 'PUT':
        handleUpdateProfile();
        break;

    default:
        respond('02', 'Method Not Allowed');
}