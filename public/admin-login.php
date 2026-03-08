<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin</title>
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
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
      --err: #b91c1c;
      --err-bg: #fef2f2;
      --ok: #166534;
      --ok-bg: #ecfdf5;
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
      outline:none;
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

    .msg{
      display:none;
      margin-bottom:14px;
      padding:12px 14px;
      border-radius:14px;
      font-size:14px;
      font-weight:600;
    }

    .msg.show{ display:block; }
    .msg.err{ background:var(--err-bg); color:var(--err); }
    .msg.ok{ background:var(--ok-bg); color:var(--ok); }

    a{
      display:block;
      margin-top:14px;
      text-align:center;
      color:#c2410c;
      text-decoration:none;
      font-weight:700;
    }

    a:hover{ text-decoration:underline; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Login Admin</h1>
    <p>Entre com seu email autorizado e sua senha.</p>

    <div id="msg" class="msg"></div>

    <form id="loginForm">
      <input type="email" id="email" placeholder="Seu email" required>
      <input type="password" id="senha" placeholder="Sua senha" required>
      <button type="submit" id="btnEntrar">Entrar</button>
    </form>

    <a href="/admin-primeiro-acesso.php">Primeiro acesso / Criar senha</a>
  </div>

  <script>
    const supabaseUrl = 'https://gfdsylfpafwsgprmajrr.supabase.co';
    const supabaseAnonKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImdmZHN5bGZwYWZ3c2dwcm1hanJyIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzI5MDQyODIsImV4cCI6MjA4ODQ4MDI4Mn0.He_tN7LD-IsyzeXdEvsF-1cO4DwV4hDNYaad6_Jwmvc';
    const client = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

    const form = document.getElementById('loginForm');
    const emailEl = document.getElementById('email');
    const senhaEl = document.getElementById('senha');
    const btnEntrar = document.getElementById('btnEntrar');
    const msg = document.getElementById('msg');

    function mostrarMensagem(texto, tipo = 'err') {
      msg.className = 'msg show ' + tipo;
      msg.textContent = texto;
    }

    async function verificarSessaoExistente() {
      const { data } = await client.auth.getSession();
      if (data.session) {
        window.location.href = '/admin.php';
      }
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      btnEntrar.disabled = true;
      btnEntrar.textContent = 'Entrando...';
      msg.className = 'msg';
      msg.textContent = '';

      const { data, error } = await client.auth.signInWithPassword({
        email: emailEl.value.trim(),
        password: senhaEl.value
      });

      if (error || !data.session) {
        mostrarMensagem(error?.message || 'Erro ao fazer login.');
        btnEntrar.disabled = false;
        btnEntrar.textContent = 'Entrar';
        return;
      }

      mostrarMensagem('Login realizado com sucesso.', 'ok');

      setTimeout(() => {
        window.location.href = '/admin.php';
      }, 500);
    });

    verificarSessaoExistente();
  </script>
</body>
</html>