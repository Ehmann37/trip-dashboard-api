<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getAllConductors(){
  global $pdo;
  
  $sql = "SELECT * FROM users WHERE role = 'conductor'";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $conductors = $stmt->fetchAll(PDO::FETCH_ASSOC);


  foreach ($conductors as &$conductor) {
    $conductor['conductor_id'] = intval($conductor['user_id']);

    unset($conductor['hashed_password'], $conductor['company_id'], $conductor['created_at'], $conductor['token'], $conductor['role'], $conductor['user_id']);

    $sql = "SELECT bus_id FROM bus WHERE conductor_id = :conductor_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':conductor_id' => $conductor['conductor_id']]);

    $bus_id = $stmt->fetchColumn();
    
    if ($bus_id) {
      $conductor['status'] = 'active';
      $conductor['bus_id'] = $bus_id;
    } else {
      $conductor['status'] = 'inactive';
      $conductor['bus_id'] = null;
    }
  }
  return $conductors;
}

function getConductorIdByBusId($bus_id): ?int {
  global $pdo;

  $sql = "SELECT conductor_id FROM bus WHERE bus_id = :bus_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':bus_id' => $bus_id]);

  return $stmt->fetchColumn() ?: null;
}

function addConductor(array $conductorData): int {
  $conductorData['hashed_password'] = password_hash('123123123', PASSWORD_DEFAULT); 
  $conductorData['role'] = 'conductor';
  $conductorData['created_at'] = date('Y-m-d H:i:s');

  $conductor_id = insertRecord('users', $conductorData);
  return insertRecord('conductors', [
    'conductor_id' => $conductor_id 
  ]);
}

function updateConductorStatus($conductor_id, $status){
  global $pdo;

  $sql = "UPDATE conductors SET status = :status WHERE conductor_id = :conductor_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':status' => $status,
    ':conductor_id' => $conductor_id
  ]);

  return $stmt->rowCount() > 0;
}

function updateConductorInfo($conductorData, $conductor_id, $allowedUserFields = []) {
  $updated = false;

  $dataConductor = [];
  if (isset($conductorData['bus_id']) && !is_null($conductorData['bus_id'])) {
    $dataConductor['status'] = 'active';
  }

  

  $userUpdate = updateRecord('users', 'user_id', $conductor_id, $conductorData, $allowedUserFields);
  if ($userUpdate) $updated = true;
  

  if (!empty($dataConductor)) {
    $conductorUpdate = updateRecord('conductors', 'conductor_id', $conductor_id, $dataConductor, ['status']);
    if ($conductorUpdate) $updated = true;
    
  }

  $unset = updateRecordByCondition('bus', ['conductor_id' => $conductor_id], ['conductor_id' => null]);
  if ($unset) $updated = true;
  
  // if (!is_null($conductorData['bus_id'])) {
  //   $assign = updateRecord('bus', 'bus_id', $conductorData['bus_id'], ['conductor_id' => $conductor_id], ['conductor_id']);
  //   if ($assign) $updated = true;
  //   echo json_encode($assign);
  //   exit;
  //   $status = updateRecord('conductors', 'conductor_id', $conductor_id, ['status' => 'active'], ['status']);
  //   if ($status) $update = true;
  // } else {
  //   $status = updateRecord('conductors', 'conductor_id', $conductor_id, ['status' => 'inactive'], ['status']);
  //   if ($status) $update = true;
  // }

  if (!is_null($conductorData['bus_id'])) {
    $busUpdate = updateRecord('bus', 'bus_id', $conductorData['bus_id'], ['conductor_id' => $conductor_id], ['conductor_id']);
    if ($busUpdate) $updated = true;
    
    $status = updateRecord('conductors', 'conductor_id', $conductor_id, ['status' => 'active'], ['status']);
  } else {
    $status = updateRecord('conductors', 'conductor_id', $conductor_id, ['status' => 'inactive'], ['status']);
  }

  return $updated;
}


function checkConductorIfAssigned($conductor_id, $bus_id): bool {
  global $pdo;
  
  $sql = "SELECT COUNT(*) FROM bus WHERE conductor_id = :conductor_id and bus_id != :bus_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':conductor_id' => $conductor_id,
    ':bus_id' => $bus_id
  ]);

  return $stmt->fetchColumn() > 0;
}

function checkConductorExists($conductor_id): bool {
  return checkExists('conductors', 'conductor_id', $conductor_id);
}

function unassignConductor($conductor_id, $bus_id){
  global $pdo;

  $stmt = $pdo->prepare("Update bus SET conductor_id = NULL WHERE bus_id = :bus_id AND conductor_id = :conductor_id");
  $stmt->execute([
    ':bus_id' => $bus_id, 
    ':conductor_id' => $conductor_id
  ]);
}

function deleteConductor($conductor_id){
  global $pdo;

  updateConductorInfo(['bus_id' => NULL, 'conductor_id' => $conductor_id], $conductor_id);

  $stmt = $pdo->prepare("DELETE FROM conductors WHERE conductor_id = :conductor_id");
  $stmt->execute([':conductor_id' => $conductor_id]);

  $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :conductor_id");
  $stmt->execute([':conductor_id' => $conductor_id]);
  return $stmt->fetch() !== false;
}

function getBusIdByConductorId($conductor_id): ?int {
  global $pdo;

  $sql = "SELECT bus_id FROM bus WHERE conductor_id = :conductor_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':conductor_id' => $conductor_id]);

  return $stmt->fetchColumn() ?: null;
}

function getDriverInfobyConductorId($conductor_id) {
  global $pdo;

  $sql = "SELECT driver_id FROM bus WHERE conductor_id = :conductor_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':conductor_id' => $conductor_id]);

  $driver_id = $stmt->fetchColumn() ?: null;

  $sql = "SELECT full_name FROM drivers WHERE driver_id = :driver_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':driver_id' => $driver_id]);

  $driver_name = $stmt->fetchColumn() ?: null;

  return ([
    'driver_id' => $driver_id,
    'driver_name' => $driver_name
  ]);
}

function checkConductorIfActive($conductor_id): bool {
  global $pdo;

  $sql = "SELECT COUNT(*) FROM conductors WHERE conductor_id = :conductor_id AND status = 'active'";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':conductor_id' => $conductor_id]);

  return $stmt->fetchColumn() > 0;
} 