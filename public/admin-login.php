<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin</title>
  <style>
    :root{
      --bg: #f4f7fb;
      --card: rgba(255,255,255,.92);
      --text: #0f172a;
      --muted: #64748b;
      --brand: #ee4d2d;
      --brand-2: #ff6a3d;
      --line: rgba(15,23,42,.10);
      --shadow: 0 24px 55px rgba(15,23,42,.08);
    }

    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
      font-family:Inter, Arial, sans-serif;
    }

    body{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      background:
        radial-gradient(circle at top left, rgba(238,77,45,.10), transparent 28%),
        radial-gradient(circle at bottom right, rgba(255,106,61,.08), transparent 24%),
        linear-gradient(135deg,#f8fafc,#edf2f7);
      padding:20px;
    }

    .card{
      width:100%;
      max-width:460px;
      background:var(--card);
      border:1px solid rgba(255,255,255,.8);
      backdrop-filter: blur(8px);
      border-radius:28px;
      padding:34px 26px;
      box-shadow:var(--shadow);
    }

    h1{
      font-size:34px;
      margin-bottom:12px;
      color:var(--text);
      letter-spacing:-.8px;
      line-height:1.08;
    }

    p{
      color:var(--muted);
      margin-bottom:20px;
      line-height:1.6;
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
    }

    button{
      width:100%;
      border:none;
      background:linear-gradient(135deg, var(--brand), var(--brand-2));
      color:#fff;
      padding:15px 18px;
      border-radius:15px;
      font-size:16px;
      font-weight:800;
      cursor:pointer;
      box-shadow:0 12px 20px rgba(238,77,45,.18);
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
    <h1>Login Admin</h1>
    <p>Entre com seu email autorizado e sua senha.</p>

    <form method="POST" action="/api/admin-auth.php">
      <input type="email" name="email" placeholder="Seu email" required>
      <input type="password" name="senha" placeholder="Sua senha" required>
      <button type="submit">Entrar</button>
    </form>

    <a href="/admin-primeiro-acesso.php">Primeiro acesso / Criar senha</a>
  </div>
</body>
</html>