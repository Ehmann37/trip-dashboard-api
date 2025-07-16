<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function companyExists($company_id) {
    return checkExists('bus_companies', 'company_id', $company_id);
}