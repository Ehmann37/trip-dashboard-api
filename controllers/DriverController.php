<?php
require_once __DIR__ . '/../models/DriverModel.php';
require_once __DIR__ . '/../models/BusModel.php';
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

function handleUpdateDriver() {
  $data = sanitizeInput(getRequestBody());

  $driver_id = $data['driver_id'] ?? null;
  if (!$driver_id) {
    respond('01', 'Missing driver_id');
  }

  $bus_id = $data['bus_id'] ?? null;
  $license_number = $data['license_number'] ?? null;

  if ($license_number && licenseExistsForOtherDriver($license_number, $driver_id)) {
    respond('01', 'License number already in use');
  }

  if ($bus_id && busHasAssigned($bus_id, 'driver_id')) {
    respond('01', 'Bus already has a driver assigned');
  }

  if ($bus_id && isAssignedToAnotherBus($driver_id, $bus_id, 'driver_id')) {
    respond('01', 'Driver is already assigned to another bus. Unassign first.');
  }

  if ($bus_id && !busExists($bus_id)) {
    respond('01', 'Bus does not exist');
  }


  $allowedFields = ['license_number', 'full_name', 'contact_number', 'status'];

  $update = updateDriverInfo($data, $driver_id, $allowedFields);

  if (!$update) {
    respond('01', 'No changes made or failed to update driver');
  } else {
    respond('1', 'Driver updated successfully');
  }
}
