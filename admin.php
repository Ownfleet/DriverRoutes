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
      --line:#e5e7eb;
      --line-strong:#d9dee8;

      --text:#182230;
      --text-soft:#5f6c7b;
      --title:#101828;

      --brand:#ee4d2d;
      --brand-2:#ff6a3d;
      --brand-dark:#d94122;
      --brand-soft:rgba(238,77,45,.10);

      --ok-bg:#dcfce7;
      --ok-tx:#166534;

      --warn-bg:#fef3c7;
      --warn-tx:#92400e;

      --bad-bg:#fee2e2;
      --bad-tx:#991b1b;

      --neutral-bg:#e5e7eb;
      --neutral-tx:#374151;

      --shadow-sm:0 8px 20px rgba(16,24,40,.06);
      --shadow-md:0 14px 34px rgba(16,24,40,.10);
      --shadow-lg:0 20px 50px rgba(16,24,40,.14);

      --radius-sm:12px;
      --radius-md:18px;
      --radius-lg:24px;

      --btn-h:46px;
      --transition:.22s ease;
    }

    body{
      min-height:100vh;
      padding:24px;
      font-family:Inter, Arial, Helvetica, sans-serif;
      color:var(--text);
      background:
        radial-gradient(circle at top left, rgba(238,77,45,.10), transparent 22%),
        radial-gradient(circle at top right, rgba(255,106,61,.10), transparent 18%),
        linear-gradient(180deg,#f8fafc 0%, #f2f5fa 100%);
    }

    .hidden{
      display:none !important;
    }

    .auth-wrap{
      min-height:calc(100vh - 48px);
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .login-card{
      width:100%;
      max-width:540px;
      background:rgba(255,255,255,.88);
      border:1px solid rgba(255,255,255,.9);
      backdrop-filter:blur(8px);
      box-shadow:var(--shadow-lg);
      border-radius:28px;
      padding:34px;
    }

    .brand-head{
      margin-bottom:24px;
      text-align:center;
    }

    .brand-head h1{
      font-size:38px;
      line-height:1.1;
      color:var(--brand);
      margin-bottom:10px;
      letter-spacing:.2px;
    }

    .brand-head p{
      color:var(--text-soft);
      line-height:1.6;
      font-size:15px;
    }

    .tabs{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:8px;
      background:#f3f4f6;
      border:1px solid #eceff4;
      border-radius:16px;
      padding:6px;
      margin-bottom:24px;
    }

    .tab{
      height:46px;
      border:none;
      border-radius:12px;
      background:transparent;
      color:#475467;
      font-weight:700;
      cursor:pointer;
      transition:var(--transition);
    }

    .tab.active{
      color:#fff;
      background:linear-gradient(135deg,var(--brand) 0%, var(--brand-2) 100%);
      box-shadow:0 10px 20px rgba(238,77,45,.20);
    }

    .form{
      display:none;
    }

    .form.active{
      display:block;
    }

    .field{
      margin-bottom:16px;
    }

    .field label{
      display:block;
      font-size:14px;
      font-weight:700;
      color:#2f3a48;
      margin-bottom:8px;
    }

    .input,
    .textarea{
      width:100%;
      border:1px solid var(--line-strong);
      background:#fff;
      color:var(--text);
      border-radius:14px;
      padding:13px 14px;
      font-size:15px;
      outline:none;
      transition:var(--transition);
    }

    .input:focus,
    .textarea:focus{
      border-color:rgba(238,77,45,.55);
      box-shadow:0 0 0 4px rgba(238,77,45,.10);
    }

    .btn{
      height:var(--btn-h);
      border:none;
      border-radius:14px;
      padding:0 16px;
      font-size:14px;
      font-weight:700;
      cursor:pointer;
      transition:var(--transition);
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      text-align:center;
      white-space:nowrap;
    }

    .btn:hover{
      transform:translateY(-1px);
    }

    .btn:disabled{
      opacity:.6;
      cursor:not-allowed;
      transform:none;
    }

    .btn-primary{
      background:linear-gradient(135deg,var(--brand) 0%, var(--brand-2) 100%);
      color:#fff;
      box-shadow:0 10px 20px rgba(238,77,45,.18);
    }

    .btn-dark{
      background:linear-gradient(135deg,#4b5563 0%, #374151 100%);
      color:#fff;
    }

    .btn-light{
      background:#fff;
      color:#344054;
      border:1px solid var(--line);
    }

    .btn-danger{
      background:linear-gradient(135deg,#ef4444 0%, #dc2626 100%);
      color:#fff;
    }

    .btn-success{
      background:linear-gradient(135deg,#16a34a 0%, #15803d 100%);
      color:#fff;
    }

    .btn-info{
      background:linear-gradient(135deg,#2563eb 0%, #1d4ed8 100%);
      color:#fff;
    }

    .btn-block{
      width:100%;
    }

    .msg{
      min-height:22px;
      margin-top:16px;
      text-align:center;
      font-weight:700;
      line-height:1.5;
      font-size:14px;
    }

    .erro{ color:#c62828; }
    .ok{ color:#2e7d32; }

    .dica{
      margin-top:12px;
      text-align:center;
      color:var(--text-soft);
      font-size:13px;
      line-height:1.6;
    }

    .admin-panel{
      display:none;
      max-width:1320px;
      margin:0 auto;
    }

    .topbar{
      background:rgba(255,255,255,.9);
      border:1px solid rgba(255,255,255,.9);
      backdrop-filter:blur(8px);
      border-radius:26px;
      padding:24px;
      box-shadow:var(--shadow-md);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:18px;
      flex-wrap:wrap;
      margin-bottom:24px;
    }

    .topbar-left h2{
      color:var(--brand);
      font-size:25px;
      margin-bottom:8px;
    }

    .topbar-left p{
      color:var(--text-soft);
      line-height:1.6;
      font-size:14px;
    }

    .topbar-right{
      display:flex;
      align-items:center;
      gap:12px;
      flex-wrap:wrap;
    }

    .email-badge{
      padding:10px 14px;
      border-radius:999px;
      background:var(--brand-soft);
      color:var(--brand-dark);
      font-weight:700;
      font-size:14px;
    }

    .section-shell{
      display:grid;
      gap:20px;
    }

    .cards{
      display:grid;
      grid-template-columns:repeat(auto-fit, minmax(270px, 1fr));
      gap:18px;
      align-items:stretch;
    }

    .card{
      background:var(--surface);
      border:1px solid var(--line);
      border-radius:22px;
      box-shadow:var(--shadow-sm);
      padding:22px;
      display:flex;
      flex-direction:column;
      min-height:100%;
    }

    .card-wide{
      grid-column:1 / -1;
    }

    .card-head{
      margin-bottom:14px;
    }

    .card-head h3{
      font-size:22px;
      color:var(--title);
      margin-bottom:8px;
    }

    .card-head p{
      color:var(--text-soft);
      line-height:1.7;
      font-size:14px;
    }

    .card-body{
      flex:1;
      display:flex;
      flex-direction:column;
      gap:14px;
    }

    .card-actions{
      margin-top:auto;
      display:flex;
      flex-direction:column;
      gap:10px;
    }

    .upload-box{
      border:1.5px dashed #d7dce5;
      background:linear-gradient(180deg,#fcfdff 0%, #f8fafc 100%);
      border-radius:18px;
      padding:18px;
    }

    .upload-box .field{
      margin-bottom:12px;
    }

    .file-help{
      font-size:13px;
      color:var(--text-soft);
      line-height:1.7;
    }

    .preview-box{
      display:none;
      margin-top:14px;
      border:1px solid var(--line);
      border-radius:16px;
      background:#fff;
      padding:14px;
    }

    .preview-title{
      font-weight:800;
      color:#253041;
      margin-bottom:10px;
      font-size:14px;
    }

    .preview-list{
      padding-left:18px;
      max-height:180px;
      overflow:auto;
      color:#445264;
      line-height:1.7;
      font-size:14px;
    }

    .import-msg{
      min-height:22px;
      font-weight:700;
      line-height:1.6;
      font-size:14px;
      word-break:break-word;
    }

    .module-panel{
      background:var(--surface);
      border:1px solid var(--line);
      border-radius:24px;
      box-shadow:var(--shadow-sm);
      padding:22px;
    }

    .module-head{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:16px;
      flex-wrap:wrap;
      margin-bottom:18px;
      padding-bottom:16px;
      border-bottom:1px solid var(--line);
    }

    .module-head-left h3{
      font-size:22px;
      color:var(--title);
      margin-bottom:6px;
    }

    .module-head-left p{
      color:var(--text-soft);
      font-size:14px;
      line-height:1.6;
    }

    .module-actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      align-items:center;
    }

    .table-wrap{
      overflow:auto;
      border:1px solid var(--line);
      border-radius:18px;
      background:#fff;
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
      padding:14px 12px;
      border-bottom:1px solid var(--line);
      text-align:left;
      vertical-align:middle;
    }

    th{
      font-size:12px;
      color:#475467;
      text-transform:uppercase;
      letter-spacing:.05em;
      font-weight:800;
    }

    td{
      font-size:14px;
      color:#344054;
    }

    tbody tr:hover{
      background:#fafcff;
    }

    .empty-state{
      text-align:center;
      color:var(--text-soft);
      padding:22px;
    }

    .status-badge{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:7px 12px;
      border-radius:999px;
      font-size:12px;
      font-weight:800;
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
      background:var(--neutral-bg);
      color:var(--neutral-tx);
    }

    .admin-form-grid{
      display:grid;
      grid-template-columns:minmax(0, 2fr) 220px;
      gap:12px;
      margin-bottom:16px;
      align-items:end;
    }

    .action-wrap{
      display:flex;
      align-items:center;
      gap:8px;
      flex-wrap:wrap;
    }

    .mini-btn{
      min-width:104px;
      height:38px;
      border:none;
      border-radius:10px;
      padding:0 12px;
      color:#fff;
      font-size:12px;
      font-weight:800;
      cursor:pointer;
      transition:var(--transition);
      display:inline-flex;
      align-items:center;
      justify-content:center;
    }

    .mini-btn:hover{
      transform:translateY(-1px);
    }

    .mini-btn.activate{
      background:linear-gradient(135deg,#16a34a 0%, #15803d 100%);
    }

    .mini-btn.deactivate{
      background:linear-gradient(135deg,#ef4444 0%, #dc2626 100%);
    }

    .mini-btn.reassign{
      background:linear-gradient(135deg,#2563eb 0%, #1d4ed8 100%);
    }

    .modal-overlay{
      position:fixed;
      inset:0;
      background:rgba(15,23,42,.48);
      backdrop-filter:blur(5px);
      display:none;
      align-items:center;
      justify-content:center;
      padding:20px;
      z-index:9999;
    }

    .modal{
      width:100%;
      max-width:440px;
      background:#fff;
      border-radius:24px;
      box-shadow:0 24px 70px rgba(0,0,0,.22);
      border:1px solid rgba(255,255,255,.8);
      overflow:hidden;
      animation:fadeUp .18s ease;
    }

    .modal-head{
      padding:20px 22px 12px;
      border-bottom:1px solid var(--line);
    }

    .modal-head h3{
      font-size:22px;
      color:var(--title);
      margin-bottom:6px;
    }

    .modal-head p{
      color:var(--text-soft);
      line-height:1.6;
      font-size:14px;
    }

    .modal-body{
      padding:18px 22px;
    }

    .modal-body p{
      color:#445264;
      line-height:1.7;
      font-size:14px;
    }

    .modal-input{
      margin-top:14px;
    }

    .modal-actions{
      padding:0 22px 22px;
      display:flex;
      gap:10px;
      justify-content:flex-end;
      flex-wrap:wrap;
    }

    @keyframes fadeUp{
      from{
        opacity:0;
        transform:translateY(8px) scale(.98);
      }
      to{
        opacity:1;
        transform:translateY(0) scale(1);
      }
    }

    @media (max-width: 860px){
      body{
        padding:16px;
      }

      .topbar{
        padding:18px;
      }

      .admin-form-grid{
        grid-template-columns:1fr;
      }

      .module-actions,
      .topbar-right{
        width:100%;
      }

      .module-actions .btn,
      .topbar-right .btn{
        flex:1;
      }
    }

    @media (max-width: 640px){
      .login-card{
        padding:24px 18px;
        border-radius:22px;
      }

      .brand-head h1{
        font-size:32px;
      }

      .topbar-left h2{
        font-size:22px;
      }

      .card,
      .module-panel{
        padding:16px;
      }

      .module-actions .btn,
      .card-actions .btn{
        width:100%;
      }

      .modal-actions .btn{
        width:100%;
      }
    }
  </style>
</head>
<body>

  <div class="auth-wrap" id="authWrap">
    <div class="login-card">
      <div class="brand-head">
        <h1>Admin</h1>
        <p>Entre ou cadastre-se com seu e-mail corporativo para acessar o painel administrativo.</p>
      </div>

      <div class="tabs">
        <button class="tab active" id="tabLogin" type="button">Entrar</button>
        <button class="tab" id="tabCadastro" type="button">Cadastrar</button>
      </div>

      <div id="formLogin" class="form active">
        <div class="field">
          <label for="loginEmail">E-mail</label>
          <input class="input" type="email" id="loginEmail" placeholder="seu.nome@shopee.com">
        </div>

        <div class="field">
          <label for="loginSenha">Senha</label>
          <input class="input" type="password" id="loginSenha" placeholder="Digite sua senha">
        </div>

        <button class="btn btn-primary btn-block" id="btnEntrar" type="button">Entrar</button>
      </div>

      <div id="formCadastro" class="form">
        <div class="field">
          <label for="cadEmail">E-mail corporativo</label>
          <input class="input" type="email" id="cadEmail" placeholder="seu.nome@shopee.com">
        </div>

        <div class="field">
          <label for="cadSenha">Senha</label>
          <input class="input" type="password" id="cadSenha" placeholder="Crie uma senha">
        </div>

        <button class="btn btn-primary btn-block" id="btnCadastrar" type="button">Cadastrar</button>
        <div class="dica">Somente e-mails com domínio @shopee.com podem se cadastrar.</div>
      </div>

      <div id="msg" class="msg"></div>
    </div>
  </div>

  <div class="admin-panel" id="adminPanel">
    <div class="topbar">
      <div class="topbar-left">
        <h2>Painel Administrativo</h2>
        <p>Área central para importar rotas, gerenciar admins e acompanhar respostas dos motoristas.</p>
      </div>

      <div class="topbar-right">
        <div class="email-badge">Admin: <span id="adminEmail"></span></div>
        <button class="btn btn-dark" id="btnSair" type="button">Sair</button>
      </div>
    </div>

    <div class="section-shell">
      <div class="cards">
        <div class="card card-wide">
          <div class="card-head">
            <h3>Importar Rotas</h3>
            <p>Envie um arquivo CSV, XLSX ou XLS contendo as colunas de ID e CLUSTER para disponibilizar as rotas no sistema.</p>
          </div>

          <div class="card-body">
            <div class="upload-box">
              <div class="field">
                <label for="arquivoRotas">Selecionar arquivo</label>
                <input class="input" type="file" id="arquivoRotas" accept=".csv,.xlsx,.xls">
              </div>

              <div class="file-help">
                Formatos aceitos: <strong>.csv</strong>, <strong>.xlsx</strong> e <strong>.xls</strong>.<br>
                O arquivo deve conter colunas com nomes como <strong>ID</strong> e <strong>CLUSTER</strong>.
              </div>

              <div class="preview-box" id="previewBox">
                <div class="preview-title">Prévia das rotas encontradas</div>
                <ol class="preview-list" id="previewList"></ol>
              </div>
            </div>

            <div class="card-actions">
              <button type="button" class="btn btn-primary btn-block" id="btnImportarRotas">Importar Rotas</button>
              <button type="button" class="btn btn-dark btn-block" id="btnLimparRotas">Limpar Rotas</button>
            </div>

            <div id="importMsg" class="import-msg"></div>
          </div>
        </div>

        <div class="card">
          <div class="card-head">
            <h3>Painel de Rotas</h3>
            <p>Visualize rotas importadas, acompanhe respostas, cancelamentos e faça o repasse para outro motorista.</p>
          </div>

          <div class="card-actions">
            <button type="button" class="btn btn-primary btn-block" id="btnAbrirPainelRotas">Abrir módulo</button>
          </div>
        </div>

        <div class="card">
          <div class="card-head">
            <h3>Gerenciar Admins</h3>
            <p>Controle quais e-mails corporativos possuem acesso administrativo ao painel.</p>
          </div>

          <div class="card-actions">
            <button type="button" class="btn btn-primary btn-block" id="btnAbrirAdmins">Abrir módulo</button>
          </div>
        </div>
      </div>

      <div class="module-panel hidden" id="painelRotasPanel">
        <div class="module-head">
          <div class="module-head-left">
            <h3>Painel de Rotas</h3>
            <p>Gerencie as rotas já enviadas aos motoristas e execute ações administrativas.</p>
          </div>

          <div class="module-actions">
            <button type="button" class="btn btn-primary" id="btnAtualizarPainelRotas">Atualizar</button>
            <button type="button" class="btn btn-danger" id="btnLimparRotasPainel">Limpar Rotas</button>
            <button type="button" class="btn btn-light" id="btnFecharPainelRotas">Fechar</button>
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
        <div class="module-head">
          <div class="module-head-left">
            <h3>Gerenciar Admins</h3>
            <p>Adicione, ative ou desative administradores autorizados a entrar no sistema.</p>
          </div>

          <div class="module-actions">
            <button type="button" class="btn btn-primary" id="btnAtualizarAdmins">Atualizar</button>
            <button type="button" class="btn btn-light" id="btnFecharAdmins">Fechar</button>
          </div>
        </div>

        <div class="admin-form-grid">
          <div class="field" style="margin-bottom:0;">
            <label for="novoAdminEmail">Novo e-mail admin</label>
            <input class="input" type="email" id="novoAdminEmail" placeholder="novo.admin@shopee.com">
          </div>

          <button type="button" class="btn btn-primary btn-block" id="btnAdicionarAdmin">Adicionar</button>
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
  </div>

  <div class="modal-overlay" id="appModal">
    <div class="modal">
      <div class="modal-head">
        <h3 id="modalTitle">Aviso</h3>
        <p id="modalSubtitle">Confira as informações abaixo.</p>
      </div>

      <div class="modal-body">
        <p id="modalText"></p>

        <div class="modal-input hidden" id="modalInputWrap">
          <input class="input" type="text" id="modalInput" placeholder="">
        </div>
      </div>

      <div class="modal-actions" id="modalActions">
        <button type="button" class="btn btn-light" id="modalCancelBtn">Cancelar</button>
        <button type="button" class="btn btn-primary" id="modalConfirmBtn">OK</button>
      </div>
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

    const appModal = document.getElementById("appModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalSubtitle = document.getElementById("modalSubtitle");
    const modalText = document.getElementById("modalText");
    const modalActions = document.getElementById("modalActions");
    const modalCancelBtn = document.getElementById("modalCancelBtn");
    const modalConfirmBtn = document.getElementById("modalConfirmBtn");
    const modalInputWrap = document.getElementById("modalInputWrap");
    const modalInput = document.getElementById("modalInput");

    let rotasProcessadas = [];
    let modalResolver = null;

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

    function setTextoMensagem(el, texto, cor) {
      el.textContent = texto;
      el.style.color = cor || "";
    }

    function fecharModal() {
      appModal.style.display = "none";
      modalInputWrap.classList.add("hidden");
      modalInput.value = "";
      modalInput.placeholder = "";
      modalResolver = null;
    }

    function abrirModalBase({
      title = "Aviso",
      subtitle = "",
      text = "",
      confirmText = "OK",
      cancelText = "Cancelar",
      showCancel = false,
      showInput = false,
      inputPlaceholder = "",
      confirmClass = "btn-primary"
    }) {
      modalTitle.textContent = title;
      modalSubtitle.textContent = subtitle || "Confira as informações abaixo.";
      modalText.textContent = text;

      modalConfirmBtn.textContent = confirmText;
      modalCancelBtn.textContent = cancelText;

      modalConfirmBtn.className = `btn ${confirmClass}`;
      modalCancelBtn.className = "btn btn-light";

      modalCancelBtn.style.display = showCancel ? "inline-flex" : "none";

      if (showInput) {
        modalInputWrap.classList.remove("hidden");
        modalInput.placeholder = inputPlaceholder || "";
        setTimeout(() => modalInput.focus(), 50);
      } else {
        modalInputWrap.classList.add("hidden");
        modalInput.value = "";
      }

      appModal.style.display = "flex";
    }

    function modalAlert(title, text, subtitle = "Mensagem do sistema") {
      return new Promise((resolve) => {
        abrirModalBase({
          title,
          subtitle,
          text,
          confirmText: "OK",
          showCancel: false,
          confirmClass: "btn-primary"
        });

        modalResolver = resolve;
      });
    }

    function modalConfirm(title, text, subtitle = "Confirmação necessária") {
      return new Promise((resolve) => {
        abrirModalBase({
          title,
          subtitle,
          text,
          confirmText: "Confirmar",
          cancelText: "Cancelar",
          showCancel: true,
          confirmClass: "btn-danger"
        });

        modalResolver = resolve;
      });
    }

    function modalPrompt(title, text, inputPlaceholder = "Digite aqui", subtitle = "Preencha para continuar") {
      return new Promise((resolve) => {
        abrirModalBase({
          title,
          subtitle,
          text,
          confirmText: "Continuar",
          cancelText: "Cancelar",
          showCancel: true,
          showInput: true,
          inputPlaceholder,
          confirmClass: "btn-info"
        });

        modalResolver = resolve;
      });
    }

    modalConfirmBtn.addEventListener("click", () => {
      if (!modalResolver) return fecharModal();

      const isPrompt = !modalInputWrap.classList.contains("hidden");
      if (isPrompt) {
        const valor = modalInput.value.trim();
        const resolver = modalResolver;
        fecharModal();
        resolver(valor);
        return;
      }

      const resolver = modalResolver;
      fecharModal();
      resolver(true);
    });

    modalCancelBtn.addEventListener("click", () => {
      if (!modalResolver) return fecharModal();
      const resolver = modalResolver;
      const isPrompt = !modalInputWrap.classList.contains("hidden");
      fecharModal();
      resolver(isPrompt ? "" : false);
    });

    appModal.addEventListener("click", (e) => {
      if (e.target === appModal) {
        if (!modalResolver) {
          fecharModal();
          return;
        }

        const isPrompt = !modalInputWrap.classList.contains("hidden");
        const resolver = modalResolver;
        fecharModal();
        resolver(isPrompt ? "" : false);
      }
    });

    modalInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        modalConfirmBtn.click();
      }
    });

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
      const confirmar = await modalConfirm(
        "Limpar rotas",
        "Tem certeza que deseja remover todas as rotas atuais do sistema?"
      );

      if (!confirmar) return;

      setTextoMensagem(importMsg, "", "");

      try {
        const resposta = await fetch("api/limpar_rotas.php", {
          method: "POST"
        });

        const resultado = await resposta.json();

        if (!resultado.ok) {
          setTextoMensagem(importMsg, resultado.error || "Erro ao limpar rotas.", "#c62828");
          console.error("DEBUG limpar_rotas:", resultado.debug || resultado);
          return;
        }

        setTextoMensagem(importMsg, "Todas as rotas foram removidas com sucesso.", "#2e7d32");
        await carregarPainelRotas();
      } catch (e) {
        console.error("Erro limpar rotas:", e);
        setTextoMensagem(importMsg, "Erro de comunicação ao limpar rotas.", "#c62828");
      }
    }

    async function cancelarRota(routeId) {
      const confirmar = await modalConfirm(
        "Cancelar rota",
        "Deseja realmente cancelar esta rota?"
      );

      if (!confirmar) return;

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
          await modalAlert("Erro", resultado.error || "Erro ao cancelar rota.");
          return;
        }

        await carregarPainelRotas();
      } catch (e) {
        await modalAlert("Erro", "Erro de comunicação ao cancelar rota.");
      }
    }

    async function repassarRota(routeId) {
      const novoDriver = await modalPrompt(
        "Repassar rota",
        "Digite o novo ID do motorista para receber esta rota.",
        "Ex.: 123456"
      );

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
          await modalAlert("Erro", resultado.error || "Erro ao repassar rota.");
          return;
        }

        await carregarPainelRotas();
      } catch (e) {
        await modalAlert("Erro", "Erro de comunicação ao repassar rota.");
      }
    }

    window.cancelarRota = cancelarRota;
    window.repassarRota = repassarRota;
    window.limparRotas = limparRotas;

    async function carregarAdmins() {
      adminsPanel.classList.remove("hidden");
      setTextoMensagem(adminManageMsg, "", "");
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
          setTextoMensagem(adminManageMsg, resultado.error || "Erro ao atualizar admin.", "#c62828");
          return;
        }

        setTextoMensagem(
          adminManageMsg,
          active ? "Admin ativado com sucesso." : "Admin desativado com sucesso.",
          "#2e7d32"
        );

        await carregarAdmins();
      } catch (e) {
        setTextoMensagem(adminManageMsg, "Erro de comunicação ao atualizar admin.", "#c62828");
      }
    }

    window.alterarAdmin = alterarAdmin;

    tabLogin.addEventListener("click", mostrarLogin);
    tabCadastro.addEventListener("click", mostrarCadastro);

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
      setTextoMensagem(importMsg, "", "");
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
          setTextoMensagem(importMsg, "Não foi possível encontrar colunas válidas de ID e CLUSTER no arquivo.", "#c62828");
          return;
        }

        setTextoMensagem(importMsg, `${rotas.length} rota(s) encontrada(s) no arquivo.`, "#2e7d32");
      } catch (error) {
        setTextoMensagem(importMsg, "Erro ao ler o arquivo selecionado.", "#c62828");
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
        await modalAlert("Erro no cadastro", error.message);
        return;
      }

      cadEmail.value = "";
      cadSenha.value = "";
      mostrarLogin();

      await modalAlert(
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
        setTextoMensagem(adminManageMsg, "Digite um e-mail para adicionar.", "#c62828");
        return;
      }

      if (!emailShopeeValido(email)) {
        setTextoMensagem(adminManageMsg, "Somente e-mails @shopee.com podem ser adicionados.", "#c62828");
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
          setTextoMensagem(adminManageMsg, resultado.error || "Erro ao adicionar admin.", "#c62828");
          return;
        }

        setTextoMensagem(adminManageMsg, "Admin adicionado com sucesso.", "#2e7d32");
        novoAdminEmail.value = "";
        await carregarAdmins();
      } catch (e) {
        setTextoMensagem(adminManageMsg, "Erro de comunicação ao adicionar admin.", "#c62828");
      }
    });

    btnImportarRotas.addEventListener("click", async () => {
      setTextoMensagem(importMsg, "", "");

      if (!rotasProcessadas.length) {
        setTextoMensagem(importMsg, "Selecione um arquivo CSV ou XLSX com rotas válidas.", "#c62828");
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

          setTextoMensagem(importMsg, detalhe, "#c62828");
          return;
        }

        let sucesso = `Importação concluída. ${resultado.total} rota(s) salva(s).`;

        if (resultado.nao_encontrados && resultado.nao_encontrados.length) {
          sucesso += ` IDs não encontrados: ${resultado.nao_encontrados.join(", ")}`;
        }

        if (resultado.erros_insert && resultado.erros_insert.length) {
          sucesso += ` | Falhas: ${JSON.stringify(resultado.erros_insert)}`;
        }

        setTextoMensagem(importMsg, sucesso, "#2e7d32");

        arquivoRotas.value = "";
        rotasProcessadas = [];
        previewBox.style.display = "none";
        previewList.innerHTML = "";

        await carregarPainelRotas();
      } catch (e) {
        setTextoMensagem(importMsg, "Erro de comunicação com o servidor.", "#c62828");
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