<?php 
  require_once __DIR__ . '/../config/db.php';
  require_once __DIR__ . '/../utils/DBUtils.php';

  function getAllDrivers(){
    global $pdo;
    
    $sql = "SELECT * FROM driver";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function addDriver(array $driverData): int {
    return insertRecord('driver', $driverData);
  }