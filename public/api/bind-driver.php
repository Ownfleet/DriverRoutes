<?php

require_once __DIR__ . '/supabase.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (
    !isset($input['access_token']) ||
    !isset($input['driver_id']) ||
    !isset($input['mode'])
) {
    echo json_encode([
        "ok" => false,
        "message" => "Dados incompletos"
    ]);
    exit;
}

$token = $input['access_token'];
$driverId = trim($input['driver_id']);
$mode = $input['mode'];

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

$driverRes = supabaseRequest(
    'GET',
    '/rest/v1/drivers?driver_id=eq.' . urlencode($driverId) . '&select=*&limit=1',
    null,
    true
);

if (($driverRes['status'] ?? 0) !== 200 || empty($driverRes['body'])) {
    echo json_encode([
        "ok" => false,
        "message" => "Driver ID não encontrado"
    ]);
    exit;
}

$driver = $driverRes['body'][0];

if ($mode === 'preview') {
    echo json_encode([
        "ok" => true,
        "driver" => $driver
    ]);
    exit;
}

if ($mode === 'confirm') {
    $existing = supabaseRequest(
        'GET',
        '/rest/v1/driver_accounts?user_id=eq.' . urlencode($userId) . '&select=id&limit=1',
        null,
        true
    );

    if (($existing['status'] ?? 0) === 200 && !empty($existing['body'])) {
        $id = $existing['body'][0]['id'];

        $update = supabaseRequest(
            'PATCH',
            '/rest/v1/driver_accounts?id=eq.' . urlencode($id),
            [
                'user_id' => $userId,
                'driver_id' => $driverId
            ],
            true
        );

        echo json_encode([
            "ok" => (($update['status'] ?? 0) >= 200 && ($update['status'] ?? 0) < 300)
        ]);
        exit;
    }

    $insert = supabaseRequest(
        'POST',
        '/rest/v1/driver_accounts',
        [[
            'user_id' => $userId,
            'driver_id' => $driverId
        ]],
        true
    );

    echo json_encode([
        "ok" => (($insert['status'] ?? 0) >= 200 && ($insert['status'] ?? 0) < 300)
    ]);
    exit;
}

echo json_encode([
    "ok" => false,
    "message" => "Modo inválido"
]);