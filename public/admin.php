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

const supabaseUrl = 'SUA_URL'
const supabaseAnonKey = 'SUA_ANON_KEY'

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