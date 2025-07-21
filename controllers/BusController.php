<?php
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';


function handleGetBus() {
  $bus = getAllBus();

  respond('1', 'Conductors retrieved successfully', $bus);
}

function handleAddBus() {
  $data = sanitizeInput(getRequestBody());

  $missing = validateFields($data, ['bus_id', 'company_id']);
  if ($missing) {
    respond('02', 'Missing required fields: ' . implode(', ', $missing));
  }

  $busAdded = addBus($data);



  if (!$busAdded) {
    respond('02', 'Failed to add bus');
  } else {
    respond('1', 'bus added successfully');
  }
}   