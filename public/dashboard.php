<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
  <style>
:root{
  --bg: #f4f7fb;
  --card: rgba(255,255,255,.92);
  --text: #0f172a;
  --muted: #64748b;
  --line: rgba(15,23,42,.08);

  --brand: #ee4d2d;
  --brand-2: #ff6a3d;

  --ok: #166534;
  --ok-bg: #ecfdf5;

  --err: #b91c1c;
  --err-bg: #fef2f2;

  --warning-bg: #fff7ed;
  --warning-text: #9a3412;
  --cancel-bg: #f3f4f6;
  --cancel-text: #374151;
  --exp-bg: #eff6ff;
  --exp-text: #1d4ed8;

  --shadow-lg: 0 22px 50px rgba(15,23,42,.08);
  --shadow-md: 0 12px 28px rgba(15,23,42,.06);
}

*{
  box-sizing:border-box;
  margin:0;
  padding:0;
  font-family:Inter, Arial, sans-serif;
}

body{
  background:
    radial-gradient(circle at top left, rgba(238,77,45,.08), transparent 30%),
    linear-gradient(180deg, #f8fafc 0%, var(--bg) 100%);
  color:var(--text);
  padding:24px;
}

.wrap{
  max-width:860px;
  margin:0 auto;
}

.card{
  background:var(--card);
  border:1px solid rgba(255,255,255,.7);
  backdrop-filter: blur(8px);
  border-radius:24px;
  padding:24px;
  box-shadow:var(--shadow-lg);
  margin-bottom:20px;
}

h1{
  margin-bottom:10px;
  font-size:42px;
  letter-spacing:-1px;
  line-height:1.05;
}

h2{
  margin-bottom:12px;
  font-size:24px;
  letter-spacing:-.4px;
  line-height:1.1;
}

p{
  margin-bottom:10px;
  font-size:15px;
  line-height:1.55;
  color:var(--muted);
}

strong{
  color:var(--text);
}

input{
  width:100%;
  padding:13px 14px;
  border:1px solid rgba(15,23,42,.12);
  border-radius:14px;
  margin:10px 0 12px;
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
  border:none;
  background:linear-gradient(135deg, var(--brand), var(--brand-2));
  color:#fff;
  padding:12px 18px;
  border-radius:14px;
  cursor:pointer;
  margin-right:8px;
  margin-top:8px;
  font-size:15px;
  font-weight:700;
  box-shadow:0 10px 18px rgba(238,77,45,.16);
  transition:transform .18s ease, opacity .18s ease, box-shadow .18s ease;
}

button:hover{
  opacity:.95;
  transform:translateY(-1px);
}

button.sec{
  background:linear-gradient(135deg, #64748b, #475569);
  box-shadow:0 10px 18px rgba(71,85,105,.14);
}

.route{
  border:1px solid var(--line);
  border-radius:18px;
  padding:18px;
  margin-top:14px;
  background:linear-gradient(180deg, #fff 0%, #fbfcfe 100%);
  box-shadow:var(--shadow-md);
}

.muted{
  color:var(--muted);
}

.ok{
  color:var(--ok);
  font-weight:700;
}

.err{
  color:var(--err);
  font-weight:700;
}

.badge{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  padding:7px 12px;
  border-radius:999px;
  font-size:12px;
  font-weight:800;
}

.badge.pendente{
  background:var(--warning-bg);
  color:var(--warning-text);
}

.badge.aceita{
  background:var(--ok-bg);
  color:var(--ok);
}

.badge.recusada{
  background:var(--err-bg);
  color:var(--err);
}

.badge.cancelada{
  background:var(--cancel-bg);
  color:var(--cancel-text);
}

.badge.expirada{
  background:var(--exp-bg);
  color:var(--exp-text);
}

.finalizado{
  margin-top:12px;
  padding:10px 12px;
  border-radius:12px;
  background:#f8fafc;
  color:#475569;
  font-weight:700;
  display:inline-block;
}

.logout{
  display:inline-block;
  margin-top:6px;
  color:#c2410c;
  font-weight:700;
  text-decoration:none;
}

.logout:hover{
  text-decoration:underline;
}

@media (max-width: 720px){
  body{
    padding:14px;
  }

  .card{
    padding:18px;
    border-radius:20px;
  }

  h1{
    font-size:34px;
  }

  h2{
    font-size:22px;
  }

  button{
    width:100%;
    margin-right:0;
  }
}
</style>
</head>
<body>
  <div class="wrap">

    <div class="card">
      <h1>Meu Painel</h1>
      <div id="status">Carregando...</div>
      <a href="#" class="logout" id="btnLogout">Sair</a>
    </div>

    <div class="card" id="bindBox" style="display:none;">
      <h2>Primeiro acesso</h2>
      <p>Digite seu ID para vincular sua conta.</p>
      <input type="text" id="driverId" placeholder="Digite seu ID">
      <button id="btnBuscar">Buscar ID</button>
      <div id="confirmArea"></div>
    </div>

    <div class="card" id="routesBox" style="display:none;">
      <h2>Minhas rotas</h2>
      <div id="routesList"></div>
    </div>

  </div>

  <script>
    const supabaseUrl = 'https://gfdsylfpafwsgprmajrr.supabase.co';
    const supabaseAnonKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImdmZHN5bGZwYWZ3c2dwcm1hanJyIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzI5MDQyODIsImV4cCI6MjA4ODQ4MDI4Mn0.He_tN7LD-IsyzeXdEvsF-1cO4DwV4hDNYaad6_Jwmvc';
    const client = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

    const statusEl = document.getElementById('status');
    const bindBox = document.getElementById('bindBox');
    const routesBox = document.getElementById('routesBox');
    const routesList = document.getElementById('routesList');
    const confirmArea = document.getElementById('confirmArea');
    const btnBuscar = document.getElementById('btnBuscar');
    const btnLogout = document.getElementById('btnLogout');

    let currentSession = null;
    let currentDriverId = null;
    let driverChannel = null;

    async function restoreSession() {
      let { data, error } = await client.auth.getSession();
      if (error) throw error;
      if (data.session) return data.session;

      const hash = window.location.hash || '';
      if (hash.includes('access_token=')) {
        const { data: urlData, error: urlError } = await client.auth.getSessionFromUrl({ storeSession: true });
        if (urlError) throw urlError;
        return urlData.session;
      }

      return null;
    }

    function getStatusBadge(status) {
      const classe = (status || '').toLowerCase();
      return `<span class="badge ${classe}">${status ?? '-'}</span>`;