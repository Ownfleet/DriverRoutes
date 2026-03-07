<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistema de Login Motorista</title>

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
  text-align:center;
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
  margin-bottom:24px;
  line-height:1.65;
  font-size:15px;
}

.btn{
  width:100%;
  border:none;
  background:linear-gradient(135deg, var(--brand), var(--brand-2));
  color:#fff;
  padding:15px 18px;
  border-radius:15px;
  font-size:16px;
  font-weight:800;
  cursor:pointer;
  transition:.2s;
  box-shadow:0 12px 20px rgba(238,77,45,.18);
}

.btn:hover{
  transform:translateY(-1px);
  opacity:.96;
}

.msg{
  margin-top:16px;
  font-size:14px;
  color:#475569;
  min-height:20px;
  line-height:1.45;
}
</style>

  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>
<body>
  <div class="card">
    <h1>Painel do Motorista</h1>
    <p>Entre com sua conta Google para acessar suas rotas disponíveis.</p>
    <button class="btn" id="btnGoogle">Entrar com Google</button>
    <div class="msg" id="msg"></div>
  </div>

  <script>
    const supabaseUrl = 'https://gfdsylfpafwsgprmajrr.supabase.co';
    const supabaseAnonKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImdmZHN5bGZwYWZ3c2dwcm1hanJyIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzI5MDQyODIsImV4cCI6MjA4ODQ4MDI4Mn0.He_tN7LD-IsyzeXdEvsF-1cO4DwV4hDNYaad6_Jwmvc';

    const msg = document.getElementById('msg');

    if (!window.supabase) {
      msg.textContent = 'Erro ao carregar biblioteca do Supabase.';
    } else {
      const client = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

      document.getElementById('btnGoogle').addEventListener('click', async () => {
        msg.textContent = 'Abrindo login...';

        const { error } = await client.auth.signInWithOAuth({
          provider: 'google',
          options: {
            redirectTo: window.location.origin + '/dashboard.php'
          }
        });

        if (error) {
          msg.textContent = 'Erro: ' + error.message;
        }
      });
    }
  </script>
</body>
</html>