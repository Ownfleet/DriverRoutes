<?php

require_once __DIR__ . '/config.php';

function supabaseRequest(string $method, string $endpoint, $data = null, bool $useServiceRole = false): array
{
    $url = rtrim(SUPABASE_URL, '/') . $endpoint;
    $apiKey = $useServiceRole ? SUPABASE_SERVICE_ROLE_KEY : SUPABASE_ANON_KEY;

    $headers = [
        'apikey: ' . $apiKey,
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'status' => 0,
            'body' => null,
            'error' => $error
        ];
    }

    curl_close($ch);

    return [
        'status' => $status,
        'body' => json_decode($response, true)
    ];
}