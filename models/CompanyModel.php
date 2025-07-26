<?php 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function companyExists($company_id) {
    return checkExists('bus_companies', 'company_id', $company_id);
}

function getCompanyOverview($company_id, $start_time, $end_time, $type) {
    global $pdo;

    // Base stats
    $sql = "
        SELECT 
            SUM(t.fare_amount) AS total_revenue,
            COUNT(t.ticket_id) AS total_passengers
        FROM trips AS tr
        JOIN tickets AS t ON tr.trip_id = t.trip_id
        WHERE tr.boarding_time BETWEEN :start_time AND :end_time 
          AND t.company_id = :company_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':company_id' => $company_id,
        ':start_time' => $start_time,
        ':end_time' => $end_time
    ]);
    $totals = $stmt->fetch(PDO::FETCH_ASSOC);

    // Average passengers per trip
    $sql = "
        SELECT AVG(sub.total_passengers) AS average_passenger_per_trip
        FROM (
            SELECT t.trip_id, COUNT(*) AS total_passengers
            FROM trips AS tr
            JOIN tickets AS t ON tr.trip_id = t.trip_id
            WHERE tr.boarding_time BETWEEN :start_time AND :end_time 
              AND t.company_id = :company_id
            GROUP BY t.trip_id
        ) AS sub";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':company_id' => $company_id,
        ':start_time' => $start_time,
        ':end_time' => $end_time
    ]);
    $avg_pass = $stmt->fetchColumn();

    // Total distinct trips
    $sql = "
        SELECT COUNT(DISTINCT tr.trip_id) AS total_trip
        FROM trips AS tr
        LEFT JOIN tickets AS t ON tr.trip_id = t.trip_id
        WHERE tr.boarding_time BETWEEN :start_time AND :end_time 
          AND t.company_id = :company_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':company_id' => $company_id,
        ':start_time' => $start_time,
        ':end_time' => $end_time
    ]);
    $total_trip = $stmt->fetchColumn();

    // Get all records to divide into segments
    $sql = "
        SELECT t.fare_amount, tr.boarding_time
        FROM trips AS tr
        JOIN tickets AS t ON tr.trip_id = t.trip_id
        WHERE tr.boarding_time BETWEEN :start_time AND :end_time
          AND t.company_id = :company_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':company_id' => $company_id,
        ':start_time' => $start_time,
        ':end_time' => $end_time
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Setup divisions
    $division = [];
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $intervals = [];

    switch ($type) {
        case 'day':
            $morning = clone $start;
            $afternoon = clone $start;
            $afternoon->modify('+12 hours');
            $intervals = [
                ['x' => 'morning', 'from' => $morning, 'to' => $afternoon],
                ['x' => 'afternoon', 'from' => $afternoon, 'to' => $end]
            ];
            break;

        case 'week':
            for ($i = 0; $i < 7; $i++) {
                $from = (clone $start)->modify("+$i day");
                $to = (clone $from)->modify('+1 day');
                $intervals[] = [
                    'x' => 'day ' .($i+1),
                    'from' => $from,
                    'to' => $to
                ];
            }
            break;

        case 'month':
            $days = $start->diff($end)->days;
            $chunk = floor($days / 4);
            for ($i = 0; $i < 4; $i++) {
                $from = (clone $start)->modify("+".($i * $chunk)." days");
                $to = (clone $from)->modify("+$chunk days");
                $intervals[] = [
                    'x' => 'week ' . ($i + 1),
                    'from' => $from,
                    'to' => ($i === 3) ? $end : $to
                ];
            }
            break;

        case 'none':
        default:
            $startYear = (int)$start->format('Y');
            $endYear = (int)$end->format('Y');
            for ($year = $startYear; $year <= $endYear; $year++) {
                $from = new DateTime("$year-01-01");
                $to = new DateTime(($year + 1) . "-01-01");
                if ($to > $end) $to = $end;
                $intervals[] = [
                    'x' => (string)$year,
                    'from' => $from,
                    'to' => $to
                ];
            }
            break;
    }

    // Aggregate into intervals
    foreach ($intervals as $interval) {
        $ridership = 0;
        $revenue = 0;
        foreach ($rows as $row) {
            $bt = new DateTime($row['boarding_time']);
            if ($bt >= $interval['from'] && $bt < $interval['to']) {
                $ridership++;
                $revenue += $row['fare_amount'];
            }
        }
        $division[] = [
            'x' => $interval['x'],
            'ridership' => $ridership,
            'revenue' => round($revenue, 2)
        ];
    }

    return [
        'revenue' => round($totals['total_revenue'] ?? 0, 2),
        'total_passengers' => (int)($totals['total_passengers'] ?? 0),
        'average_passenger_per_trip' => round($avg_pass ?? 0, 4),
        'total_trip' => (int)($total_trip ?? 0),
        'division' => $division
    ];
}

