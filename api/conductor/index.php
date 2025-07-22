<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/ConductorController.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
  case 'GET':
    handleGetConductor();
    break;
  
  case 'POST':
    handleAddConductor();
    break;

  case 'PUT':
    handleUpdateConductor();
    break;
  default:
    respond('02', 'Method Not Allowed');
}
