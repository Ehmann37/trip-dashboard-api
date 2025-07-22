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
      $conductor['status'] = 'Active';
      $conductor['bus_id'] = $bus_id;
    } else {
      $conductor['status'] = 'Inactive';
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

function updateConductorInfo($conductorData, $conductor_id, $allowedUserFields) {
  $updated = false;

  $dataConductor = [];
  if (isset($conductorData['status'])) {
    $dataConductor['status'] = $conductorData['status'];
  }

  $userUpdate = updateRecord('users', 'user_id', $conductor_id, $conductorData, $allowedUserFields);
  if ($userUpdate) $updated = true;

  if (!empty($dataConductor)) {
    $conductorUpdate = updateRecord('conductors', 'conductor_id', $conductor_id, $dataConductor, ['status']);
    if ($conductorUpdate) $updated = true;
  }

  if (isset($conductorData['bus_id'])) {
    $busUpdate = updateRecord('bus', 'bus_id', $conductorData['bus_id'], ['conductor_id' => $conductor_id], ['conductor_id']);
    if ($busUpdate) $updated = true;
  }

  return $updated;
}

function checkConductorIfAssigned($conductor_id): bool {
  global $pdo;

  $sql = "SELECT COUNT(*) FROM bus WHERE conductor_id = :conductor_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':conductor_id' => $conductor_id]);

  return $stmt->fetchColumn() > 0;
}

function checkConductorExists($conductor_id): bool {
  return checkExists('conductors', 'conductor_id', $conductor_id);
}

function checkIfAnyConductorisAssigned($bus_id) : bool {
  global $pdo;

  $sql = "SELECT COUNT(*) FROM bus WHERE bus_id = :bus_id AND conductor_id IS NOT NULL";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':bus_id' => $bus_id]);

  return $stmt->fetchColumn() > 0;
}

function emailExistsForOtherUser($email, $excludeUserId) {
  global $pdo;
  $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = :email AND user_id != :id LIMIT 1");
  $stmt->execute([':email' => $email, ':id' => $excludeUserId]);
  return $stmt->fetch() !== false;
}

function isConductorAssignedToAnotherBus($conductor_id, $new_bus_id) {
  global $pdo;
  $stmt = $pdo->prepare("SELECT bus_id FROM bus WHERE conductor_id = :cid AND bus_id != :new_bus_id LIMIT 1");
  $stmt->execute([':cid' => $conductor_id, ':new_bus_id' => $new_bus_id]);
  return $stmt->fetch() !== false;
}