<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getAllDrivers(){
  global $pdo;
  
  $sql = "SELECT * FROM drivers";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($drivers as &$driver){
    $sql = "SELECT bus_id FROM bus where driver_id = :driver_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':driver_id' => $driver['driver_id']
    ]);
    $driver['bus_id'] = $stmt->fetchColumn();
    if ($driver['bus_id'] == false){
      $driver['bus_id'] = null;
    }
  }

  return ($drivers);
}

function getDriverIdByBusId($bus_id): ?int {
  global $pdo;

  $sql = "SELECT driver_id FROM bus WHERE bus_id = :bus_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':bus_id' => $bus_id]);

  return $stmt->fetchColumn() ?: null;
}

function addDriver(array $driverData): int {
  return insertRecord('drivers', $driverData);
}

function updateDriverStatus($driver_id, $status){
  global $pdo;

  $sql = "UPDATE drivers SET status = :status WHERE driver_id = :driver_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':status' => $status, 
    ':driver_id' => $driver_id
  ]);

  return $stmt->rowCount() > 0;
}

function checkDriverIfAssigned($driver_id, $bus_id): bool {
  global $pdo;

  $sql = "SELECT COUNT(*) FROM bus WHERE driver_id = :driver_id and bus_id != :bus_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':driver_id' => $driver_id,
    ':bus_id' => $bus_id
  ]);

  return $stmt->fetchColumn() > 0;
}

function checkDriverExists($driver_id): bool {
  return checkExists('drivers', 'driver_id', $driver_id);
}

function checkIfAnyDriveisAssigned($bus_id) : bool {
  global $pdo;

  $sql = "SELECT COUNT(*) FROM bus WHERE bus_id = :bus_id AND driver_id IS NOT NULL";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':bus_id' => $bus_id]);

  return $stmt->fetchColumn() > 0;
}

function updateDriverInfo($driverData, $driver_id, $allowedFields=[]) {
  $updated = false;

  // echo json_encode($driverData['status']);
  // exit;

  $driverUpdate = updateRecord('drivers', 'driver_id', $driver_id, $driverData, $allowedFields);
  if ($driverUpdate) $updated = true;

  $unset = updateRecordByCondition('bus', ['driver_id' => $driver_id], ['driver_id' => null]);
  if ($unset) $updated = true;


  if (!is_null($driverData['bus_id'])) {
    $busUpdate = updateRecord('bus', 'bus_id', $driverData['bus_id'], ['driver_id' => $driver_id], ['driver_id']);
    if ($busUpdate) $updated = true;
    $status = updateRecord('drivers', 'driver_id', $driver_id, ['status' => 'active'], ['status']);
  } else {
    $status = updateRecord('drivers', 'driver_id', $driver_id, ['status' => 'inactive'], ['status']);
  }

  return $updated;
}

function licenseExistsForOtherDriver($license_number, $exclude_driver_id) {
  global $pdo;
  $stmt = $pdo->prepare("SELECT 1 FROM drivers WHERE license_number = :ln AND driver_id != :id LIMIT 1");
  $stmt->execute([':ln' => $license_number, ':id' => $exclude_driver_id]);
  return $stmt->fetch() !== false;
}

function unassignDriver($driver_id, $bus_id){
  global $pdo;

  $stmt = $pdo->prepare("Update bus SET driver_id = NULL WHERE bus_id = :bus_id AND driver_id = :driver_id");
  $stmt->execute([
    ':bus_id' => $bus_id, 
    ':driver_id' => $driver_id
  ]);
}

function deleteDriver($driver_id){
  global $pdo;
  updateDriverInfo(['bus_id' => NULL, 'driver_id' => $driver_id], $driver_id);
  $stmt = $pdo->prepare("DELETE FROM drivers WHERE driver_id = :driver_id");
  $stmt->execute([':driver_id' => $driver_id]);
  return $stmt->fetch() !== false;
}