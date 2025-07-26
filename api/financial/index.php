<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/FinancialController.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
  case 'GET':
    $queryParams = getQueryParams(['company_id', 'start_time', 'end_time']);
    handleGetCompany($queryParams);
    break;

  default:
    respond('02', 'Method Not Allowed');
}
