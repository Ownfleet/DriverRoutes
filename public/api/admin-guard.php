<?php

require_once __DIR__ . '/config.php';

function redirectAdminLogin(): void {
    header('Location: /admin-login.php');
    exit;
}

function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function getBearerToken(): ?string {
    $headers = function_exists('getallheaders') ? getallheaders() : [];

    if (!empty($headers['Authorization']) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $m)) {
        return $m[1];
    }

    if (!empty($_POST['access_token'])) {
        return $_POST['access_token'];
    }

    $raw = json_decode(file_get_contents('php://input'), true);
    if (!empty($raw['access_token'])) {
        return $raw['access_token'];
    }

    return null;
}

function requireAdmin(bool $redirectOnFail = true): array {
    $token = getBearerToken();

    if (!$token) {
        if ($redirectOnFail) redirectAdminLogin();
        jsonResponse(['ok' => false, 'message' => 'Token não enviado'], 401);
    }

    $ch = curl_init(SUPABASE_URL . '/auth/v1/user');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'apikey: ' . SUPABASE_ANON_KEY
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $user = json_decode($response, true);

    if ($httpCode !== 200 || !$user || empty($user['email'])) {
        if ($redirectOnFail) redirectAdminLogin();
        jsonResponse(['ok' => false, 'message' => 'Usuário inválido'], 401);
    }

    $url = SUPABASE_URL . '/rest/v1/admins?select=*&email=eq.' . urlencode($user['email']) . '&ativo=eq.true&limit=1';

    $ch2 = curl_init($url);
    curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'apikey: ' . SUPABASE_SERVICE_ROLE_KEY,
            'Authorization: Bearer ' . SUPABASE_SERVICE_ROLE_KEY,
            'Content-Type: application/json'
        ]
    ]);

    $response2 = curl_exec($ch2);
    $httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);

    $admins = json_decode($response2, true);

    if ($httpCode2 !== 200 || !is_array($admins) || count($admins) === 0) {
        if ($redirectOnFail) redirectAdminLogin();
        jsonResponse(['ok' => false, 'message' => 'Usuário não é admin'], 403);
    }

    return $admins[0];
}