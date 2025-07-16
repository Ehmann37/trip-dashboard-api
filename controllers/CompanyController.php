<?php
require_once __DIR__ . '/../models/CompanyModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';

function handleGetCompany($queryParams) {
  $company_id = $queryParams['company_id'] ?? null;

  if (!companyExists($company_id)) {
    respond('01', 'Invalid company ID');
  }

  $company = getCompanyAnalytics($company_id);

  if (!$company) {
    respond('01', 'No routes found for the specified range');
  }
  
  respond('1', 'Routes retrieved successfully', $company);
}
