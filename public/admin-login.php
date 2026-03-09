<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin</title>
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>
<body>
  <div style="max-width:420px;margin:60px auto;font-family:Arial">
    <h1>Login Admin</h1>
    <p>Entre com seu email e senha de admin.</p>

    <div id="msg" style="margin:10px 0;color:#b91c1c;"></div>

    <form id="loginForm">
      <input type="email" id="email" placeholder="Seu email" required style="display:block;width:100%;padding:12px;margin-bottom:12px;">
      <input type="password" id="senha" placeholder="Sua senha" required style="display:block;width:100%;padding:12px;margin-bottom:12px;">
      <button type="submit" id="btnEntrar" style="padding:12px 18px;">Entrar</button>
    </form>
  </div>

  <script>
    const supabaseUrl = 'https://gfdsylfpafwsgprmajrr.supabase.co';
    const supabaseAnonKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImdmZHN5bGZwYWZ3c2dwcm1hanJyIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzI5MDQyODIsImV4cCI6MjA4ODQ4MDI4Mn0.He_tN7LD-IsyzeXdEvsF-1cO4DwV4hDNYaad6_Jwmvc';
    const client = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

    const form = document.getElementById('loginForm');
    const msg = document.getElementById('msg');
    const btn = document.getElementById('btnEntrar');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      msg.textContent = '';
      btn.disabled = true;
      btn.textContent = 'Entrando...';

      const { data, error } = await client.auth.signInWithPassword({
        email: document.getElementById('email').value.trim(),
        password: document.getElementById('senha').value
      });

      if (error || !data.session) {
        msg.textContent = error?.message || 'Erro ao fazer login.';
        btn.disabled = false;
        btn.textContent = 'Entrar';
        return;
      }

      window.location.href = '/admin.php';
    });
  </script>
</body>
</html>