<?php

require_once __DIR__ . '/supabase.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['access_token'])) {
    echo json_encode([
        "ok" => false,
        "message" => "Token não enviado"
    ]);
    exit;
}

$token = $input['access_token'];

$ch = curl_init(SUPABASE_URL . "/auth/v1/user");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $token,
        "apikey: " . SUPABASE_ANON_KEY
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$user = json_decode($response, true);

if ($httpCode !== 200 || !$user || !isset($user['id'])) {
    echo json_encode([
        "ok" => false,
        "message" => "Usuário inválido"
    ]);
    exit;
}

$userId = $user['id'];

$accountRes = supabaseRequest(
    'GET',
    '/rest/v1/driver_accounts?user_id=eq.' . urlencode($userId) . '&select=driver_id&limit=1',
    null,
    true
);

if (($accountRes['status'] ?? 0) !== 200 || empty($accountRes['body'])) {
    echo json_encode([
        "ok" => true,
        "routes" => []
    ]);
    exit;
}

$driverId = $accountRes['body'][0]['driver_id'];

$routesRes = supabaseRequest(
    'GET',
    '/rest/v1/route_offers?driver_id=eq.' . urlencode($driverId) . '&select=*&order=created_at.desc',
    null,
    true
);

echo json_encode([
    "ok" => true,
    "routes" => $routesRes['body'] ?? []
]);