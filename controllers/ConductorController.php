<?php
require_once __DIR__ . '/../models/ConductorModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';


function handleGetConductor() {
  $conductors = getAllConductors();
  
  respond('1', 'Routes retrieved successfully', $company);
}

function handleAddConductor() {
  $data = sanitizeInput(getRequestBody());

  $missing = validateFields($data, ['name', 'email', 'contact_number']);
  if ($missing) {
    respond('02', 'Missing required fields: ' . implode(', ', $missing));
  }

  $conductorAdded = addConductor($data);

  if (!$conductorAdded) {
    respond('02', 'Failed to add conductor');
  } else {
    respond('1', 'Conductor added successfully', ['conductor_id' => $conductorAdded]);
  }
}   