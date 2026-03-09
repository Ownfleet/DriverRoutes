<?php

require_once __DIR__.'/config.php';

header('Content-Type: application/json');

$input =
json_decode(
file_get_contents("php://input"),
true
);

if(!isset($input['access_token']))
{
echo json_encode([
"ok"=>false,
"message"=>"Token não enviado"
]);
exit;
}

$token = $input['access_token'];

$ch =
curl_init(
SUPABASE_URL."/auth/v1/user"
);

curl_setopt_array($ch,[

CURLOPT_RETURNTRANSFER=>true,

CURLOPT_HTTPHEADER=>[
"Authorization: Bearer ".$token,
"apikey: ".SUPABASE_ANON_KEY
]

]);

$response = curl_exec($ch);

$status =
curl_getinfo($ch,CURLINFO_HTTP_CODE);

curl_close($ch);

$user = json_decode($response,true);

if($status!==200 || !isset($user["email"]))
{
echo json_encode([
"ok"=>false,
"message"=>"Usuário inválido"
]);
exit;
}

$email = $user["email"];

$url =
SUPABASE_URL.
"/rest/v1/admins?select=*&email=eq.".
urlencode($email).
"&ativo=eq.true&limit=1";

$ch =
curl_init($url);

curl_setopt_array($ch,[

CURLOPT_RETURNTRANSFER=>true,

CURLOPT_HTTPHEADER=>[
"apikey: ".SUPABASE_SERVICE_ROLE_KEY,
"Authorization: Bearer ".SUPABASE_SERVICE_ROLE_KEY,
"Content-Type: application/json"
]

]);

$response = curl_exec($ch);

$status =
curl_getinfo($ch,CURLINFO_HTTP_CODE);

curl_close($ch);

$admins = json_decode($response,true);

if($status!==200 || count($admins)==0)
{
echo json_encode([
"ok"=>false,
"message"=>"Usuário não é admin"
]);
exit;
}

echo json_encode([
"ok"=>true
]);