<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$mode = trim((string)($input['mode'] ?? 'preview'));
$authUserId = trim((string)($input['auth_user_id'] ?? ''));
$email = trim(strtolower((string)($input['email'] ?? '')));
$driverCode = trim((string)($input['driver_code'] ?? ''));

if ($authUserId === '' || $email === '' || $driverCode === '') {
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
        'Content-Type: application/json',
        'Prefer: return=representation,resolution=merge-duplicates'
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

$driverResp = supabaseRequest(
    'GET',
    SUPABASE_URL . '/rest/v1/drivers?select=id,driver_code,full_name,shift,active&driver_code=eq.' . urlencode($driverCode) . '&active=eq.true&limit=1'
);

if (!$driverResp['ok']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao consultar motorista.',
        'debug' => $driverResp
    ]);
    exit;
}

$driverData = json_decode($driverResp['response'], true);
$driver = $driverData[0] ?? null;

if (!$driver) {
    echo json_encode([
        'ok' => false,
        'error' => 'Motorista não encontrado ou inativo.'
    ]);
    exit;
}

if ($mode === 'preview') {
    echo json_encode([
        'ok' => true,
        'driver' => $driver
    ]);
    exit;
}

$profileResp = supabaseRequest(
    'GET',
    SUPABASE_URL . '/rest/v1/profiles?select=user_id,driver_id,is_driver_linked,email&user_id=eq.' . urlencode($authUserId) . '&limit=1'
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

if ($profile && !empty($profile['is_driver_linked']) && (string)$profile['driver_id'] !== (string)$driver['id']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Esta conta já está vinculada. Somente o admin pode resetar.'
    ]);
    exit;
}

$usedResp = supabaseRequest(
    'GET',
    SUPABASE_URL . '/rest/v1/profiles?select=user_id,driver_id&driver_id=eq.' . $driver['id'] . '&limit=1'
);

if (!$usedResp['ok']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao verificar vínculo do motorista.',
        'debug' => $usedResp
    ]);
    exit;
}

$usedData = json_decode($usedResp['response'], true);
$used = $usedData[0] ?? null;

if ($used && $used['user_id'] !== $authUserId) {
    echo json_encode([
        'ok' => false,
        'error' => 'Este motorista já está vinculado a outra conta.'
    ]);
    exit;
}

$body = [
    'user_id' => $authUserId,
    'email' => $email,
    'driver_id' => $driver['id'],
    'driver_code' => $driver['driver_code'],
    'full_name' => $driver['full_name'],
    'is_driver_linked' => true,
    'linked_at' => gmdate('c'),
    'driver_locked' => true,
    'role' => 'driver'
];

if ($profile) {
    $saveResp = supabaseRequest(
        'PATCH',
        SUPABASE_URL . '/rest/v1/profiles?user_id=eq.' . urlencode($authUserId),
        $body
    );
} else {
    $saveResp = supabaseRequest(
        'POST',
        SUPABASE_URL . '/rest/v1/profiles',
        $body
    );
}

if (!$saveResp['ok']) {
    echo json_encode([
        'ok' => false,
        'error' => 'Erro ao salvar vínculo.',
        'debug' => $saveResp
    ]);
    exit;
}

echo json_encode([
    'ok' => true
]);