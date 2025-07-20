<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';


function addTripDetails(array $trip_details, $conductor_id): int {

  global $pdo;
  $sql = "SELECT bus_id, driver_id FROM bus WHERE conductor_id = :conductor_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':conductor_id' => $conductor_id]);

  $bus = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$bus) {
    return 0; // No bus found for the given conductor
  }

  $trip_details['bus_id'] = $bus['bus_id'];
  $trip_details['driver_id'] = $bus['driver_id'];
  $trip_details['conductor_id'] = $conductor_id;
   
  return insertRecord('trip', $trip_details);
}

?>