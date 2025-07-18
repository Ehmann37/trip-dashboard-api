<?php
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../models/TicketModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';

function handleCreateTrip() {
    $data = sanitizeInput(getRequestBody());

    $trip_details = $data['trip_details'] ?? null;
    $tickets = $data['tickets'] ?? null;

    
    $missing = validateFields($trip_details, ['route_id', 'bus_id', 'driver_id', 'conductor_id', 'boarding_time', 'arrival_time', 'total_passenger', 'total_revenue']);

    
    if (!empty($missing)) {
        respond('01', 'Missing required fields: ' . implode(', ', $missing));
        return;
    }

    

    $trip_id = addTripDetails($trip_details);
    if (!$trip_id){
        respond('01', 'Error uploading Trip');
    }

    $ticket_uploaded = true;
    foreach ($tickets as &$ticket) {
        if (!isset($trip_id)) {
            respond('01', 'Trip ID is missing in trip details');
            return;
        }

        $ticket['trip_id'] = $trip_id;
        $ticket['company_id'] = 1;
        $tick = addTicket($ticket);

        if (!$tick) {
            $ticket_uploaded = false;
        }
    };

    if (!$trip_id && $ticket_uploaded){
        respond('01', 'Error Uploading Ticket Summary');
    } else {
        respond('1', 'Ticket Summary Successfully Uploaded');
    }

}
