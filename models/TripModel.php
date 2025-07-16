<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';


function addTripDetails(array $trip_details): bool {
  return insertRecord('trip', $trip_details);
}

?>