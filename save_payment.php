<?php
// This project does not integrate a payment provider. Never collect or store raw card details.
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');
http_response_code(410);
echo json_encode([
  'success' => false,
  'message' => 'Online payment is not enabled in this demo. No card details were saved. Orders remain Pending.'
]);
