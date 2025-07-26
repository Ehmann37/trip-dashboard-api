<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getCurrentWeekRevenueByCompany($company_id) {
  global $pdo;

  $daysQuery = "
      SELECT DATEDIFF(NOW(), MIN(tr.boarding_time)) + 1 AS total_days
      FROM trips tr
      INNER JOIN bus b ON tr.bus_id = b.bus_id
      WHERE b.company_id = :company_id
  ";
  $stmtDays = $pdo->prepare($daysQuery);
  $stmtDays->execute([':company_id' => $company_id]);
  $total_days = (int) $stmtDays->fetchColumn();
  if ($total_days <= 0) $total_days = 1; 

  $sql = "
      SELECT  
          tr.route_id,
          COUNT(t.ticket_id) AS total_passengers,
          SUM(t.fare_amount) AS total_revenue
      FROM trips tr
      INNER JOIN tickets t ON t.trip_id = tr.trip_id
      WHERE t.company_id = :company_id
      GROUP BY tr.route_id
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([':company_id' => $company_id]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $final = [];
  foreach ($result as &$row) {
      $passenger_count = (int)$row['total_passengers'];
      $revenue = (float)$row['total_revenue'];
      $daily_passenger_count = floor($passenger_count / $total_days);
      $daily_revenue = round($revenue / $total_days, 2);
      $revenue_per_passenger = $daily_passenger_count > 0 
          ? round($daily_revenue / $daily_passenger_count, 2) 
          : 0;

      $final[] = [
          "route_id" => (int)$row['route_id'],
          "route_name" => getRouteNameById((int)$row['route_id']) ?: "Unknown Route",
          "passengers" => $daily_passenger_count,
          "daily_revenue" => number_format($daily_revenue, 2),
          "revenue_per_passenger" => number_format($revenue_per_passenger, 2)
      ];
  }

  return($final);
}


function getRouteNameById($route_id): ?string {
  global $pdo;

  $sql = "SELECT route_name FROM routes WHERE route_id = :route_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':route_id' => $route_id]);

  return $stmt->fetchColumn() ?: null;
}

function checkRouteExists($route_id): bool {
  return checkExists('routes', 'route_id', $route_id);
}