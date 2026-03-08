<?php

require_once __DIR__ . '/supabase.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (
    !isset($input['access_token']) ||
    !isset($input['route_id']) ||
    !isset($input['status'])
) {
    echo json_encode([
        "ok" => false,
        "message" => "Dados incompletos"
    ]);
    exit;
}

$token = $input['access_token'];
$routeId = (int)$input['route_id'];
$newStatus = $input['status'];

if (!in_array($newStatus, ['aceita', 'recusada'], true)) {
    echo json_encode([
        "ok" => false,
        "message" => "Status inválido"
    ]);
    exit;
}

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
        "ok" => false,
        "message" => "Conta não vinculada"
    ]);
    exit;
}

$driverId = $accountRes['body'][0]['driver_id'];

$routeRes = supabaseRequest(
    'GET',
    '/rest/v1/route_offers?id=eq.' . $routeId . '&select=*&limit=1',
    null,
    true
);

if (($routeRes['status'] ?? 0) !== 200 || empty($routeRes['body'])) {
    echo json_encode([
        "ok" => false,
        "message" => "Rota não encontrada"
    ]);
    exit;
}

$route = $routeRes['body'][0];

if ((string)$route['driver_id'] !== (string)$driverId) {
    echo json_encode([
        "ok" => false,
        "message" => "Essa rota não pertence a este motorista"
    ]);
    exit;
}

if (($route['status'] ?? '') !== 'pendente') {
    echo json_encode([
        "ok" => false,
        "message" => "Essa rota já foi finalizada"
    ]);
    exit;
}

$update = supabaseRequest(
    'PATCH',
    '/rest/v1/route_offers?id=eq.' . $routeId,
    [
        'status' => $newStatus
    ],
    true
);

$statusCode = $update['status'] ?? 0;

if ($statusCode >= 200 && $statusCode < 300) {
    echo json_encode([
        "ok" => true,
        "message" => "Rota atualizada com sucesso"
    ]);
    exit;
}

echo json_encode([
    "ok" => false,
    "message" => "Erro ao atualizar rota"
]);