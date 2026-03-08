<?php

require_once __DIR__ . '/supabase.php';

header('Content-Type: application/json');

$status = trim($_GET['status'] ?? '');
$driverId = trim($_GET['driver_id'] ?? '');

$query = '/rest/v1/route_offers?select=*&order=created_at.desc';

if ($status !== '') {
    $query .= '&status=eq.' . urlencode($status);
}

if ($driverId !== '') {
    $query .= '&driver_id=eq.' . urlencode($driverId);
}

$res = supabaseRequest('GET', $query, null, true);

echo json_encode([
    'ok' => (($res['status'] ?? 0) === 200),
    'routes' => $res['body'] ?? []
]);