<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';


function addTicket(array $ticket) {
  return insertRecord('ticket', $ticket);
}
?>