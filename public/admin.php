<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Painel Admin</title>
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

<style>
:root{
  --bg: #f4f7fb;
  --bg-soft: #eef3f8;
  --card: rgba(255,255,255,0.88);
  --card-solid: #ffffff;
  --text: #0f172a;
  --muted: #64748b;
  --line: rgba(15,23,42,.08);

  --brand: #ee4d2d;
  --brand-2: #ff6a3d;
  --brand-dark: #d63c1c;

  --ok-bg: #ecfdf5;
  --ok-text: #166534;

  --danger: #b91c1c;
  --danger-2: #ef4444;

  --warning-bg: #fff7ed;
  --warning-text: #9a3412;

  --success-bg: #ecfdf5;
  --success-text: #166534;

  --error-bg: #fef2f2;
  --error-text: #b91c1c;

  --cancel-bg: #f3f4f6;
  --cancel-text: #374151;

  --expired-bg: #eff6ff;
  --expired-text: #1d4ed8;

  --radius-xl: 24px;
  --radius-lg: 18px;
  --radius-md: 14px;
  --radius-sm: 12px;

  --shadow-lg: 0 22px 55px rgba(15,23,42,.08);
  --shadow-md: 0 12px 30px rgba(15,23,42,.06);
  --shadow-sm: 0 6px 16px rgba(15,23,42,.05);
}

*{ box-sizing:border-box; }
html{ scroll-behavior:smooth; }

