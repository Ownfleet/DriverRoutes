<?php

require_once __DIR__ . '/config.php';

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

/* pega usuário autenticado */
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

if ($httpCode !== 200 || !$user || !isset($user['email'])) {
    echo json_encode([
        "ok" => false,
        "message" => "Usuário inválido"
    ]);
    exit;
}

$email = $user['email'];

/* verifica tabela admins */
$url = SUPABASE_URL . "/rest/v1/admins?select=*&email=eq." . urlencode($email) . "&ativo=eq.true&limit=1";

$ch2 = curl_init($url);
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_SERVICE_ROLE_KEY,
        "Authorization: Bearer " . SUPABASE_SERVICE_ROLE_KEY,
        "Content-Type: application/json"
    ]
]);

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

$admins = json_decode($response2, true);

if ($httpCode2 !== 200 || !is_array($admins) || count($admins) === 0) {
    echo json_encode([
        "ok" => false,
        "message" => "Usuário não é admin"
    ]);
    exit;
}

echo json_encode([
    "ok" => true,
    "admin" => $admins[0]
]);