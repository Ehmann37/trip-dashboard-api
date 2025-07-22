<?php
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../models/DriverModel.php';
require_once __DIR__ . '/../models/ConductorModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';


function handleGetBus() {
  $bus = getAllBus();

  respond('1', 'Buses retrieved successfully', $bus);
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

function handleUpdateBus($queryParams) {
  $data = sanitizeInput(getRequestBody());

  $bus_id = $queryParams['bus_id'] ?? null;
  if ($bus_id === null) {
    respond('02', 'Missing bus_id in query parameters');
  }

  $conductor_id = $data['conductor_id'] ?? null;
  $driver_id = $data['driver_id'] ?? null;

  if ($driver_id !== null) {
    if (checkDriverIfAssigned($driver_id)){
      respond('02', 'Driver is already assigned to a bus');
    }
    if (!checkDriverExists($driver_id)) {
      respond('02', 'Driver does not exist');
    }

    $previousDriverId = getDriverIdByBusId($bus_id) ?? null;
    if (!is_null($previousDriverId)) {
      updateDriverStatus($previousDriverId, 'inactive');
    }
    
    if (!is_null($driver_id)) {
      updateDriverStatus($driver_id, 'active');
    }

  }

  if ($conductor_id !== null){
    if (checkConductorIfAssigned($conductor_id)){
      respond('02', 'Conductor is already assigned to a bus');
    }
    if (!checkConductorExists($conductor_id)) {
      respond('02', 'Conductor does not exist');
    }

    $previousConductorId = getConductorIdByBusId($bus_id) ?? null;
    if (!is_null($previousConductorId)) {
      updateConductorStatus($previousConductorId, 'inactive');
    }
    
    if (!is_null($conductor_id)) {
      updateConductorStatus($conductor_id, 'active');
    }
  }


  $busUpdated = updateBus($data, $bus_id, ['status', 'route_id', 'conductor_id', 'driver_id']);

  if (!$busUpdated) {
    respond('02', 'Failed to update bus');
  } else {
    respond('1', 'Bus updated successfully');
  }
}