body{
  margin:0;
  font-family:Inter, Arial, sans-serif;
  background:
    radial-gradient(circle at top left, rgba(238,77,45,.08), transparent 28%),
    radial-gradient(circle at top right, rgba(255,106,61,.08), transparent 24%),
    linear-gradient(180deg, #f8fafc 0%, var(--bg) 100%);
  color:var(--text);
  padding:32px;
}

.container{
  max-width:1280px;
  margin:auto;
  background:var(--card);
  backdrop-filter: blur(10px);
  border:1px solid rgba(255,255,255,.6);
  padding:32px;
  border-radius:var(--radius-xl);
  box-shadow:var(--shadow-lg);
}

h1{
  margin:0 0 8px;
  font-size:46px;
  line-height:1.05;
  letter-spacing:-1px;
  font-weight:800;
  color:var(--text);
}

h2{
  margin:0 0 18px;
  font-size:28px;
  line-height:1.15;
  letter-spacing:-.4px;
  font-weight:800;
  color:var(--text);
}

p{
  font-size:15px;
  line-height:1.55;
  color:var(--muted);
  margin:0 0 10px;
}

b,strong{ color:var(--text); }

a{
  color:var(--brand-dark);
  text-decoration:none;
  font-weight:700;
}

a:hover{ text-decoration:underline; }

button{
  appearance:none;
  border:none;
  background:linear-gradient(135deg, var(--brand), var(--brand-2));
  color:#fff;
  padding:12px 20px;
  border-radius:14px;
  cursor:pointer;
  font-size:15px;
  font-weight:700;
  box-shadow:0 10px 18px rgba(238,77,45,.18);
  transition:transform .18s ease, box-shadow .18s ease, opacity .18s ease;
}

button:hover{
  transform:translateY(-1px);
  box-shadow:0 14px 24px rgba(238,77,45,.22);
}

button:active{ transform:translateY(0); }

button.secondary{
  background:linear-gradient(135deg, #64748b, #475569);
  box-shadow:0 10px 18px rgba(71,85,105,.15);
}

button.danger{
  background:linear-gradient(135deg, #dc2626, #ef4444);
  box-shadow:0 10px 18px rgba(220,38,38,.16);
}

input, select{
  width:320px;
  max-width:100%;
  padding:13px 14px;
  border:1px solid rgba(15,23,42,.12);
  border-radius:14px;
  font-size:15px;
  color:var(--text);
  background:#fff;
  outline:none;
  transition:border-color .18s ease, box-shadow .18s ease, transform .18s ease;
  box-shadow: inset 0 1px 2px rgba(15,23,42,.03);
}

input:focus, select:focus{
  border-color:rgba(238,77,45,.55);
  box-shadow:0 0 0 4px rgba(238,77,45,.12);
}

.section{
  margin-top:34px;
  background:var(--card-solid);
  border:1px solid var(--line);
  border-radius:var(--radius-lg);
  padding:24px;
  box-shadow:var(--shadow-sm);
}

.hr{
  margin:24px 0 8px;
  border:none;
  border-top:1px solid var(--line);
}

.msg{
  background:linear-gradient(135deg, #ecfdf5, #f0fdf4);
  color:var(--ok-text);
  padding:14px 16px;
  border:1px solid rgba(22,101,52,.12);
  border-radius:14px;
  margin:16px 0;
  font-size:14px;
  font-weight:600;
}

.row{
  display:flex;
  gap:12px;
  align-items:center;
  flex-wrap:wrap;
}

.form-stack{
  display:flex;
  flex-direction:column;
  gap:14px;
  max-width:380px;
}

.small{
  color:var(--muted);
  font-size:13px;
  margin-top:10px;
}

.filters{
  display:flex;
  gap:12px;
  align-items:center;
  flex-wrap:wrap;
  margin-bottom:18px;
}

.table-wrap{
  overflow:auto;
  border:1px solid var(--line);
  border-radius:18px;
  margin-top:16px;
  background:#fff;
}

table{
  width:100%;
  border-collapse:collapse;
  min-width:960px;
}

th, td{
  padding:14px 14px;
  border-bottom:1px solid rgba(15,23,42,.06);
  text-align:left;
  font-size:14px;
}

th{
  background:#f8fafc;
  color:#334155;
  font-weight:800;
  position:sticky;
  top:0;
  z-index:1;
}

tbody tr{ transition:background .16s ease; }
tbody tr:hover{ background:#fcfcfd; }

.badge{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:94px;
  padding:7px 12px;
  border-radius:999px;
  font-size:12px;
  font-weight:800;
  letter-spacing:.2px;
}

.pendente{ background:var(--warning-bg); color:var(--warning-text); }
.aceita{ background:var(--success-bg); color:var(--success-text); }
.recusada{ background:var(--error-bg); color:var(--error-text); }
.cancelada{ background:var(--cancel-bg); color:var(--cancel-text); }
.expirada{ background:var(--expired-bg); color:var(--expired-text); }

.empty{
  padding:24px;
  color:var(--muted);
  text-align:center;
}

.actions form{ margin:0; }

.stats{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
  gap:14px;
  margin-bottom:20px;
}

.stat-card{
  background:linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
  border:1px solid var(--line);
  border-radius:18px;
  padding:18px;
  box-shadow:var(--shadow-sm);
  position:relative;
  overflow:hidden;
}

.stat-card::before{
  content:"";
  position:absolute;
  inset:0 auto 0 0;
  width:4px;
  background:linear-gradient(180deg, var(--brand), var(--brand-2));
}

.stat-card strong{
  display:block;
  font-size:34px;
  line-height:1;
  margin-top:10px;
  color:var(--text);
  letter-spacing:-1px;
}

.top-actions{
  display:flex;
  gap:12px;
  flex-wrap:wrap;
  margin-top:16px;
}

.hidden{ display:none !important; }

@media (max-width: 820px){
  body{ padding:18px; }
  .container{ padding:20px; border-radius:20px; }
  h1{ font-size:36px; }
  h2{ font-size:24px; }
  .section{ padding:18px; }
  .stats{ grid-template-columns:repeat(2, 1fr); }
}

@media (max-width: 560px){
  body{ padding:12px; }
  .container{ padding:16px; }
  h1{ font-size:32px; }
  .stats{ grid-template-columns:1fr; }
  input, select, button{ width:100%; }
  .row{ align-items:stretch; }
  .filters{ flex-direction:column; align-items:stretch; }
}
</style>
</head>
<body>

<div class="container hidden" id="appContainer">

  <h1>Painel Admin</h1>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="msg">
      <?= htmlspecialchars($_GET['msg']) ?>
    </div>
  <?php endif; ?>

  <p>Logado como: <b id="adminEmail">Carregando...</b></p>

  <div class="top-actions">
    <a href="#" id="btnLogout">Sair</a>
  </div>

  <hr class="hr">

  <div class="section">
    <h2>Importar rotas</h2>

    <form action="api/import-routes.php" method="POST" enctype="multipart/form-data" class="row">
      <input type="file" name="file" accept=".csv" required>
      <button type="submit">Importar</button>
    </form>

    <p class="small">O CSV deve ter as colunas: <b>driver_id, cluster, turno</b></p>
  </div>

  <div class="section">
    <h2>Resetar vínculo de motorista</h2>

    <form action="api/reset-driver-link.php" method="POST" class="row">
      <input type="text" name="driver_id" placeholder="Driver ID" required>
      <button type="submit">Resetar</button>
    </form>
  </div>

  <div class="section">
    <h2>Criar rota manual</h2>

    <form action="api/import-routes.php" method="POST" class="form-stack">
      <input type="text" name="driver_id" placeholder="Driver ID" required>
      <input type="text" name="cluster" placeholder="Cluster" required>
      <input type="text" name="turno" placeholder="Turno (AM / SD / PM2)" required>
      <button type="submit">Criar rota</button>
    </form>
  </div>

  <div class="section">
    <h2>Limpar base de rotas</h2>

    <form action="api/clear-routes.php" method="POST" onsubmit="return confirm('Tem certeza que deseja apagar TODAS as rotas da base?');">
      <button type="submit" class="danger">Limpar todas as rotas</button>
    </form>

    <p class="small">Esse botão apaga todas as rotas da tabela <b>route_offers</b>.</p>
  </div>

  <div class="section">
    <h2>Painel de rotas enviadas</h2>

    <div class="stats">
      <div class="stat-card">
        Total
        <strong id="statTotal">0</strong>
      </div>
      <div class="stat-card">
        Pendentes
        <strong id="statPendentes">0</strong>
      </div>
      <div class="stat-card">
        Aceitas
        <strong id="statAceitas">0</strong>
      </div>
      <div class="stat-card">
        Recusadas
        <strong id="statRecusadas">0</strong>
      </div>
      <div class="stat-card">
        Canceladas
        <strong id="statCanceladas">0</strong>
      </div>
    </div>

    <div class="filters">
      <select id="filtroStatus">
        <option value="">Todos os status</option>
        <option value="pendente">Pendentes</option>
        <option value="aceita">Aceitas</option>
        <option value="recusada">Recusadas</option>
        <option value="cancelada">Canceladas</option>
        <option value="expirada">Expiradas</option>
      </select>

      <input type="text" id="filtroDriver" placeholder="Buscar por Driver ID">

      <button type="button" onclick="carregarRotas()">Filtrar</button>
      <button type="button" class="secondary" onclick="limparFiltros()">Limpar</button>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID da rota</th>
            <th>Driver ID</th>
            <th>Cluster</th>
            <th>Turno</th>
            <th>Data</th>
            <th>Status</th>
            <th>Criada em</th>
            <th>Ação</th>
          </tr>
        </thead>
        <tbody id="tabelaRotas">
          <tr>
            <td colspan="8" class="empty">Carregando...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>

<div class="container" id="loadingContainer">
  <h1>Painel Admin</h1>
  <p>Validando acesso...</p>
</div>

<script>
const supabaseUrl = 'https://gfdsylfpafwsgprmajrr.supabase.co';
const supabaseAnonKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImdmZHN5bGZwYWZ3c2dwcm1hanJyIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzI5MDQyODIsImV4cCI6MjA4ODQ4MDI4Mn0.He_tN7LD-IsyzeXdEvsF-1cO4DwV4hDNYaad6_Jwmvc';
const client = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

let currentSession = null;
let adminChannel = null;

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

async function validarAdmin() {
  try {
    currentSession = await restoreSession();

    if (!currentSession) {
      window.location.href = 'admin-login.php';
      return;
    }

    if (window.location.hash) {
      history.replaceState({}, document.title, window.location.pathname);
    }

    const res = await fetch('/api/admin-check.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        access_token: currentSession.access_token
      })
    });

    const text = await res.text();
    let json;

    try {
      json = JSON.parse(text);
    } catch {
      throw new Error('Resposta inválida do admin-check.php: ' + text);
    }

    if (!json.ok || !json.admin) {
      alert(json.message || 'Acesso negado.');
      await client.auth.signOut();
      window.location.href = 'admin-login.php';
      return;
    }

    document.getElementById('adminEmail').textContent = currentSession.user.email;
    document.getElementById('loadingContainer').classList.add('hidden');
    document.getElementById('appContainer').classList.remove('hidden');

    iniciarRealtimeAdmin();
    carregarRotas();

  } catch (e) {
    console.error(e);
    alert('Erro ao validar admin: ' + e.message);
    window.location.href = 'admin-login.php';
  }
}

document.getElementById('btnLogout').addEventListener('click', async (e) => {
  e.preventDefault();
  await client.auth.signOut();
  window.location.href = 'admin-login.php';
});

function atualizarResumo(routes) {
  const total = routes.length;
  const pendentes = routes.filter(r => r.status === "pendente").length;
  const aceitas = routes.filter(r => r.status === "aceita").length;
  const recusadas = routes.filter(r => r.status === "recusada").length;
  const canceladas = routes.filter(r => r.status === "cancelada").length;

  document.getElementById("statTotal").textContent = total;
  document.getElementById("statPendentes").textContent = pendentes;
  document.getElementById("statAceitas").textContent = aceitas;
  document.getElementById("statRecusadas").textContent = recusadas;
  document.getElementById("statCanceladas").textContent = canceladas;
}

function iniciarRealtimeAdmin() {
  if (adminChannel) {
    client.removeChannel(adminChannel);
  }

  adminChannel = client
    .channel('admin-route-offers')
    .on(
      'postgres_changes',
      {
        event: '*',
        schema: 'public',
        table: 'route_offers'
      },
      (payload) => {
        console.log('Realtime admin:', payload);
        carregarRotas();
      }
    )
    .subscribe((status) => {
      console.log('Canal admin:', status);
    });
}

async function carregarRotas() {
  const status = document.getElementById("filtroStatus").value;
  const driver = document.getElementById("filtroDriver").value.trim();

  let url = "api/list-routes.php?";
  const params = [];

  if (status) {
    params.push("status=" + encodeURIComponent(status));
  }

  if (driver) {
    params.push("driver_id=" + encodeURIComponent(driver));
  }

  url += params.join("&");

  const tbody = document.getElementById("tabelaRotas");
  tbody.innerHTML = "<tr><td colspan='8' class='empty'>Carregando...</td></tr>";

  try {
    const res = await fetch(url);
    const data = await res.json();

    tbody.innerHTML = "";

    if (!data.ok || !data.routes || data.routes.length === 0) {
      atualizarResumo([]);
      tbody.innerHTML = "<tr><td colspan='8' class='empty'>Nenhuma rota encontrada.</td></tr>";
      return;
    }

    atualizarResumo(data.routes);

    data.routes.forEach(route => {
      let botao = "-";

      if (route.status === "pendente") {
        botao = `
          <form method="POST" action="api/cancel-route.php" onsubmit="return confirm('Deseja cancelar esta rota?');">
            <input type="hidden" name="route_id" value="${route.id}">
            <button type="submit">Cancelar</button>
          </form>
        `;
      }

      const statusClass = (route.status || "").toLowerCase();

      tbody.innerHTML += `
        <tr>
          <td>${route.id ?? "-"}</td>
          <td>${route.driver_id ?? "-"}</td>
          <td>${route.cluster ?? "-"}</td>
          <td>${route.turno ?? "-"}</td>
          <td>${route.offer_date ?? "-"}</td>
          <td><span class="badge ${statusClass}">${route.status ?? "-"}</span></td>
          <td>${route.created_at ?? "-"}</td>
          <td class="actions">${botao}</td>
        </tr>
      `;
    });

  } catch (e) {
    atualizarResumo([]);
    tbody.innerHTML = "<tr><td colspan='8' class='empty'>Erro ao carregar rotas.</td></tr>";
  }
}

function limparFiltros() {
  document.getElementById("filtroStatus").value = "";
  document.getElementById("filtroDriver").value = "";
  carregarRotas();
}

validarAdmin();
</script>

</body>
</html>