<?php

require_once __DIR__ . '/supabase.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['access_token'])) {
    echo json_encode([
        "linked" => false,
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
        "linked" => false,
        "message" => "Usuário inválido"
    ]);
    exit;
}

$userId = $user['id'];

$res = supabaseRequest(
    'GET',
    '/rest/v1/driver_accounts?user_id=eq.' . urlencode($userId) . '&select=*&limit=1',
    null,
    true
);

if (($res['status'] ?? 0) !== 200 || empty($res['body'])) {
    echo json_encode([
        "linked" => false
    ]);
    exit;
}

echo json_encode([
    "linked" => true,
    "account" => $res['body'][0]
]);