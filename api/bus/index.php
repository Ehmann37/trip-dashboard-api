<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/BusController.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
  case 'GET':
    handleGetBus();
    break;
  
  case 'POST':
    handleAddBus();
    break;

  case 'PUT':
    $queryParams = getQueryParams(['bus_id']);
    handleUpdateBus($queryParams);
    break;
  default:
    respond('02', 'Method Not Allowed');
}
