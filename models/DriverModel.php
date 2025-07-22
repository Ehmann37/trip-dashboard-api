<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getAllDrivers(){
  global $pdo;
  
  $sql = "SELECT * FROM drivers";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

function checkDriverIfAssigned($driver_id): bool {
  global $pdo;

  $sql = "SELECT COUNT(*) FROM bus WHERE driver_id = :driver_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':driver_id' => $driver_id]);

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