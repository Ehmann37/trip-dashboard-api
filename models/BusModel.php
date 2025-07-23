<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getAllBus(){
  global $pdo;
  
  $sql = "SELECT * FROM bus where is_deleted IS NULL";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addBus(array $busData){
  insertRecord('bus', $busData);
  return true;
}

function updateBus($busData, $bus_id, $allowed_fields): bool {
  return updateRecord('bus', 'bus_id', $bus_id, $busData, $allowed_fields);
}

function busHasAssigned($bus_id, $column): bool {
  global $pdo;

  if (!in_array($column, ['conductor_id', 'driver_id'])) {
    throw new InvalidArgumentException("Invalid column name.");
  }

  $sql = "SELECT 1 FROM bus WHERE bus_id = :bus_id AND $column IS NOT NULL LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':bus_id' => $bus_id]);

  return $stmt->fetchColumn() !== false;
}

function isAssignedToAnotherBus($id, $new_bus_id, $column): bool {
  global $pdo;

  if (!in_array($column, ['conductor_id', 'driver_id'])) {
    throw new InvalidArgumentException("Invalid column name.");
  }

  $sql = "SELECT 1 FROM bus WHERE $column = :id AND bus_id != :bus_id LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $id, ':bus_id' => $new_bus_id]);

  return $stmt->fetchColumn() !== false;
}

function busExists($bus_id): bool {
  global $pdo;
  $stmt = $pdo->prepare("SELECT 1 FROM bus WHERE bus_id = :bus_id LIMIT 1");
  $stmt->execute([':bus_id' => $bus_id]);
  return $stmt->fetch() !== false;
}

function deleteBus($bus_id){
  global $pdo;
  $stmt = $pdo->prepare("DELETE FROM bus WHERE bus_id = :bus_id");
  $stmt->execute([':bus_id' => $bus_id]);
  return $stmt->fetch() !== false;
}