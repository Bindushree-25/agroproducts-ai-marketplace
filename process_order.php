<?php
// Retired legacy endpoint: its expected columns do not exist in the supplied shop_db schema,
// and it previously accepted raw card numbers. Keep the route safe for stale clients.
header('Content-Type: application/json; charset=utf-8');
http_response_code(410);
echo json_encode([
  'success' => false,
  'message' => 'This legacy checkout endpoint is disabled. Use the customer cart checkout; this demo does not process payments.'
]);
