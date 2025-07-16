<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getCurrentWeekRevenueByCompany($company_id) {
  global $pdo;

  // Get current Monday
  $sql = "SELECT 
      t.route_id,
      -- Monday of this week
      DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AS week_start,
      -- Sunday of this week
      DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 6 DAY) AS week_end,
      SUM(t.total_revenue) AS total_revenue,
      SUM(t.total_passenger) AS total_passenger,
      COUNT(t.trip_id) AS trip_count
  FROM trip t
  INNER JOIN bus b ON t.bus_id = b.bus_id
  WHERE b.company_id = :company_id
    AND DATE(t.boarding_time) >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
    AND DATE(t.boarding_time) <= DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 6 DAY)
  GROUP BY t.route_id
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':company_id', $company_id, PDO::PARAM_INT);
  $stmt->execute();

  echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
