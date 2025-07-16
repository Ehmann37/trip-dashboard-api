<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/RouteController.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
  case 'GET':
    $queryParams = getQueryParams(['company_id', 'range', 'start', 'end']);
    handleGetRoute($queryParams);
    break;

  default:
    respond('02', 'Method Not Allowed');
}
