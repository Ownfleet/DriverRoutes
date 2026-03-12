<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Motorista • Acesso</title>

<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

<style>

body{
margin:0;
height:100vh;
display:flex;
align-items:center;
justify-content:center;
font-family:Arial;
background:#f5f7fb;
}

.box{
background:white;
padding:40px;
border-radius:20px;
width:420px;
text-align:center;
box-shadow:0 10px 30px rgba(0,0,0,.08);
}

h1{
color:#ee4d2d;
margin-bottom:10px;
}

.btn{
width:100%;
padding:16px;
border:none;
border-radius:10px;
font-size:16px;
cursor:pointer;
font-weight:bold;
}

.google{
background:#ee4d2d;
color:white;
}

</style>
</head>

<body>

<div class="box">

<h1>Motorista</h1>

<p>Entre com sua conta Google</p>

<button class="btn google" id="btnGoogle">
Entrar com Google
</button>

</div>

<script>

const supabaseClient = window.supabase.createClient(
"<?= SUPABASE_URL ?>",
"<?= SUPABASE_ANON_KEY ?>"
)

document.getElementById("btnGoogle").onclick = async () => {

await supabaseClient.auth.signInWithOAuth({
provider:'google',
options:{
redirectTo: window.location.origin + "/dashboard.php"
}
})

}

</script>

</body>
</html>