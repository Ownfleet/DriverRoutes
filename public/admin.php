<!doctype html>
<html lang="pt-br">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Painel Admin</title>

<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>

<body style="font-family:Arial;padding:40px">

<h1>Painel Admin</h1>

<p id="status">
Validando acesso...
</p>

<script>

const supabaseUrl = 'https://zxoyiysdchjbzqeajxac.supabase.cohttps://zxoyiysdchjbzqeajxac.supabase.co'
const supabaseAnonKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inp4b3lpeXNkY2hqYnpxZWFqeGFjIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzMwODY5NTEsImV4cCI6MjA4ODY2Mjk1MX0.YoWBH0yCFwrqeL0tt_dFgsFWFieoyl2-o8HkPwQNtxM'

const client =
window.supabase.createClient(
supabaseUrl,
supabaseAnonKey
)

async function init()
{

const { data } =
await client.auth.getSession()

if (!data.session)
{
window.location.href =
'/admin-login.php'
return
}

const res =
await fetch('/api/admin-check.php',
{
method:'POST',

headers:{
'Content-Type':'application/json'
},

body: JSON.stringify({
access_token:
data.session.access_token
})
})

const json = await res.json()

if (!json.ok)
{
document
.getElementById('status')
.textContent = 'Acesso negado'
return
}

document
.getElementById('status')
.textContent =
'Admin validado: ' +
data.session.user.email

}

init()

</script>

</body>
</html>