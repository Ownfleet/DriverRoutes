<?php
require_once __DIR__ . '/config.php';

function supabaseRequest($method, $endpoint, $body = null, $useServiceRole = false, $jwt = null) {
    $url = SUPABASE_URL . $endpoint;

    $headers = [
        'Content-Type: application/json',
        'apikey: ' . ($useServiceRole ? SUPABASE_SERVICE_ROLE_KEY : SUPABASE_ANON_KEY),
    ];

    if ($useServiceRole) {
        $headers[] = 'Authorization: Bearer ' . SUPABASE_SERVICE_ROLE_KEY;
    } elseif ($jwt) {
        $headers[] = 'Authorization: Bearer ' . $jwt;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    $decoded = json_decode($response, true);

    return [
        'status' => $httpCode,
        'body' => $decoded !== null ? $decoded : $response,
        'curl_error' => $curlError
    ];
}