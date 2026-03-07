<?php
require_once 'supabase.php';

$data = json_decode(file_get_contents('php://input'), true);
$jwt = $data['access_token'] ?? '';

$userRes = supabaseRequest('GET', '/auth/v1/user', null, false, $jwt);
if ($userRes['status'] !== 200) {
    echo json_encode(['routes' => []]);
    exit;
}

$userId = $userRes['body']['id'];

$linkRes = supabaseRequest(
    'GET',
    '/rest/v1/driver_accounts?user_id=eq.' . $userId . '&select=*',
    null,
    true
);

if ($linkRes['status'] !== 200 || count($linkRes['body']) === 0) {
    echo json_encode(['routes' => []]);
    exit;
}

$driverId = $linkRes['body'][0]['driver_id'];

$routeRes = supabaseRequest(
    'GET',
    '/rest/v1/route_offers?driver_id=eq.' . urlencode($driverId) . '&select=*&order=created_at.desc',
    null,
    true
);

echo json_encode(['routes' => $routeRes['body'] ?? []]);