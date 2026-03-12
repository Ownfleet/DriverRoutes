<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$authUserId = trim((string)($input['auth_user_id'] ?? ''));
$email = trim(strtolower((string)($input['email'] ?? '')));

if ($authUserId === '' || $email === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'Dados obrigatórios não informados.'
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

$profileResp = supabaseRequest(
    'GET',
    SUPABASE_URL . '/rest/v1/profiles?select=user_id,email,driver_id,driver_code,full_name,is_driver_linked&user_id=eq.' . urlencode($authUserId) . '&limit=1'
);

if (!$profileResp['ok']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao consultar perfil.',
        'debug' => [
            'http_code' => $profileResp['http_code'],
            'response' => $profileResp['response']
        ]
    ]);
    exit;
}

$profileData = json_decode($profileResp['response'], true);
$profile = $profileData[0] ?? null;

if (!$profile || empty($profile['is_driver_linked']) || empty($profile['driver_id'])) {
    echo json_encode([
        'ok' => false,
        'redirect_to_link' => true,
        'error' => 'Conta ainda não vinculada ao motorista.'
    ]);
    exit;
}

/*
  Busca a rota mais recente do motorista com status:
  - pending
  - accepted
  Assim, quando ele aceitar, a rota continua aparecendo.
*/
$routeResp = supabaseRequest(
    'GET',
    SUPABASE_URL . '/rest/v1/route_offers?select=id,cluster,shift,status,sent_at,responded_at&driver_id=eq.' . $profile['driver_id'] . '&status=in.(pending,accepted)&order=sent_at.desc&limit=1'
);

if (!$routeResp['ok']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao consultar rota.',
        'debug' => [
            'http_code' => $routeResp['http_code'],
            'response' => $routeResp['response']
        ]
    ]);
    exit;
}

$routeData = json_decode($routeResp['response'], true);
$route = $routeData[0] ?? null;

echo json_encode([
    'ok' => true,
    'profile' => $profile,
    'route' => $route
]);