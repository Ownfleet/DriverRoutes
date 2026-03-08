<?php

$SUPABASE_URL = "https://gfdsylfpafwsgprmajrr.supabase.co";
$SUPABASE_ANON_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImdmZHN5bGZwYWZ3c2dwcm1hanJyIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzI5MDQyODIsImV4cCI6MjA4ODQ4MDI4Mn0.He_tN7LD-IsyzeXdEvsF-1cO4DwV4hDNYaad6_Jwmvc";

/* conexão banco postgres (railway) */

$host = getenv("PGHOST");
$port = getenv("PGPORT");
$db   = getenv("PGDATABASE");
$user = getenv("PGUSER");
$pass = getenv("PGPASSWORD");

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {

$pdo = new PDO($dsn,$user,$pass,[
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

} catch(Exception $e){

echo json_encode([
"ok"=>false,
"message"=>"Erro conexão banco"
]);

exit;

}






