<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Dashboard Motorista</title>
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

  <style>
    *{box-sizing:border-box}

    :root{
      --bg:#f6f8fc;
      --card:#ffffff;
      --line:#eceef3;
      --text:#1f2937;
      --muted:#667085;
      --brand:#ee4d2d;
      --ok:#198754;
      --bad:#dc3545;
      --ok-bg:#e9f7ef;
      --ok-border:#b7e4c7;
      --shadow:0 12px 30px rgba(16,24,40,.08);
    }

    body{
      margin:0;
      min-height:100vh;
      font-family:Arial, Helvetica, sans-serif;
      background:
        radial-gradient(circle at top left, rgba(238,77,45,.10), transparent 24%),
        linear-gradient(180deg,#f8fafc 0%, #f2f4f8 100%);
      padding:24px;
      color:var(--text);
    }

    .wrap{
      max-width:1000px;
      margin:0 auto;
    }

    .header,.card{
      background:var(--card);
      border-radius:22px;
      padding:22px;
      box-shadow:var(--shadow);
      margin-bottom:18px;
      border:1px solid rgba(255,255,255,.75);
    }

    .header{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      flex-wrap:wrap;
      background:linear-gradient(135deg,#fff 0%, #fff7f5 100%);
    }

    .header h1{
      margin:0;
      color:var(--brand);
      font-size:34px;
      line-height:1.1;
    }

    .muted{
      color:var(--muted);
      line-height:1.5;
    }

    .logout{
      border:none;
      background:#444;
      color:#fff;
      padding:12px 16px;
      border-radius:12px;
      cursor:pointer;
      font-weight:bold;
      font-size:14px;
    }

    .route{
      border:1px solid var(--line);
      border-radius:18px;
      padding:18px;
      background:#fcfcfd;
    }

    .grid{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
      gap:12px;
      margin-top:12px;
    }

    .box{
      padding:14px;
      border-radius:14px;
      background:#fff;
      border:1px solid var(--line);
    }

    .box .lbl{
      font-size:12px;
      text-transform:uppercase;
      color:var(--muted);
      margin-bottom:6px;
      font-weight:bold;
      letter-spacing:.04em;
    }

    .box .val{
      font-size:18px;
      font-weight:bold;
      color:var(--text);
    }

    .actions{
      display:flex;
      gap:12px;
      margin-top:18px;
      flex-wrap:wrap;
    }

    .btn{
      flex:1;
      min-width:180px;
      border:none;
      padding:14px;
      border-radius:12px;
      cursor:pointer;
      font-weight:bold;
      color:#fff;
      font-size:15px;
    }

    .accept{
      background:linear-gradient(135deg,#198754 0%, #2bb673 100%);
    }

    .reject{
      background:linear-gradient(135deg,#dc3545 0%, #ff5c6c 100%);
    }

    .msg{
      margin-top:12px;
      font-weight:bold;
      min-height:20px;
    }

    .erro{color:#c62828}
    .ok{color:#2e7d32}

    .empty{
      padding:18px;
      border:1px dashed #d8dee7;
      border-radius:16px;
      background:#fafbfc;
      color:var(--muted);
      text-align:center;
      line-height:1.6;
      word-break:break-word;
    }

    .accepted-banner{
      margin-top:20px;
      padding:16px;
      background:var(--ok-bg);
      border:1px solid var(--ok-border);
      border-radius:12px;
      font-weight:bold;
      color:var(--ok);
      text-align:center;
    }

    @media (max-width: 700px){
      body{ padding:16px; }
      .header h1{ font-size:28px; }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <div>
        <h1>Minha Rota</h1>
        <div class="muted">Acompanhe sua rota disponível e responda.</div>
      </div>
      <button class="logout" id="btnSair" type="button">Sair</button>
    </div>

    <div class="card">
      <div id="perfilInfo" class="muted">Carregando vínculo...</div>
    </div>

    <div class="card">
      <h2 style="margin-top:0;">Rota disponível</h2>
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
                <div class="val" style="font-size:15px;">
                  ${rota.sent_at ? new Date(rota.sent_at).toLocaleString() : "-"}
                </div>
              </div>
            </div>

            ${rota.status === 'pending' ? `
              <div class="actions">
                <button class="btn accept" type="button" onclick="responderRota('accepted')">
                  Aceitar rota
                </button>

                <button class="btn reject" type="button" onclick="responderRota('rejected')">
                  Recusar rota
                </button>
              </div>
            ` : `
              <div class="accepted-banner">
                ✔ Você já aceitou esta rota
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