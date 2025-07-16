<?php
require_once __DIR__ . '/../models/RouteModel.php';
require_once __DIR__ . '/../models/CompanyModel.php';

require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';

function handleGetRoute($queryParams) {
  $company_id = $queryParams['company_id'] ?? null;
  $range = $queryParams['range'] ?? null;
  $start = $queryParams['start'] ?? null;
  $end = $queryParams['end'] ?? null;

  if (!companyExists($company_id)) {
    respond('01', 'Invalid company ID');
  }

  if ($start && $end) {
    $routes = getCurrentWeekRevenueByCompany($company_id, $start, $end);

    if (!$routes) {
      respond('01', 'No routes found for the specified date range');
    }
  } else{
    
    $routes = getCurrentWeekRevenueByCompany($company_id);

    if (!$routes) {
      respond('01', 'No routes found for the specified range');
    }
  }
  respond('1', 'Routes retrieved successfully', $routes);
}
