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

    let currentSession = null;

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
    }

    async function init() {
      try {
        currentSession = await restoreSession();

        if (!currentSession) {
          statusEl.innerHTML = '<p class="err">Sessão não encontrada. Volte e faça login novamente.</p>';
          return;
        }

        if (window.location.hash) {
          history.replaceState({}, document.title, window.location.pathname);
        }

        statusEl.innerHTML = `<p><strong>Email:</strong> ${currentSession.user.email}</p>`;

        const res = await fetch('/api/get-driver.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ access_token: currentSession.access_token })
        });

        const text = await res.text();
        let json;
        try {
          json = JSON.parse(text);
        } catch {
          throw new Error('Resposta inválida do get-driver.php: ' + text);
        }

        if (json.linked) {
          statusEl.innerHTML += `<p class="ok">Conta vinculada ao ID: <strong>${json.account.driver_id}</strong></p>`;
          routesBox.style.display = 'block';
          await carregarRotas();
        } else {
          statusEl.innerHTML += `<p class="muted">Sua conta ainda não está vinculada a um ID.</p>`;
          bindBox.style.display = 'block';
        }
      } catch (e) {
        console.error(e);
        statusEl.innerHTML = `<p class="err">Erro ao carregar dashboard: ${e.message}</p>`;
      }
    }

    btnBuscar.addEventListener('click', async () => {
      try {
        const id = document.getElementById('driverId').value.trim();

        if (!id) {
          confirmArea.innerHTML = '<p class="err">Digite um ID.</p>';
          return;
        }

        if (!currentSession || !currentSession.access_token) {
          confirmArea.innerHTML = '<p class="err">Sessão não encontrada. Faça login novamente.</p>';
          return;
        }

        const res = await fetch('/api/bind-driver.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            access_token: currentSession.access_token,
            driver_id: id,
            mode: 'preview'
          })
        });

        const text = await res.text();
        let json;
        try {
          json = JSON.parse(text);
        } catch {
          throw new Error('Resposta inválida do bind-driver.php: ' + text);
        }

        if (!json.ok) {
          confirmArea.innerHTML = `<p class="err">${json.message}</p>`;
          return;
        }

        confirmArea.innerHTML = `
          <p>Este é seu nome? <strong>${json.driver.nome}</strong></p>
          <p>Veículo: ${json.driver.tipo_veiculo ?? '-'}</p>
          <p>Turno: ${json.driver.turno ?? '-'}</p>
          <button id="btnConfirmar">Confirmar</button>
          <button class="sec" id="btnCancelar">Cancelar</button>
        `;

        document.getElementById('btnCancelar').onclick = () => {
          confirmArea.innerHTML = '';
        };

        document.getElementById('btnConfirmar').onclick = async () => {
          const res2 = await fetch('/api/bind-driver.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              access_token: currentSession.access_token,
              driver_id: id,
              mode: 'confirm'
            })
          });

          const text2 = await res2.text();
          let json2;
          try {
            json2 = JSON.parse(text2);
          } catch {
            throw new Error('Resposta inválida ao confirmar vínculo: ' + text2);
          }

          if (!json2.ok) {
            confirmArea.innerHTML = `<p class="err">${json2.message}</p>`;
            return;
          }

          bindBox.style.display = 'none';
          statusEl.innerHTML += `<p class="ok">Conta vinculada com sucesso.</p>`;
          routesBox.style.display = 'block';
          await carregarRotas();
        };
      } catch (e) {
        console.error(e);
        confirmArea.innerHTML = `<p class="err">Erro: ${e.message}</p>`;
      }
    });

    async function carregarRotas() {
      try {
        const res = await fetch('/api/get-routes.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ access_token: currentSession.access_token })
        });

        const text = await res.text();
        let json;
        try {
          json = JSON.parse(text);
        } catch {
          throw new Error('Resposta inválida do get-routes.php: ' + text);
        }

        routesList.innerHTML = '';

        if (!json.routes || json.routes.length === 0) {
          routesList.innerHTML = '<p class="muted">Nenhuma rota disponível.</p>';
          return;
        }

        json.routes.forEach(route => {
          const div = document.createElement('div');
          div.className = 'route';

          let botoes = '';

          if (route.status === 'pendente') {
            botoes = `
              <button onclick="responderRota(${route.id}, 'aceita')">Aceitar</button>
              <button class="sec" onclick="responderRota(${route.id}, 'recusada')">Recusar</button>
            `;
          } else {
            botoes = `<p class="finalizado">Rota finalizada.</p>`;
          }

          div.innerHTML = `
            <p><strong>Cluster:</strong> ${route.cluster}</p>
            <p><strong>Turno:</strong> ${route.turno}</p>
            <p><strong>Data:</strong> ${route.offer_date}</p>
            <p><strong>Status:</strong> ${getStatusBadge(route.status)}</p>
            ${botoes}
          `;

          routesList.appendChild(div);
        });

      } catch (e) {
        console.error(e);
        routesList.innerHTML = `<p class="err">Erro ao carregar rotas: ${e.message}</p>`;
      }
    }

    async function responderRota(routeId, status) {
      if (!currentSession) {
        alert('Sessão não encontrada.');
        return;
      }

      const res = await fetch('/api/respond-route.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          access_token: currentSession.access_token,
          route_id: routeId,
          status: status
        })
      });

      const text = await res.text();
      let json;
      try {
        json = JSON.parse(text);
      } catch {
        alert('Resposta inválida: ' + text);
        return;
      }

      alert(json.message || 'Atualizado');
      await carregarRotas();
    }

    init();
  </script>
</body>
</html>