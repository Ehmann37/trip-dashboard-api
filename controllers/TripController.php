<?php
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../models/TicketModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/TokenUtils.php';


function handleCreateTrip() {
    $KEY = 'mysecretkey1234567890abcdef';

    $data = sanitizeInput(getRequestBody());
    $conductor_id = $data['conductor_id'] ?? null;

    if (!isset($data['token'])) {
        respond('01', 'Token is required');
        return;
    }

    if ($conductor_id === null) {
        $decryptedData = decryptData($data['token'], $KEY);

        $trip_details = $decryptedData['trip_details'] ?? [];
        unset($trip_details['status']);
        unset($trip_details['trip_id']);


        $tickets = $decryptedData['tickets'] ?? [];

        return respond('1', 'Trip Decrypted Successfully', [
            'trip_details' => $trip_details,
            'tickets' => $tickets
        ]);
    } else {
        $decryptedData = decryptData($data['token'], $KEY);
        $conductor_id = $data['conductor_id'];
    
        $trip_details = $decryptedData['trip_details'] ?? [];
        unset($trip_details['status']);
        unset($trip_details['trip_id']);
    
    
        $tickets = $decryptedData['tickets'] ?? [];
    
        
        $missing = validateFields($trip_details, ['route_id', 'boarding_time', 'arrival_time', 'total_passenger', 'total_revenue']);
    
        
        if (!empty($missing)) {
            respond('01', 'Missing required fields: ' . implode(', ', $missing));
            return;
        }
    
        
        $trip_id = addTripDetails($trip_details, $conductor_id);
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

    

}
