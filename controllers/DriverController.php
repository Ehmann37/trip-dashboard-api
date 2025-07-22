<?php
require_once __DIR__ . '/../models/DriverModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';


function handleGetDriver() {
  $drivers = getAllDrivers();
  
  respond('1', 'Drivers retrieved successfully', $drivers);
}

function handleAddDriver() {
  $data = sanitizeInput(getRequestBody());

  $missing = validateFields($data, ['license_number', 'full_name', 'contact_number']);
  if ($missing) {
    respond('02', 'Missing required fields: ' . implode(', ', $missing));
  }

  $driverAdded = addDriver($data);

  if (!$driverAdded) {
    respond('02', 'Failed to add driver');
  } else {
    respond('1', 'driver added successfully', ['driver_id' => $driverAdded]);
  }
}   