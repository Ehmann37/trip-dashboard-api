<?php
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../models/DriverModel.php';
require_once __DIR__ . '/../models/ConductorModel.php';
require_once __DIR__ . '/../models/RouteModel.php';
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
    respond('01', 'Missing required fields: ' . implode(', ', $missing));
  }

  $bus_id = $data['bus_id'];
  if (busExists($bus_id)){
    respond('01', 'A bus with bus id ' .$bus_id. ' already exist.');
  }

  $busAdded = addBus($data);

  if (!$busAdded) {
    respond('01', 'Failed to add bus.');
  } else {
    respond('1', 'bus added successfully.');
  }
}   

function handleUpdateBus() {
  $data = sanitizeInput(getRequestBody());

  $bus_id = $data['bus_id'] ?? null;
  if ($bus_id === null) {
    respond('01', 'Missing bus_id in query parameters');
  }

  $conductor_id = $data['conductor_id'] ?? null;
  $driver_id = $data['driver_id'] ?? null;
  $route_id = $data['route_id'] ?? null;

  if ($driver_id !== null) {
    if (checkDriverIfAssigned($driver_id, $bus_id)){
      respond('01', 'Driver is already assigned to a bus');
    }
    if (!checkDriverExists($driver_id)) {
      respond('01', 'Driver does not exist');
    }

    if ($bus_id && busHasAssigned($bus_id, 'driver_id')  && getDriverIdByBusId($bus_id) != $driver_id)  {
      updateDriverInfo(['bus_id' => NULL, 'driver_id' => getDriverIdByBusId($bus_id)], getDriverIdByBusId($bus_id));
    }
    
    if (!is_null($driver_id)) {
      updateDriverStatus($driver_id, 'active');
    }

  }

  if ($conductor_id !== null){
    if (checkConductorIfAssigned($conductor_id, $bus_id)){
      respond('01', 'Conductor is already assigned to a bus');
    }
    if (!checkConductorExists($conductor_id)) {
      respond('01', 'Conductor does not exist');
    }

    if (busHasAssigned($bus_id, 'conductor_id') && getConductorIdByBusId($bus_id) != $conductor_id) {
      updateConductorInfo(['bus_id' => NULL, 'conductor_id' => getConductorIdByBusId($bus_id)], getConductorIdByBusId($bus_id));
    }
    
    if (!is_null($conductor_id)) {
      updateConductorStatus($conductor_id, 'active');
    }
  }

  if ($route_id !== null){
    if (!checkRouteExists($route_id)) {
      respond('01', 'Route not found');
    }
  }

  $allowedFields = ['status', 'route_id', 'conductor_id', 'driver_id', 'next_maintenance'];
  $busUpdated = updateBus($data, $bus_id, $allowedFields);

  if (!$busUpdated) {
    respond('01', 'Failed to update bus or no changes made');
  } else {
    respond('1', 'Bus updated successfully');
  }
}

function handleDeleteBus($queryParams) {
  $bus_id = $queryParams['bus_id'];

  $deleted = deleteBus($bus_id);
  respond('1', 'Successfully deleted bus.');
}