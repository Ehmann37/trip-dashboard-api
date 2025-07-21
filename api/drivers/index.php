<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/DriverController.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
  case 'GET':
    handleGetDriver();
    break;
  
  case 'POST':
    handleAddDriver();
    break;
  default:
    respond('02', 'Method Not Allowed');
}
