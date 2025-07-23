<?php
require_once __DIR__ . '/../models/ConductorModel.php';
require_once __DIR__ . '/../models/UsersModel.php';
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';
require_once __DIR__ . '/../models/UsersModel.php';

function handleGetConductor() {
  $conductors = getAllConductors();
  
  respond('1', 'Conductors retrieved successfully', $conductors);
}

function handleAddConductor() {
  $data = sanitizeInput(getRequestBody());

  $missing = validateFields($data, ['name', 'email', 'contact_number']);
  if ($missing) {
    respond('01', 'Missing required fields: ' . implode(', ', $missing));
  }

  if (emailCannot($data['email'])) {
    respond('01', 'Email already in use');
    return;
  }

  $conductorAdded = addConductor($data);

  if (!$conductorAdded) {
    respond('01', 'Failed to add conductor');
  } else {
    respond('1', 'Conductor added successfully', ['conductor_id' => $conductorAdded]);
  }
}   


function handleUpdateConductor() {
  $data = sanitizeInput(getRequestBody());

  $conductor_id = $data['conductor_id'] ?? null;
  if (!$conductor_id) {
    respond('01', 'Missing conductor_id');
  }

  $email = $data['email'] ?? null;
  $bus_id = $data['bus_id'] ?? null;

  if ($email && emailExists($email, $conductor_id)) {
    respond('01', 'Email already in use by another user');
  }

  if ($bus_id !== null) {
    if (!busExists($bus_id)) {
      respond('01', 'Bus does not exist');
    }

    if (busHasAssigned($bus_id, 'conductor_id')) {
      respond('01', 'Bus already has a conductor assigned');
    }

    if (isAssignedToAnotherBus($conductor_id, $bus_id, 'conductor_id')) {
      respond('01', 'Conductor is already assigned to another bus. Unassign first.');
    }
  }

  $allowedFields = ['name', 'email', 'contact_number'];
  $update = updateConductorInfo($data, $conductor_id, $allowedFields);

  if (!$update) {
    respond('01', 'No changes made or failed to update conductor');
  } else {
    respond('1', 'Conductor updated successfully');
  }
}
