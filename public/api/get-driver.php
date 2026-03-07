<?php
require_once 'supabase.php';

$data = json_decode(file_get_contents('php://input'), true);
$jwt = $data['access_token'] ?? '';

if (!$jwt) {
    echo json_encode(['linked' => false, 'message' => 'Token ausente']);
    exit;
}

$userRes = supabaseRequest('GET', '/auth/v1/user', null, false, $jwt);

if ($userRes['status'] !== 200) {
    echo json_encode(['linked' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

$userId = $userRes['body']['id'];

$linkRes = supabaseRequest(
    'GET',
    '/rest/v1/driver_accounts?user_id=eq.' . $userId . '&select=*',
    null,
    true
);

if ($linkRes['status'] === 200 && count($linkRes['body']) > 0) {
    echo json_encode(['linked' => true, 'account' => $linkRes['body'][0]]);
} else {
    echo json_encode(['linked' => false]);
}