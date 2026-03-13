<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Dashboard Motorista</title>
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

  <style>
    *{
      box-sizing:border-box;
      margin:0;
      padding:0;
    }

    :root{
      --bg:#f4f7fb;
      --bg-soft:#fbfcfe;
      --surface:#ffffff;
      --surface-2:#f8fafc;
      --line:#e6eaf0;
      --line-strong:#d8dee8;

      --text:#182230;
      --text-soft:#667085;
      --title:#101828;

      --brand:#ee4d2d;
      --brand-2:#ff6a3d;
      --brand-dark:#d94223;
      --brand-soft:rgba(238,77,45,.10);

      --ok:#198754;
      --ok-2:#2bb673;
      --ok-bg:#e9f7ef;
      --ok-border:#b7e4c7;

      --bad:#dc3545;
      --bad-2:#ff5c6c;
      --bad-bg:#fee2e2;
      --bad-border:#f5b5bc;

      --pending-bg:#fff3cd;
      --pending-border:#f7df8a;
      --pending-text:#8a6d3b;

      --shadow-sm:0 10px 24px rgba(16,24,40,.07);
      --shadow-md:0 16px 38px rgba(16,24,40,.10);
      --shadow-lg:0 22px 56px rgba(16,24,40,.14);

      --radius-sm:12px;
      --radius-md:18px;
      --radius-lg:24px;

      --btn-h:48px;
      --transition:.22s ease;
    }

    body{
      margin:0;
      min-height:100vh;
      font-family:Inter, Arial, Helvetica, sans-serif;
      color:var(--text);
      background:
        radial-gradient(circle at top left, rgba(238,77,45,.10), transparent 22%),
        radial-gradient(circle at top right, rgba(255,106,61,.08), transparent 18%),
        linear-gradient(180deg,#f8fafc 0%, #f2f5fa 100%);
      padding:24px;
    }

    .wrap{
      max-width:1100px;
      margin:0 auto;
    }

    .header,
    .card{
      background:rgba(255,255,255,.92);
      border:1px solid rgba(255,255,255,.85);
      backdrop-filter:blur(8px);
      border-radius:26px;
      box-shadow:var(--shadow-md);
      margin-bottom:20px;
    }

    .header{
      padding:24px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:16px;
      flex-wrap:wrap;
      background:linear-gradient(135deg,#ffffff 0%, #fff7f5 100%);
    }

    .header-left h1{
      margin:0 0 8px;
      font-size:36px;
      line-height:1.1;
      color:var(--brand);
      letter-spacing:.2px;
    }

    .header-left p{
      color:var(--text-soft);
      line-height:1.6;
      font-size:15px;
    }

    .card{
      padding:22px;
    }

    .card-title{
      font-size:22px;
      color:var(--title);
      margin-bottom:8px;
    }

    .card-subtitle{
      color:var(--text-soft);
      line-height:1.6;
      font-size:14px;
      margin-bottom:16px;
    }

    .muted{
      color:var(--text-soft);
      line-height:1.6;
      font-size:14px;
    }

    .btn{
      height:var(--btn-h);
      border:none;
      border-radius:14px;
      padding:0 18px;
      font-size:14px;
      font-weight:800;
      cursor:pointer;
      transition:var(--transition);
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      white-space:nowrap;
    }

    .btn:hover{
      transform:translateY(-1px);
    }

    .btn-logout{
      background:linear-gradient(135deg,#4b5563 0%, #374151 100%);
      color:#fff;
      min-width:130px;
    }

    .btn-accept{
      flex:1;
      min-width:200px;
      background:linear-gradient(135deg,var(--ok) 0%, var(--ok-2) 100%);
      color:#fff;
      box-shadow:0 10px 20px rgba(25,135,84,.18);
    }

    .btn-reject{
      flex:1;
      min-width:200px;
      background:linear-gradient(135deg,var(--bad) 0%, var(--bad-2) 100%);
      color:#fff;
      box-shadow:0 10px 20px rgba(220,53,69,.18);
    }

    .profile-card{
      display:flex;
      align-items:center;
      gap:14px;
      padding:18px;
      border-radius:18px;
      background:linear-gradient(180deg,#ffffff 0%, #f9fbfd 100%);
      border:1px solid var(--line);
    }

    .profile-icon{
      width:52px;
      height:52px;
      border-radius:16px;
      background:var(--brand-soft);
      color:var(--brand-dark);
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:22px;
      font-weight:900;
      flex-shrink:0;
    }

    .route{
      border:1px solid var(--line);
      border-radius:22px;
      padding:20px;
      background:linear-gradient(180deg,#ffffff 0%, #fbfcfe 100%);
      box-shadow:inset 0 1px 0 rgba(255,255,255,.65);
    }

    .route-head{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:14px;
      flex-wrap:wrap;
      margin-bottom:18px;
      padding-bottom:14px;
      border-bottom:1px solid var(--line);
    }

    .route-head-left h3{
      margin:0 0 6px;
      font-size:22px;
      color:var(--title);
    }

    .route-head-left p{
      color:var(--text-soft);
      font-size:14px;
      line-height:1.6;
    }

    .status-chip{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:10px 14px;
      border-radius:999px;
      font-size:12px;
      font-weight:800;
      text-transform:uppercase;
      letter-spacing:.04em;
      white-space:nowrap;
    }

    .status-pending{
      background:var(--pending-bg);
      border:1px solid var(--pending-border);
      color:var(--pending-text);
    }

    .status-accepted{
      background:var(--ok-bg);
      border:1px solid var(--ok-border);
      color:var(--ok);
    }

    .status-rejected{
      background:var(--bad-bg);
      border:1px solid var(--bad-border);
      color:var(--bad);
    }

    .grid{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(190px,1fr));
      gap:14px;
    }

    .box{
      padding:16px;
      border-radius:16px;
      background:#fff;
      border:1px solid var(--line);
      min-height:96px;
      display:flex;
      flex-direction:column;
      justify-content:center;
    }

    .box .lbl{
      font-size:12px;
      text-transform:uppercase;
      letter-spacing:.05em;
      color:var(--text-soft);
      margin-bottom:8px;
      font-weight:800;
    }

    .box .val{
      font-size:19px;
      line-height:1.35;
      font-weight:800;
      color:var(--text);
      word-break:break-word;
    }

    .box .val.small{
      font-size:15px;
      font-weight:700;
      color:#344054;
    }

    .actions{
      display:flex;
      gap:12px;
      margin-top:20px;
      flex-wrap:wrap;
    }

    .msg{
      min-height:22px;
      margin-top:14px;
      font-weight:800;
      line-height:1.5;
      font-size:14px;
    }

    .erro{
      color:#c62828;
    }

    .ok{
      color:#2e7d32;
    }

    .empty{
      padding:24px;
      border:1px dashed #d7dde8;
      border-radius:18px;
      background:linear-gradient(180deg,#fcfdff 0%, #f8fafc 100%);
      color:var(--text-soft);
      text-align:center;
      line-height:1.7;
      word-break:break-word;
      font-size:14px;
    }

    .accepted-banner{
      margin-top:20px;
      padding:16px 18px;
      background:var(--ok-bg);
      border:1px solid var(--ok-border);
      border-radius:14px;
      font-weight:800;
      color:var(--ok);
      text-align:center;
      line-height:1.5;
    }

    .rejected-banner{
      margin-top:20px;
      padding:16px 18px;
      background:var(--bad-bg);
      border:1px solid var(--bad-border);
      border-radius:14px;
      font-weight:800;
      color:var(--bad);
      text-align:center;
      line-height:1.5;
    }

    @media (max-width: 760px){
      body{
        padding:16px;
      }

      .header{
        padding:18px;
      }

      .card{
        padding:18px;
      }

      .header-left h1{
        font-size:30px;
      }

      .btn-logout,
      .btn-accept,
      .btn-reject{
        width:100%;
      }

      .actions{
        flex-direction:column;
      }
    }

    @media (max-width: 520px){
      .header-left h1{
        font-size:27px;
      }

      .route-head-left h3{
        font-size:20px;
      }

      .box .val{
        font-size:17px;
      }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <div class="header-left">
        <h1>Minha Rota</h1>
        <p>Acompanhe sua rota disponível, veja os detalhes e responda diretamente pelo painel.</p>
      </div>

      <button class="btn btn-logout" id="btnSair" type="button">Sair</button>
    </div>

    <div class="card">
      <div class="card-title">Meu vínculo</div>
      <div class="card-subtitle">Informações do motorista vinculado à sua conta.</div>

      <div class="profile-card">
        <div class="profile-icon">👤</div>
        <div id="perfilInfo" class="muted">Carregando vínculo...</div>
      </div>
    </div>

    <div class="card">
      <div class="card-title">Rota disponível</div>
      <div class="card-subtitle">Abaixo estão as informações da rota atual disponível para você.</div>

      <div id="rotaArea" class="muted">Carregando rota...</div>
      <div class="msg" id="msg"></div>
    </div>
  </div>

  <script>
    const supabaseClient = window.supabase.createClient(
      "<?= SUPABASE_URL ?>",
      "<?= SUPABASE_ANON_KEY ?>"
    );

    const perfilInfo = document.getElementById("perfilInfo");
    const rotaArea = document.getElementById("rotaArea");
    const msg = document.getElementById("msg");
    const btnSair = document.getElementById("btnSair");

    let rotaAtual = null;

    function setMsg(texto, tipo = "") {
      msg.className = "msg " + tipo;
      msg.textContent = texto;
    }

    function textoStatus(status) {
      const s = String(status || "").toLowerCase();

      if (s === "accepted") return "ACEITA";
      if (s === "rejected") return "RECUSADA";
      if (s === "pending") return "PENDENTE";
      return status || "-";
    }

    function classeStatus(status) {
      const s = String(status || "").toLowerCase();

      if (s === "accepted") return "status-accepted";
      if (s === "rejected") return "status-rejected";
      return "status-pending";
    }

    async function carregarDashboard() {
      setMsg("");

      const { data, error } = await supabaseClient.auth.getSession();
      const user = data?.session?.user;

      if (error || !user) {
        window.location.href = "index.php";
        return;
      }

      try {
        const resposta = await fetch("api/buscar_rotas.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            auth_user_id: user.id,
            email: user.email
          })
        });

        const resultado = await resposta.json();
        console.log("buscar_rotas resultado:", JSON.stringify(resultado, null, 2));

        if (!resultado.ok) {
          if (resultado.redirect_to_link) {
            window.location.href = "vincular-id.php";
            return;
          }

          perfilInfo.textContent = resultado.error || "Erro ao consultar perfil.";

          if (resultado.debug) {
            console.error("DEBUG buscar_rotas:", JSON.stringify(resultado.debug, null, 2));
            rotaArea.innerHTML = `
              <div class="empty">
                <strong>Erro técnico</strong><br>
                HTTP: ${resultado.debug.http_code ?? "-"}<br>
                Resposta: ${resultado.debug.response ?? "-"}
              </div>
            `;
          } else {
            rotaArea.innerHTML = `
              <div class="empty">
                Não foi possível carregar sua rota.
              </div>
            `;
          }
          return;
        }

        const profile = resultado.profile;
        perfilInfo.innerHTML = `
          <strong>Motorista:</strong> ${profile.full_name || "-"}<br>
          <strong>ID:</strong> ${profile.driver_code || "-"}
        `;

        const rota = resultado.route;
        rotaAtual = rota || null;

        if (!rota) {
          rotaArea.innerHTML = `
            <div class="empty">
              Nenhuma rota pendente no momento.
            </div>
          `;
          return;
        }

        rotaArea.innerHTML = `
          <div class="route">
            <div class="route-head">
              <div class="route-head-left">
                <h3>Detalhes da rota</h3>
                <p>Confira as informações abaixo antes de responder.</p>
              </div>

              <div class="status-chip ${classeStatus(rota.status)}">
                ${textoStatus(rota.status)}
              </div>
            </div>

            <div class="grid">
              <div class="box">
                <div class="lbl">Cluster</div>
                <div class="val">${rota.cluster ?? "-"}</div>
              </div>

              <div class="box">
                <div class="lbl">Turno</div>
                <div class="val">${rota.shift ?? "-"}</div>
              </div>

              <div class="box">
                <div class="lbl">Status</div>
                <div class="val">${textoStatus(rota.status)}</div>
              </div>

              <div class="box">
                <div class="lbl">Enviado em</div>
                <div class="val small">
                  ${rota.sent_at ? new Date(rota.sent_at).toLocaleString() : "-"}
                </div>
              </div>
            </div>

            ${rota.status === 'pending' ? `
              <div class="actions">
                <button class="btn btn-accept" type="button" onclick="responderRota('accepted')">
                  Aceitar rota
                </button>

                <button class="btn btn-reject" type="button" onclick="responderRota('rejected')">
                  Recusar rota
                </button>
              </div>
            ` : rota.status === 'accepted' ? `
              <div class="accepted-banner">
                ✔ Você já aceitou esta rota.
              </div>
            ` : `
              <div class="rejected-banner">
                ✖ Você recusou esta rota.
              </div>
            `}
          </div>
        `;
      } catch (e) {
        console.error("Erro carregarDashboard:", e);
        perfilInfo.textContent = "Erro de comunicação.";
        rotaArea.innerHTML = `
          <div class="empty">
            Erro de comunicação com o servidor.
          </div>
        `;
      }
    }

    async function responderRota(status) {
      if (!rotaAtual) return;

      const { data } = await supabaseClient.auth.getSession();
      const user = data?.session?.user;

      if (!user) {
        window.location.href = "index.php";
        return;
      }

      try {
        const resposta = await fetch("api/responder_rota.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            auth_user_id: user.id,
            route_id: rotaAtual.id,
            status
          })
        });

        const resultado = await resposta.json();
        console.log("responder_rota resultado:", JSON.stringify(resultado, null, 2));

        if (!resultado.ok) {
          setMsg(resultado.error || "Erro ao responder rota.", "erro");
          return;
        }

        setMsg(
          status === "accepted"
            ? "Rota aceita com sucesso."
            : "Rota recusada com sucesso.",
          "ok"
        );

        await carregarDashboard();
      } catch (e) {
        console.error("Erro responderRota:", e);
        setMsg("Erro de comunicação.", "erro");
      }
    }

    window.responderRota = responderRota;

    btnSair.addEventListener("click", async () => {
      await supabaseClient.auth.signOut();
      window.location.href = "index.php";
    });

    window.addEventListener("load", carregarDashboard);
  </script>
</body>
</html>