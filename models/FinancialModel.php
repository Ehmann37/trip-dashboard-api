<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function companyExists($company_id) {
  return checkExists('bus_companies', 'company_id', $company_id);
}

function getCompanyAnalytics(int $companyId, $start_time, $end_time): array {
  global $pdo;

  $sql = "
      SELECT 
          t.fare_amount,
          t.payment_mode AS method,
          t.passenger_category AS type,
          tr.arrival_time
      FROM tickets t
      INNER JOIN trips tr ON t.trip_id = tr.trip_id
      INNER JOIN bus b ON tr.bus_id = b.bus_id
      WHERE b.company_id = :company_id
      AND tr.boarding_time BETWEEN :start_time AND :end_time
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
      'company_id' => $companyId,
      'start_time' => $start_time,
      'end_time' => $end_time
  ]);
  $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $grossRevenue = 0;
  $totalPassenger = 0;

  // Define all valid methods and types
  $allMethods = ['cash', 'online'];
  $allTypes = ['PWD', 'regular', 'senior', 'student'];

  // Initialize stats with all combinations
  $paymentStats = [];
  foreach ($allMethods as $method) {
      $paymentStats[$method] = ['count' => 0, 'revenue' => 0];
  }

  $categoryStats = [];
  foreach ($allTypes as $type) {
      $categoryStats[$type] = ['count' => 0, 'revenue' => 0];
  }

  $monthlyBreakdown = [];

  // Process ticket data
  foreach ($tickets as $ticket) {
      $fare = floatval($ticket['fare_amount']);
      $method = $ticket['method'];
      $type = $ticket['type'];
      $arrivalTime = $ticket['arrival_time'];

      $grossRevenue += $fare;
      $totalPassenger++;

      // Update method stats
      if (isset($paymentStats[$method])) {
          $paymentStats[$method]['count']++;
          $paymentStats[$method]['revenue'] += $fare;
      }

      // Update type stats
      if (isset($categoryStats[$type])) {
          $categoryStats[$type]['count']++;
          $categoryStats[$type]['revenue'] += $fare;
      }

      // Update monthly breakdown
      $monthKey = date('m-Y', strtotime($arrivalTime));
      if (!isset($monthlyBreakdown[$monthKey])) {
          $monthlyBreakdown[$monthKey] = ['revenue' => 0, 'passenger_count' => 0];
      }
      $monthlyBreakdown[$monthKey]['revenue'] += $fare;
      $monthlyBreakdown[$monthKey]['passenger_count']++;
  }

  // Format outputs
  $paymentModeBreakdown = [];
  foreach ($allMethods as $method) {
      $data = $paymentStats[$method];
      $paymentModeBreakdown[] = [
          'method' => $method,
          'percentage' => $totalPassenger > 0 ? round(($data['count'] / $totalPassenger) * 100, 2) : 0,
          'count' => $data['count'],
          'revenue' => round($data['revenue'], 2)
      ];
  }

  $passengerCategoryBreakdown = [];
  foreach ($allTypes as $type) {
      $data = $categoryStats[$type];
      $passengerCategoryBreakdown[] = [
          'type' => $type,
          'percentage' => $totalPassenger > 0 ? round(($data['count'] / $totalPassenger) * 100, 2) : 0,
          'count' => $data['count'],
          'revenue' => round($data['revenue'], 2)
      ];
  }

  return [
      'gross_revenue' => round($grossRevenue, 2),
      'total_passenger' => $totalPassenger,
      'payment_mode_breakdown' => $paymentModeBreakdown,
      'passenger_category_breakdown' => $passengerCategoryBreakdown
  ];
}
