<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$authUserId = trim((string)($input['auth_user_id'] ?? ''));
$routeId = (int)($input['route_id'] ?? 0);
$status = trim((string)($input['status'] ?? ''));

if ($authUserId === '' || $routeId <= 0 || !in_array($status, ['accepted', 'rejected'], true)) {
    echo json_encode([
        'ok' => false,
        'error' => 'Dados inválidos.'
    ]);
    exit;
}

function supabaseRequest($method, $url, $body = null) {
    $headers = [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Authorization: Bearer ' . SUPABASE_SERVICE_KEY,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    $opts = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers
    ];

    if ($body !== null) {
        $opts[CURLOPT_POSTFIELDS] = json_encode($body);
    }

    curl_setopt_array($ch, $opts);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'ok' => false,
            'http_code' => 0,
            'response' => $error
        ];
    }

    curl_close($ch);

    return [
        'ok' => $httpCode >= 200 && $httpCode < 300,
        'http_code' => $httpCode,
        'response' => $response
    ];
}

/* 1) Buscar perfil do motorista pelo user_id */
$profileResp = supabaseRequest(
    'GET',
    SUPABASE_URL . '/rest/v1/profiles?select=user_id,driver_id,is_driver_linked&user_id=eq.' . urlencode($authUserId) . '&limit=1'
);

if (!$profileResp['ok']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao consultar perfil.',
        'debug' => $profileResp
    ]);
    exit;
}

$profileData = json_decode($profileResp['response'], true);
$profile = $profileData[0] ?? null;

if (!$profile || empty($profile['is_driver_linked']) || empty($profile['driver_id'])) {
    echo json_encode([
        'ok' => false,
        'error' => 'Perfil não vinculado.'
    ]);
    exit;
}

/* 2) Verificar se a rota pertence a esse motorista */
$routeResp = supabaseRequest(
    'GET',
    SUPABASE_URL . '/rest/v1/route_offers?select=id,driver_id,status&driver_id=eq.' . $profile['driver_id'] . '&id=eq.' . $routeId . '&limit=1'
);

if (!$routeResp['ok']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao consultar rota.',
        'debug' => $routeResp
    ]);
    exit;
}

$routeData = json_decode($routeResp['response'], true);
$route = $routeData[0] ?? null;

if (!$route) {
    echo json_encode([
        'ok' => false,
        'error' => 'Rota não encontrada para este motorista.'
    ]);
    exit;
}

if ($route['status'] !== 'pending') {
    echo json_encode([
        'ok' => false,
        'error' => 'Essa rota já foi respondida ou alterada.'
    ]);
    exit;
}

/* 3) Atualizar status */
$updateResp = supabaseRequest(
    'PATCH',
    SUPABASE_URL . '/rest/v1/route_offers?id=eq.' . $routeId,
    [
        'status' => $status,
        'responded_at' => gmdate('c')
    ]
);

if (!$updateResp['ok']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao responder rota.',
        'debug' => $updateResp
    ]);
    exit;
}

echo json_encode([
    'ok' => true
]);