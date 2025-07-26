<?php
require_once __DIR__ . '/../models/FinancialModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';

function handleGetCompany($queryParams) {
  $company_id = $queryParams['company_id'] ?? null;
  $start_time = $queryParams['start_time'] ?? "2024-01-01 00:00:00";
  $end_time = $queryParams['end_time'] ?? "2026-12-31 23:59:59";
   
  if (!companyExists($company_id)) {
    respond('01', 'Invalid company ID');
  }

  if ($start_time == null && $end_time == null){
    respond('01', 'Please provide a starting and ending time.');
  }


  $company = getCompanyAnalytics($company_id, $start_time, $end_time);

  if (!$company) {
    respond('01', 'No routes found for the specified range');
  }
  
  respond('1', 'Routes retrieved successfully', $company);
}
