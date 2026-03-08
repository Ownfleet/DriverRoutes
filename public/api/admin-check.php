<?php
require_once 'config.php';

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['access_token'])) {
    echo json_encode([
        "ok" => false,
        "message" => "Token não informado"
    ]);
    exit;
}

$token = $input['access_token'];

$ch = curl_init($SUPABASE_URL . "/auth/v1/user");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
        "apikey: $SUPABASE_ANON_KEY"
    ]
]);

$response = curl_exec($ch);
$user = json_decode($response, true);

if (!$user || !isset($user['email'])) {
    echo json_encode([
        "ok" => false,
        "message" => "Usuário inválido"
    ]);
    exit;
}

$email = $user['email'];

$stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND ativo = true LIMIT 1");
$stmt->execute([$email]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo json_encode([
        "ok" => false,
        "message" => "Usuário não é admin"
    ]);
    exit;
}

echo json_encode([
    "ok" => true,
    "admin" => $admin
]);