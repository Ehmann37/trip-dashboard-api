<?php 
  require_once __DIR__ . '/../config/db.php';
  require_once __DIR__ . '/../utils/DBUtils.php';

  function getAllBus(){
    global $pdo;
    
    $sql = "SELECT * FROM bus";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function addBus(array $busData){
    insertRecord('bus', $busData);
    return true;
  }