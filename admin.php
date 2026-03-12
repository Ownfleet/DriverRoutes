<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Painel Admin</title>

  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

  <style>
    *{box-sizing:border-box}

    :root{
      --bg:#f5f7fb;
      --card:#ffffff;
      --line:#eceef3;
      --text:#1f2937;
      --muted:#667085;
      --brand:#ee4d2d;
      --brand-dark:#d94223;
      --dark:#3d3d3d;
      --ok-bg:#dff7e7;
      --ok-tx:#157347;
      --warn-bg:#fff3cd;
      --warn-tx:#8a6d3b;
      --bad-bg:#fde2e2;
      --bad-tx:#842029;
      --cancel-bg:#e5e7eb;
      --cancel-tx:#374151;
      --shadow:0 12px 30px rgba(16,24,40,.08);
    }

    body{
      margin:0;
      font-family:Arial, Helvetica, sans-serif;
      background:
        radial-gradient(circle at top left, rgba(238,77,45,.10), transparent 24%),
        linear-gradient(180deg,#f8fafc 0%, #f3f5f9 100%);
      min-height:100vh;
      padding:24px;
      color:var(--text);
    }

    .auth-wrap{
      min-height:calc(100vh - 48px);
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .box{
      width:100%;
      max-width:500px;
      background:var(--card);
      padding:32px;
      border-radius:22px;
      box-shadow:var(--shadow);
      border:1px solid rgba(255,255,255,.7);
    }

    h1{
      margin:0 0 10px;
      color:var(--brand);
      text-align:center;
      font-size:38px;
      letter-spacing:.3px;
    }

    p{
      text-align:center;
      color:var(--muted);
      margin-bottom:20px;
      line-height:1.5;
    }

    .tabs{
      display:flex;
      gap:10px;
      margin-bottom:20px;
      background:#f3f4f6;
      padding:6px;
      border-radius:14px;
    }

    .tab{
      flex:1;
      padding:12px;
      border:none;
      border-radius:10px;
      cursor:pointer;
      background:transparent;
      font-weight:bold;
      color:#4b5563;
      transition:.2s;
    }

    .tab.active{
      background:linear-gradient(135deg,var(--brand) 0%, #ff6a3d 100%);
      color:#fff;
      box-shadow:0 8px 18px rgba(238,77,45,.24);
    }

    .form{
      display:none;
    }

    .form.active{
      display:block;
    }

    label{
      display:block;
      font-weight:bold;
      margin:12px 0 6px;
      color:#2d3748;
    }

    input, textarea{
      width:100%;
      padding:13px 14px;
      border:1px solid #d9dde6;
      border-radius:12px;
      font-size:15px;
      outline:none;
      transition:.2s;
      background:#fff;
    }

    input:focus, textarea:focus{
      border-color:rgba(238,77,45,.55);
      box-shadow:0 0 0 4px rgba(238,77,45,.10);
    }

    button.action{
      width:100%;
      border:none;
      background:linear-gradient(135deg,var(--brand) 0%, #ff6a3d 100%);
      color:#fff;
      padding:14px;
      border-radius:12px;
      font-size:16px;
      cursor:pointer;
      margin-top:18px;
      font-weight:bold;
      box-shadow:0 10px 20px rgba(238,77,45,.18);
    }

    button.action:hover,
    .card button:hover,
    .logout:hover,
    .popup button:hover,
    .toolbar-btn:hover{
      transform:translateY(-1px);
      opacity:.97;
    }

    .msg{
      margin-top:15px;
      text-align:center;
      font-weight:bold;
      line-height:1.4;
      min-height:22px;
    }

    .erro{ color:#c62828; }
    .ok{ color:#2e7d32; }

    .dica{
      margin-top:10px;
      font-size:13px;
      color:#777;
      text-align:center;
      line-height:1.5;
    }

    .hidden{
      display:none !important;
    }

    .admin-panel{
      display:none;
      max-width:1250px;
      margin:0 auto;
    }

    .admin-header{
      background:linear-gradient(135deg,#fff 0%, #fff7f5 100%);
      border-radius:24px;
      padding:24px;
      box-shadow:var(--shadow);
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:16px;
      flex-wrap:wrap;
      margin-bottom:22px;
      border:1px solid rgba(238,77,45,.10);
    }

    .admin-header h2{
      margin:0;
      color:var(--brand);
      font-size:20px;
    }

    .admin-header p{
      margin:8px 0 0;
      text-align:left;
      color:#4b5563;
    }

    .logout{
      border:none;
      background:#444;
      color:#fff;
      padding:12px 18px;
      border-radius:12px;
      cursor:pointer;
      font-size:15px;
      font-weight:bold;
    }

    .cards{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
      gap:18px;
    }

    .card{
      background:var(--card);
      border-radius:20px;
      padding:22px;
      box-shadow:var(--shadow);
      border:1px solid var(--line);
    }

    .card h3{
      margin:0 0 10px;
      color:#161b26;
      font-size:28px;
      line-height:1.15;
    }

    .card p{
      text-align:left;
      margin:0 0 16px;
      color:var(--muted);
      line-height:1.7;
      font-size:15px;
    }

    .card button{
      width:100%;
      border:none;
      background:linear-gradient(135deg,var(--brand) 0%, #ff6a3d 100%);
      color:#fff;
      padding:12px;
      border-radius:12px;
      cursor:pointer;
      font-size:15px;
      font-weight:bold;
      box-shadow:0 10px 20px rgba(238,77,45,.16);
    }

    .import-msg{
      margin-top:12px;
      font-weight:bold;
      min-height:20px;
      word-break:break-word;
      line-height:1.5;
    }

    .file-box{
      border:2px dashed #d9d9d9;
      border-radius:16px;
      padding:20px;
      background:linear-gradient(180deg,#fcfcfd 0%, #fafafa 100%);
    }

    .file-help{
      font-size:14px;
      color:#666;
      margin-top:10px;
      line-height:1.6;
    }

    .preview-box{
      margin-top:14px;
      padding:14px;
      background:#fff;
      border:1px solid #eee;
      border-radius:14px;
    }

    .preview-title{
      font-weight:bold;
      margin-bottom:10px;
      color:#333;
    }

    .preview-list{
      margin:0;
      padding-left:18px;
      color:#444;
      line-height:1.6;
      max-height:180px;
      overflow:auto;
    }

    .popup-overlay{
      position:fixed;
      inset:0;
      background:rgba(17,24,39,.45);
      display:none;
      align-items:center;
      justify-content:center;
      z-index:9999;
      padding:20px;
      backdrop-filter:blur(4px);
    }

    .popup{
      background:#fff;
      width:100%;
      max-width:380px;
      border-radius:18px;
      padding:26px;
      box-shadow:0 20px 50px rgba(0,0,0,.20);
      text-align:center;
    }

    .popup h3{
      margin:0 0 10px;
      color:#222;
      font-size:22px;
    }

    .popup p{
      margin:0;
      color:#555;
      line-height:1.6;
      text-align:center;
    }

    .popup button{
      margin-top:18px;
      border:none;
      background:linear-gradient(135deg,var(--brand) 0%, #ff6a3d 100%);
      color:#fff;
      padding:12px 22px;
      border-radius:12px;
      cursor:pointer;
      font-size:15px;
      font-weight:bold;
    }

    .module-panel{
      margin-top:22px;
      background:#fff;
      border-radius:20px;
      padding:22px;
      box-shadow:var(--shadow);
      border:1px solid var(--line);
    }

    .module-header{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      flex-wrap:wrap;
      margin-bottom:16px;
    }

    .module-header h3{
      margin:0;
      color:#222;
      font-size:22px;
    }

    .toolbar-btn{
      border:none;
      background:linear-gradient(135deg,var(--brand) 0%, #ff6a3d 100%);
      color:#fff;
      padding:10px 14px;
      border-radius:10px;
      cursor:pointer;
      font-size:14px;
      font-weight:bold;
    }

    .toolbar-btn.secondary{
      background:#555;
    }

    .toolbar-btn.danger{
      background:linear-gradient(135deg,#444 0%, #2f2f2f 100%);
    }

    .table-wrap{
      overflow:auto;
      border:1px solid var(--line);
      border-radius:14px;
    }

    table{
      width:100%;
      border-collapse:collapse;
      min-width:980px;
    }

    thead{
      background:#f8fafc;
    }

    th, td{
      padding:13px 12px;
      border-bottom:1px solid var(--line);
      text-align:left;
      font-size:14px;
      vertical-align:middle;
    }

    th{
      color:#334155;
      font-size:13px;
      text-transform:uppercase;
      letter-spacing:.04em;
    }

    td{
      color:#475467;
    }

    .status-badge{
      display:inline-block;
      padding:7px 10px;
      border-radius:999px;
      font-size:12px;
      font-weight:bold;
      text-transform:uppercase;
      white-space:nowrap;
    }

    .status-pending{
      background:var(--warn-bg);
      color:var(--warn-tx);
    }

    .status-accepted{
      background:var(--ok-bg);
      color:var(--ok-tx);
    }

    .status-rejected{
      background:var(--bad-bg);
      color:var(--bad-tx);
    }

    .status-cancelled{
      background:var(--cancel-bg);
      color:var(--cancel-tx);
    }

    .empty-state{
      padding:18px;
      color:#666;
      text-align:center;
    }

    .admin-form-grid{
      display:grid;
      grid-template-columns:2fr 1fr;
      gap:12px;
      margin-bottom:16px;
    }

    .action-wrap{
      display:flex;
      gap:8px;
      flex-wrap:wrap;
    }

    .mini-btn{
      border:none;
      border-radius:10px;
      padding:8px 10px;
      font-size:12px;
      cursor:pointer;
      font-weight:bold;
      color:#fff;
    }

    .mini-btn.activate{
      background:#198754;
    }

    .mini-btn.deactivate{
      background:#dc3545;
    }

    .mini-btn.reassign{
      background:#0d6efd;
    }

    @media (max-width: 700px){
      body{ padding:16px; }
      .admin-form-grid{ grid-template-columns:1fr; }
      .card h3{ font-size:24px; }
    }
  </style>
</head>
<body>

  <div class="auth-wrap" id="authWrap">
    <div class="box">
      <h1>Admin</h1>
      <p>Entre ou cadastre-se com e-mail corporativo.</p>

      <div class="tabs">
        <button class="tab active" id="tabLogin" type="button">Entrar</button>
        <button class="tab" id="tabCadastro" type="button">Cadastrar</button>
      </div>

      <div id="formLogin" class="form active">
        <label for="loginEmail">E-mail</label>
        <input type="email" id="loginEmail" placeholder="seu.nome@shopee.com">

        <label for="loginSenha">Senha</label>
        <input type="password" id="loginSenha" placeholder="Digite sua senha">

        <button class="action" id="btnEntrar" type="button">Entrar</button>
      </div>

      <div id="formCadastro" class="form">
        <label for="cadEmail">E-mail corporativo</label>
        <input type="email" id="cadEmail" placeholder="seu.nome@shopee.com">

        <label for="cadSenha">Senha</label>
        <input type="password" id="cadSenha" placeholder="Crie uma senha">

        <button class="action" id="btnCadastrar" type="button">Cadastrar</button>
        <div class="dica">Somente e-mails com domínio @shopee.com podem se cadastrar.</div>
      </div>

      <div id="msg" class="msg"></div>
    </div>
  </div>

  <div class="admin-panel" id="adminPanel">
    <div class="admin-header">
      <div>
        <h2>Painel Administrativo</h2>
        <p><strong>Admin logado:</strong> <span id="adminEmail"></span></p>
      </div>

      <button class="logout" id="btnSair" type="button">Sair</button>
    </div>

    <div class="cards">
      <div class="card" style="grid-column:1/-1;">
        <h3>Importar Rotas</h3>
        <p>Envie um arquivo CSV ou XLSX contendo as colunas de ID e CLUSTER.</p>

        <div class="file-box">
          <label for="arquivoRotas">Selecionar arquivo</label>
          <input type="file" id="arquivoRotas" accept=".csv,.xlsx,.xls">

          <div class="file-help">
            Formatos aceitos: <strong>.csv</strong>, <strong>.xlsx</strong> e <strong>.xls</strong>.<br>
            O arquivo deve conter colunas com nomes como <strong>ID</strong> e <strong>CLUSTER</strong>.
          </div>

          <div class="preview-box" id="previewBox" style="display:none;">
            <div class="preview-title">Prévia das rotas encontradas</div>
            <ol class="preview-list" id="previewList"></ol>
          </div>
        </div>

        <button type="button" id="btnImportarRotas" style="margin-top:12px;">
          Importar Rotas
        </button>

        <button type="button" id="btnLimparRotas" style="margin-top:10px;background:#444;">
          Limpar Rotas
        </button>

        <div id="importMsg" class="import-msg"></div>
      </div>

      <div class="card">
        <h3>Painel de Rotas</h3>
        <p>Veja rotas importadas, respostas, cancelamentos e faça o repasse para outro motorista.</p>
        <button type="button" id="btnAbrirPainelRotas">Abrir módulo</button>
      </div>

      <div class="card">
        <h3>Gerenciar Admins</h3>
        <p>Controle quais e-mails corporativos têm acesso ao painel administrativo.</p>
        <button type="button" id="btnAbrirAdmins">Abrir módulo</button>
      </div>
    </div>

    <div class="module-panel hidden" id="painelRotasPanel">
      <div class="module-header">
        <h3>Painel de Rotas</h3>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <button type="button" class="toolbar-btn" id="btnAtualizarPainelRotas">Atualizar</button>
          <button type="button" class="toolbar-btn danger" id="btnLimparRotasPainel">Limpar Rotas</button>
          <button type="button" class="toolbar-btn secondary" id="btnFecharPainelRotas">Fechar</button>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID Motorista</th>
              <th>Nome</th>
              <th>Cluster</th>
              <th>Turno</th>
              <th>Status</th>
              <th>Enviado em</th>
              <th>Respondido em</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="painelRotasTable">
            <tr>
              <td colspan="8" class="empty-state">Nenhuma rota carregada.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="module-panel hidden" id="adminsPanel">
      <div class="module-header">
        <h3>Gerenciar Admins</h3>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <button type="button" class="toolbar-btn" id="btnAtualizarAdmins">Atualizar</button>
          <button type="button" class="toolbar-btn secondary" id="btnFecharAdmins">Fechar</button>
        </div>
      </div>

      <div class="admin-form-grid">
        <div>
          <label for="novoAdminEmail">Novo e-mail admin</label>
          <input type="email" id="novoAdminEmail" placeholder="novo.admin@shopee.com">
        </div>
        <div style="display:flex; align-items:end;">
          <button type="button" class="toolbar-btn" id="btnAdicionarAdmin" style="width:100%;">Adicionar</button>
        </div>
      </div>

      <div id="adminManageMsg" class="import-msg"></div>

      <div class="table-wrap" style="margin-top:14px;">
        <table>
          <thead>
            <tr>
              <th>E-mail</th>
              <th>Status</th>
              <th>Criado em</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="adminsTable">
            <tr>
              <td colspan="4" class="empty-state">Nenhum admin carregado.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="popup-overlay" id="popup">
    <div class="popup">
      <h3 id="popupTitle">Aviso</h3>
      <p id="popupText"></p>
      <button type="button" id="popupBtn">OK</button>
    </div>
  </div>

  <script>
    const supabaseClient = window.supabase.createClient(
      "<?= SUPABASE_URL ?>",
      "<?= SUPABASE_ANON_KEY ?>"
    );

    const authWrap = document.getElementById("authWrap");
    const adminPanel = document.getElementById("adminPanel");

    const tabLogin = document.getElementById("tabLogin");
    const tabCadastro = document.getElementById("tabCadastro");
    const formLogin = document.getElementById("formLogin");
    const formCadastro = document.getElementById("formCadastro");

    const loginEmail = document.getElementById("loginEmail");
    const loginSenha = document.getElementById("loginSenha");
    const cadEmail = document.getElementById("cadEmail");
    const cadSenha = document.getElementById("cadSenha");

    const btnEntrar = document.getElementById("btnEntrar");
    const btnCadastrar = document.getElementById("btnCadastrar");
    const btnSair = document.getElementById("btnSair");

    const msg = document.getElementById("msg");
    const adminEmail = document.getElementById("adminEmail");

    const popup = document.getElementById("popup");
    const popupTitle = document.getElementById("popupTitle");
    const popupText = document.getElementById("popupText");
    const popupBtn = document.getElementById("popupBtn");

    const arquivoRotas = document.getElementById("arquivoRotas");
    const btnImportarRotas = document.getElementById("btnImportarRotas");
    const btnLimparRotas = document.getElementById("btnLimparRotas");
    const importMsg = document.getElementById("importMsg");
    const previewBox = document.getElementById("previewBox");
    const previewList = document.getElementById("previewList");

    const btnAbrirPainelRotas = document.getElementById("btnAbrirPainelRotas");
    const btnAtualizarPainelRotas = document.getElementById("btnAtualizarPainelRotas");
    const btnLimparRotasPainel = document.getElementById("btnLimparRotasPainel");
    const btnFecharPainelRotas = document.getElementById("btnFecharPainelRotas");
    const painelRotasPanel = document.getElementById("painelRotasPanel");
    const painelRotasTable = document.getElementById("painelRotasTable");

    const btnAbrirAdmins = document.getElementById("btnAbrirAdmins");
    const btnAtualizarAdmins = document.getElementById("btnAtualizarAdmins");
    const btnFecharAdmins = document.getElementById("btnFecharAdmins");
    const btnAdicionarAdmin = document.getElementById("btnAdicionarAdmin");
    const adminsPanel = document.getElementById("adminsPanel");
    const adminsTable = document.getElementById("adminsTable");
    const novoAdminEmail = document.getElementById("novoAdminEmail");
    const adminManageMsg = document.getElementById("adminManageMsg");

    let rotasProcessadas = [];

    function limparMensagem() {
      msg.className = "msg";
      msg.textContent = "";
    }

    function mostrarErro(texto) {
      msg.className = "msg erro";
      msg.textContent = texto;
    }

    function mostrarLogin() {
      tabLogin.classList.add("active");
      tabCadastro.classList.remove("active");
      formLogin.classList.add("active");
      formCadastro.classList.remove("active");
      limparMensagem();
    }

    function mostrarCadastro() {
      tabCadastro.classList.add("active");
      tabLogin.classList.remove("active");
      formCadastro.classList.add("active");
      formLogin.classList.remove("active");
      limparMensagem();
    }

    function emailShopeeValido(email) {
      return email.toLowerCase().endsWith("@shopee.com");
    }

    function abrirPopup(titulo, texto) {
      popupTitle.textContent = titulo;
      popupText.textContent = texto;
      popup.style.display = "flex";
    }

    function fecharPopup() {
      popup.style.display = "none";
    }

    function abrirPainel(email) {
      authWrap.classList.add("hidden");
      adminPanel.style.display = "block";
      adminEmail.textContent = email;
      limparMensagem();
    }

    function fecharPainel() {
      adminPanel.style.display = "none";
      authWrap.classList.remove("hidden");
      loginSenha.value = "";
      limparMensagem();
      mostrarLogin();
    }

    async function validarAdmin(email) {
      const resposta = await fetch("api/admin-login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email })
      });

      return await resposta.json();
    }

    function normalizarNomeColuna(nome) {
      return String(nome || "")
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
    }

    function encontrarValor(obj, possiveisNomes) {
      const entradas = Object.entries(obj || {});
      for (const [chave, valor] of entradas) {
        const chaveNormalizada = normalizarNomeColuna(chave);
        if (possiveisNomes.includes(chaveNormalizada)) {
          return valor;
        }
      }
      return "";
    }

    function extrairRotasDeLinhas(linhas) {
      const resultado = [];

      for (const linha of linhas) {
        const driverCode = String(
          encontrarValor(linha, ["id", "driver_id", "driver code", "driver_code", "codigo", "codigo motorista", "id motorista"])
        ).trim();

        const cluster = String(
          encontrarValor(linha, ["cluster", "rota", "route", "clusters"])
        ).trim();

        if (!driverCode || !cluster) continue;

        resultado.push({
          driver_code: driverCode,
          cluster: cluster
        });
      }

      return resultado;
    }

    function atualizarPreview(rotas) {
      previewList.innerHTML = "";

      if (!rotas.length) {
        previewBox.style.display = "none";
        return;
      }

      const limite = rotas.slice(0, 15);

      for (const rota of limite) {
        const li = document.createElement("li");
        li.textContent = `${rota.driver_code} → ${rota.cluster}`;
        previewList.appendChild(li);
      }

      if (rotas.length > 15) {
        const li = document.createElement("li");
        li.textContent = `... e mais ${rotas.length - 15} rota(s)`;
        previewList.appendChild(li);
      }

      previewBox.style.display = "block";
    }

    async function lerArquivoRotas(file) {
      const nome = file.name.toLowerCase();

      if (nome.endsWith(".csv")) {
        const texto = await file.text();
        const workbook = XLSX.read(texto, { type: "string" });
        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        const linhas = XLSX.utils.sheet_to_json(firstSheet, { defval: "" });
        return extrairRotasDeLinhas(linhas);
      }

      if (nome.endsWith(".xlsx") || nome.endsWith(".xls")) {
        const buffer = await file.arrayBuffer();
        const workbook = XLSX.read(buffer, { type: "array" });
        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        const linhas = XLSX.utils.sheet_to_json(firstSheet, { defval: "" });
        return extrairRotasDeLinhas(linhas);
      }

      return [];
    }

    function statusBadge(status) {
      const s = String(status || "").toLowerCase();

      if (s === "accepted") {
        return `<span class="status-badge status-accepted">Aceita</span>`;
      }

      if (s === "rejected") {
        return `<span class="status-badge status-rejected">Recusada</span>`;
      }

      if (s === "cancelled") {
        return `<span class="status-badge status-cancelled">Cancelada</span>`;
      }

      return `<span class="status-badge status-pending">Pendente</span>`;
    }

    async function carregarPainelRotas() {
      painelRotasPanel.classList.remove("hidden");
      painelRotasTable.innerHTML = `
        <tr>
          <td colspan="8" class="empty-state">Carregando rotas...</td>
        </tr>
      `;

      try {
        const resposta = await fetch("api/listar_rotas_admin.php");
        const resultado = await resposta.json();

        if (!resultado.ok) {
          painelRotasTable.innerHTML = `
            <tr>
              <td colspan="8" class="empty-state">Erro ao carregar rotas.</td>
            </tr>
          `;
          return;
        }

        const data = resultado.data || [];

        if (!data.length) {
          painelRotasTable.innerHTML = `
            <tr>
              <td colspan="8" class="empty-state">Nenhuma rota encontrada.</td>
            </tr>
          `;
          return;
        }

        painelRotasTable.innerHTML = "";

        data.forEach((r) => {
          const tr = document.createElement("tr");

          tr.innerHTML = `
            <td>${r.driver_code ?? "-"}</td>
            <td>${r.full_name ?? "-"}</td>
            <td>${r.cluster ?? "-"}</td>
            <td>${r.shift ?? "-"}</td>
            <td>${statusBadge(r.status)}</td>
            <td>${r.sent_at ? new Date(r.sent_at).toLocaleString() : "-"}</td>
            <td>${r.responded_at ? new Date(r.responded_at).toLocaleString() : "-"}</td>
            <td>
              <div class="action-wrap">
                <button class="mini-btn deactivate" type="button" onclick="cancelarRota(${r.id})">Cancelar</button>
                <button class="mini-btn reassign" type="button" onclick="repassarRota(${r.id})">Repassar</button>
              </div>
            </td>
          `;

          painelRotasTable.appendChild(tr);
        });
      } catch (e) {
        painelRotasTable.innerHTML = `
          <tr>
            <td colspan="8" class="empty-state">Erro de comunicação ao carregar rotas.</td>
          </tr>
        `;
      }
    }

    async function limparRotas() {
      const confirmar = confirm("Tem certeza que deseja limpar todas as rotas atuais?");
      if (!confirmar) return;

      importMsg.textContent = "";
      importMsg.style.color = "";

      try {
        const resposta = await fetch("api/limpar_rotas.php", {
          method: "POST"
        });

        const resultado = await resposta.json();

        if (!resultado.ok) {
          importMsg.textContent = resultado.error || "Erro ao limpar rotas.";
          importMsg.style.color = "#c62828";
          console.error("DEBUG limpar_rotas:", resultado.debug || resultado);
          return;
        }

        importMsg.textContent = "Todas as rotas foram removidas com sucesso.";
        importMsg.style.color = "#2e7d32";

        await carregarPainelRotas();
      } catch (e) {
        console.error("Erro limpar rotas:", e);
        importMsg.textContent = "Erro de comunicação ao limpar rotas.";
        importMsg.style.color = "#c62828";
      }
    }

    async function cancelarRota(routeId) {
      if (!confirm("Deseja cancelar esta rota?")) return;

      try {
        const resposta = await fetch("api/atualizar_rota_admin.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            route_id: routeId,
            action: "cancel"
          })
        });

        const resultado = await resposta.json();

        if (!resultado.ok) {
          abrirPopup("Erro", resultado.error || "Erro ao cancelar rota.");
          return;
        }

        await carregarPainelRotas();
      } catch (e) {
        abrirPopup("Erro", "Erro de comunicação ao cancelar rota.");
      }
    }

    async function repassarRota(routeId) {
      const novoDriver = prompt("Digite o novo ID do motorista para repasse:");

      if (!novoDriver) return;

      try {
        const resposta = await fetch("api/atualizar_rota_admin.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            route_id: routeId,
            action: "reassign",
            new_driver_code: novoDriver
          })
        });

        const resultado = await resposta.json();

        if (!resultado.ok) {
          abrirPopup("Erro", resultado.error || "Erro ao repassar rota.");
          return;
        }

        await carregarPainelRotas();
      } catch (e) {
        abrirPopup("Erro", "Erro de comunicação ao repassar rota.");
      }
    }

    window.cancelarRota = cancelarRota;
    window.repassarRota = repassarRota;
    window.limparRotas = limparRotas;

    async function carregarAdmins() {
      adminsPanel.classList.remove("hidden");
      adminManageMsg.textContent = "";
      adminsTable.innerHTML = `
        <tr>
          <td colspan="4" class="empty-state">Carregando admins...</td>
        </tr>
      `;

      try {
        const resposta = await fetch("api/listar_admins.php");
        const resultado = await resposta.json();

        if (!resultado.ok) {
          adminsTable.innerHTML = `
            <tr>
              <td colspan="4" class="empty-state">Erro ao carregar admins.</td>
            </tr>
          `;
          return;
        }

        if (!resultado.data || !resultado.data.length) {
          adminsTable.innerHTML = `
            <tr>
              <td colspan="4" class="empty-state">Nenhum admin encontrado.</td>
            </tr>
          `;
          return;
        }

        adminsTable.innerHTML = "";

        resultado.data.forEach((admin) => {
          const tr = document.createElement("tr");

          tr.innerHTML = `
            <td>${admin.email ?? "-"}</td>
            <td>${admin.active
              ? '<span class="status-badge status-accepted">Ativo</span>'
              : '<span class="status-badge status-rejected">Inativo</span>'}
            </td>
            <td>${admin.created_at ? new Date(admin.created_at).toLocaleString() : "-"}</td>
            <td>
              <div class="action-wrap">
                ${
                  admin.active
                    ? `<button class="mini-btn deactivate" type="button" onclick="alterarAdmin('${admin.email}', false)">Desativar</button>`
                    : `<button class="mini-btn activate" type="button" onclick="alterarAdmin('${admin.email}', true)">Ativar</button>`
                }
              </div>
            </td>
          `;

          adminsTable.appendChild(tr);
        });
      } catch (e) {
        adminsTable.innerHTML = `
          <tr>
            <td colspan="4" class="empty-state">Erro de comunicação ao carregar admins.</td>
          </tr>
        `;
      }
    }

    async function alterarAdmin(email, active) {
      try {
        const resposta = await fetch("api/atualizar_admin.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ email, active })
        });

        const resultado = await resposta.json();

        if (!resultado.ok) {
          adminManageMsg.textContent = resultado.error || "Erro ao atualizar admin.";
          adminManageMsg.style.color = "#c62828";
          return;
        }

        adminManageMsg.textContent = active
          ? "Admin ativado com sucesso."
          : "Admin desativado com sucesso.";
        adminManageMsg.style.color = "#2e7d32";

        await carregarAdmins();
      } catch (e) {
        adminManageMsg.textContent = "Erro de comunicação ao atualizar admin.";
        adminManageMsg.style.color = "#c62828";
      }
    }

    window.alterarAdmin = alterarAdmin;

    tabLogin.addEventListener("click", mostrarLogin);
    tabCadastro.addEventListener("click", mostrarCadastro);
    popupBtn.addEventListener("click", fecharPopup);

    btnAbrirPainelRotas.addEventListener("click", carregarPainelRotas);
    btnAtualizarPainelRotas.addEventListener("click", carregarPainelRotas);
    btnLimparRotasPainel.addEventListener("click", limparRotas);
    btnFecharPainelRotas.addEventListener("click", () => {
      painelRotasPanel.classList.add("hidden");
    });

    btnAbrirAdmins.addEventListener("click", carregarAdmins);
    btnAtualizarAdmins.addEventListener("click", carregarAdmins);
    btnFecharAdmins.addEventListener("click", () => {
      adminsPanel.classList.add("hidden");
    });

    btnLimparRotas.addEventListener("click", limparRotas);

    arquivoRotas.addEventListener("change", async (event) => {
      importMsg.textContent = "";
      importMsg.style.color = "";
      rotasProcessadas = [];
      previewBox.style.display = "none";
      previewList.innerHTML = "";

      const file = event.target.files[0];
      if (!file) return;

      try {
        const rotas = await lerArquivoRotas(file);
        rotasProcessadas = rotas;
        atualizarPreview(rotas);

        if (!rotas.length) {
          importMsg.textContent = "Não foi possível encontrar colunas válidas de ID e CLUSTER no arquivo.";
          importMsg.style.color = "#c62828";
          return;
        }

        importMsg.textContent = `${rotas.length} rota(s) encontrada(s) no arquivo.`;
        importMsg.style.color = "#2e7d32";
      } catch (error) {
        importMsg.textContent = "Erro ao ler o arquivo selecionado.";
        importMsg.style.color = "#c62828";
      }
    });

    btnCadastrar.addEventListener("click", async () => {
      limparMensagem();

      const email = cadEmail.value.trim().toLowerCase();
      const password = cadSenha.value.trim();

      if (!email || !password) {
        mostrarErro("Preencha e-mail e senha.");
        return;
      }

      if (!emailShopeeValido(email)) {
        mostrarErro("Somente e-mails @shopee.com podem se cadastrar.");
        return;
      }

      if (password.length < 6) {
        mostrarErro("A senha precisa ter pelo menos 6 caracteres.");
        return;
      }

      const { error } = await supabaseClient.auth.signUp({ email, password });

      if (error) {
        abrirPopup("Erro no cadastro", error.message);
        return;
      }

      cadEmail.value = "";
      cadSenha.value = "";
      mostrarLogin();

      abrirPopup(
        "Cadastro realizado com sucesso",
        "Sua conta foi criada. Agora esse e-mail precisa estar liberado na tabela admin_users para acessar o painel."
      );
    });

    btnEntrar.addEventListener("click", async () => {
      limparMensagem();

      const email = loginEmail.value.trim().toLowerCase();
      const password = loginSenha.value.trim();

      if (!email || !password) {
        mostrarErro("Preencha e-mail e senha.");
        return;
      }

      if (!emailShopeeValido(email)) {
        mostrarErro("Somente e-mails @shopee.com podem entrar.");
        return;
      }

      const { error } = await supabaseClient.auth.signInWithPassword({ email, password });

      if (error) {
        mostrarErro("Login inválido: " + error.message);
        return;
      }

      const resultado = await validarAdmin(email);

      if (!resultado.ok) {
        await supabaseClient.auth.signOut();
        mostrarErro("Seu e-mail não está liberado como admin no sistema.");
        return;
      }

      abrirPainel(email);
    });

    btnSair.addEventListener("click", async () => {
      await supabaseClient.auth.signOut();
      fecharPainel();
    });

    btnAdicionarAdmin.addEventListener("click", async () => {
      const email = novoAdminEmail.value.trim().toLowerCase();

      if (!email) {
        adminManageMsg.textContent = "Digite um e-mail para adicionar.";
        adminManageMsg.style.color = "#c62828";
        return;
      }

      if (!emailShopeeValido(email)) {
        adminManageMsg.textContent = "Somente e-mails @shopee.com podem ser adicionados.";
        adminManageMsg.style.color = "#c62828";
        return;
      }

      try {
        const resposta = await fetch("api/atualizar_admin.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            email,
            active: true,
            create_if_not_exists: true
          })
        });

        const resultado = await resposta.json();

        if (!resultado.ok) {
          adminManageMsg.textContent = resultado.error || "Erro ao adicionar admin.";
          adminManageMsg.style.color = "#c62828";
          return;
        }

        adminManageMsg.textContent = "Admin adicionado com sucesso.";
        adminManageMsg.style.color = "#2e7d32";
        novoAdminEmail.value = "";
        await carregarAdmins();
      } catch (e) {
        adminManageMsg.textContent = "Erro de comunicação ao adicionar admin.";
        adminManageMsg.style.color = "#c62828";
      }
    });

    btnImportarRotas.addEventListener("click", async () => {
      importMsg.textContent = "";
      importMsg.style.color = "";

      if (!rotasProcessadas.length) {
        importMsg.textContent = "Selecione um arquivo CSV ou XLSX com rotas válidas.";
        importMsg.style.color = "#c62828";
        return;
      }

      try {
        const resposta = await fetch("api/importar_rotas.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ rotas: rotasProcessadas })
        });

        const resultado = await resposta.json();

        if (!resultado.ok) {
          let detalhe = resultado.error || "Erro ao importar rotas.";

          if (resultado.http_code) {
            detalhe += " | HTTP " + resultado.http_code;
          }

          if (resultado.response) {
            detalhe += " | " + JSON.stringify(resultado.response);
          }

          if (resultado.nao_encontrados && resultado.nao_encontrados.length) {
            detalhe += " | IDs não encontrados: " + resultado.nao_encontrados.join(", ");
          }

          if (resultado.erros_insert && resultado.erros_insert.length) {
            detalhe += " | Erros insert: " + JSON.stringify(resultado.erros_insert);
          }

          importMsg.textContent = detalhe;
          importMsg.style.color = "#c62828";
          return;
        }

        let sucesso = `Importação concluída. ${resultado.total} rota(s) salva(s).`;

        if (resultado.nao_encontrados && resultado.nao_encontrados.length) {
          sucesso += ` IDs não encontrados: ${resultado.nao_encontrados.join(", ")}`;
        }

        if (resultado.erros_insert && resultado.erros_insert.length) {
          sucesso += ` | Falhas: ${JSON.stringify(resultado.erros_insert)}`;
        }

        importMsg.textContent = sucesso;
        importMsg.style.color = "#2e7d32";
        arquivoRotas.value = "";
        rotasProcessadas = [];
        previewBox.style.display = "none";
        previewList.innerHTML = "";

        await carregarPainelRotas();
      } catch (e) {
        importMsg.textContent = "Erro de comunicação com o servidor.";
        importMsg.style.color = "#c62828";
      }
    });

    window.addEventListener("load", async () => {
      const { data, error } = await supabaseClient.auth.getSession();

      if (error || !data.session || !data.session.user) {
        return;
      }

      const email = (data.session.user.email || "").toLowerCase();
      if (!email) return;

      const resultado = await validarAdmin(email);

      if (resultado.ok) {
        abrirPainel(email);
      } else {
        await supabaseClient.auth.signOut();
      }
    });
  </script>
</body>
</html>