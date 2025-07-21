<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function companyExists($company_id) {
    return checkExists('bus_companies', 'company_id', $company_id);
}

function getCompanyAnalytics(int $companyId): array {
  global $pdo;
  $sql = "
      SELECT 
          t.fare_amount,
          t.payment_mode,
          t.passenger_category,
          tr.arrival_time
      FROM ticket t
      INNER JOIN trip tr ON t.trip_id = tr.trip_id
      INNER JOIN bus b ON tr.bus_id = b.bus_id
      WHERE b.company_id = :company_id
  ";
  
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['company_id' => $companyId]);
  $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $grossRevenue = 0;
  $totalPassenger = 0;
  $paymentCounts = [];
  $categoryCounts = [];
  $monthlyBreakdown = [];

  foreach ($tickets as $ticket) {
      $fare = floatval($ticket['fare_amount']);
      $paymentMethod = $ticket['payment_mode'];
      $passengerCategory = $ticket['passenger_category'];
      $arrivalTime = $ticket['arrival_time'];

      $grossRevenue += $fare;
      $totalPassenger++;

      if (!isset($paymentCounts[$paymentMethod])) {
          $paymentCounts[$paymentMethod] = 0;
      }
      $paymentCounts[$paymentMethod]++;

      if (!isset($categoryCounts[$passengerCategory])) {
          $categoryCounts[$passengerCategory] = 0;
      }
      $categoryCounts[$passengerCategory]++;

      $monthKey = date('m-Y', strtotime($arrivalTime));
      if (!isset($monthlyBreakdown[$monthKey])) {
          $monthlyBreakdown[$monthKey] = ['revenue' => 0, 'passenger_count' => 0];
      }
      $monthlyBreakdown[$monthKey]['revenue'] += $fare;
      $monthlyBreakdown[$monthKey]['passenger_count']++;
  }

  $paymentPercentages = [];
  foreach ($paymentCounts as $method => $count) {
      $percentage = $totalPassenger > 0 ? round(($count / $totalPassenger) * 100, 2) : 0;
      $paymentPercentages[] = [
          'payment_mode' => $method,
          'percentage' => $percentage
      ];
  }

  $categoryPercentages = [];
  foreach ($categoryCounts as $category => $count) {
      $percentage = $totalPassenger > 0 ? round(($count / $totalPassenger) * 100, 2) : 0;
      $categoryPercentages[] = [
          'passenger_category' => $category,
          'percentage' => $percentage
      ];
  }

  $formattedMonthly = [];
  ksort($monthlyBreakdown); 
  foreach ($monthlyBreakdown as $month => $data) {
      $formattedMonthly[] = [
          'month' => $month,
          'revenue' => $data['revenue'],
          'passenger_count' => $data['passenger_count']
      ];
  }

  return [
      'gross_revenue' => $grossRevenue,
      'total_passenger' => $totalPassenger,
      'payment_mode_percentages' => $paymentPercentages,
      'passenger_category_percentages' => $categoryPercentages,
      'monthly_breakdown' => $formattedMonthly
  ];
}
