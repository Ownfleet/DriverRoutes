<!doctype html>
<html lang="pt-br">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin</title>

<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>

<body style="font-family:Arial;padding:40px;">

<div style="max-width:420px;margin:40px auto">

<h1>Login Admin</h1>

<p>Entre com seu email e senha de admin.</p>

<div id="msg" style="color:red;margin-bottom:10px"></div>

<form id="loginForm">

<input
type="email"
id="email"
placeholder="Seu email"
required
style="display:block;width:100%;padding:12px;margin-bottom:12px"
>

<input
type="password"
id="senha"
placeholder="Sua senha"
required
style="display:block;width:100%;padding:12px;margin-bottom:12px"
>

<button type="submit">Entrar</button>

</form>

</div>

<script>

const supabaseUrl = 'https://zxoyiysdchjbzqeajxac.supabase.cohttps://zxoyiysdchjbzqeajxac.supabase.co'
const supabaseAnonKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inp4b3lpeXNkY2hqYnpxZWFqeGFjIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzMwODY5NTEsImV4cCI6MjA4ODY2Mjk1MX0.YoWBH0yCFwrqeL0tt_dFgsFWFieoyl2-o8HkPwQNtxM'

const client = window.supabase.createClient(
supabaseUrl,
supabaseAnonKey
)

document
.getElementById('loginForm')
.addEventListener('submit', async (e) => {

e.preventDefault()

const msg = document.getElementById('msg')
msg.textContent = ''

const { data, error } =
await client.auth.signInWithPassword({

email:
document.getElementById('email').value.trim(),

password:
document.getElementById('senha').value

})

if (error || !data.session)
{
msg.textContent =
error?.message || 'Erro ao fazer login'
return
}

window.location.href = '/admin.php'

})

</script>

</body>
</html>