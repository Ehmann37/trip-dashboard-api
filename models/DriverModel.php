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

function addDriver(array $driverData): int {
  return insertRecord('drivers', $driverData);
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