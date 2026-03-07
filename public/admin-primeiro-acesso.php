<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Primeiro acesso admin</title>
  <style>
:root{
  --bg: #f4f7fb;
  --card: rgba(255,255,255,.92);
  --text: #0f172a;
  --muted: #64748b;
  --brand: #ee4d2d;
  --brand-2: #ff6a3d;
  --line: rgba(15,23,42,.10);
  --shadow: 0 22px 50px rgba(15,23,42,.08);
}

*{
  box-sizing:border-box;
  margin:0;
  padding:0;
  font-family:Inter, Arial, sans-serif;
}

body{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  background:
    radial-gradient(circle at top left, rgba(238,77,45,.08), transparent 30%),
    linear-gradient(180deg, #f8fafc 0%, var(--bg) 100%);
  padding:20px;
}

.card{
  width:100%;
  max-width:480px;
  background:var(--card);
  border:1px solid rgba(255,255,255,.8);
  backdrop-filter: blur(8px);
  border-radius:26px;
  padding:30px 24px;
  box-shadow:var(--shadow);
}

h1{
  margin-bottom:12px;
  font-size:34px;
  color:var(--text);
  letter-spacing:-.7px;
  line-height:1.08;
}

p{
  margin-bottom:16px;
  color:var(--muted);
  line-height:1.55;
  font-size:15px;
}

input{
  width:100%;
  padding:13px 14px;
  border:1px solid var(--line);
  border-radius:14px;
  margin-bottom:12px;
  font-size:15px;
  background:#fff;
  transition:border-color .18s ease, box-shadow .18s ease;
}

input:focus{
  outline:none;
  border-color:rgba(238,77,45,.55);
  box-shadow:0 0 0 4px rgba(238,77,45,.12);
}

button{
  width:100%;
  border:none;
  background:linear-gradient(135deg, var(--brand), var(--brand-2));
  color:#fff;
  padding:13px;
  border-radius:14px;
  cursor:pointer;
  font-size:15px;
  font-weight:800;
  box-shadow:0 10px 18px rgba(238,77,45,.16);
  transition:transform .18s ease, opacity .18s ease;
}

button:hover{
  opacity:.96;
  transform:translateY(-1px);
}

a{
  display:block;
  margin-top:14px;
  text-align:center;
  color:#c2410c;
  text-decoration:none;
  font-weight:700;
}

a:hover{
  text-decoration:underline;
}
</style>
</head>
<body>
  <div class="card">
    <h1>Primeiro acesso admin</h1>
    <p>Use seu email autorizado para criar sua senha.</p>

    <form method="POST" action="/api/admin-set-password.php">
      <input type="email" name="email" placeholder="Seu email autorizado" required>
      <input type="password" name="senha" placeholder="Nova senha" required>
      <input type="password" name="confirmar_senha" placeholder="Confirmar senha" required>
      <button type="submit">Criar senha</button>
    </form>

    <a href="/admin-login.php">Voltar para login</a>
  </div>
</body>
</html>