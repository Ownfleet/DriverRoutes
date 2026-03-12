<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Vincular ID</title>
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
  <style>
    *{box-sizing:border-box}
    body{
      margin:0;
      min-height:100vh;
      font-family:Arial, Helvetica, sans-serif;
      background:linear-gradient(180deg,#f8fafc 0%, #f2f4f8 100%);
      display:flex;
      align-items:center;
      justify-content:center;
      padding:24px;
    }
    .box{
      width:100%;
      max-width:520px;
      background:#fff;
      border-radius:22px;
      padding:32px;
      box-shadow:0 12px 30px rgba(16,24,40,.08);
    }
    h1{
      margin:0 0 10px;
      text-align:center;
      color:#ee4d2d;
      font-size:32px;
    }
    p{
      text-align:center;
      color:#667085;
      line-height:1.5;
    }
    label{
      display:block;
      margin:12px 0 6px;
      font-weight:bold;
    }
    input{
      width:100%;
      padding:13px 14px;
      border:1px solid #d9dde6;
      border-radius:12px;
      font-size:15px;
    }
    .action{
      width:100%;
      border:none;
      margin-top:18px;
      padding:14px;
      border-radius:12px;
      background:linear-gradient(135deg,#ee4d2d 0%, #ff6a3d 100%);
      color:#fff;
      font-weight:bold;
      font-size:16px;
      cursor:pointer;
    }
    .box-name{
      margin-top:18px;
      padding:16px;
      border-radius:14px;
      background:#fff7f5;
      border:1px solid rgba(238,77,45,.16);
      display:none;
    }
    .actions{
      display:flex;
      gap:10px;
      margin-top:14px;
    }
    .btn2{
      flex:1;
      border:none;
      padding:12px;
      border-radius:10px;
      cursor:pointer;
      font-weight:bold;
      color:#fff;
    }
    .confirm{background:#198754}
    .cancel{background:#444}
    .msg{margin-top:14px;text-align:center;font-weight:bold;min-height:20px}
    .erro{color:#c62828}
    .ok{color:#2e7d32}
  </style>
</head>
<body>
  <div class="box">
    <h1>Vincular ID</h1>
    <p>No primeiro acesso, informe seu ID de motorista.</p>

    <label for="driverCode">ID do motorista</label>
    <input type="text" id="driverCode" placeholder="Ex: 1205">

    <button class="action" id="btnBuscar" type="button">Buscar motorista</button>

    <div class="box-name" id="boxName">
      <div>Esse é o seu nome?</div>
      <h3 id="nomeMotorista" style="margin:10px 0 0;color:#ee4d2d;"></h3>
      <div class="actions">
        <button class="btn2 confirm" id="btnConfirmar" type="button">Sim, confirmar</button>
        <button class="btn2 cancel" id="btnCancelar" type="button">Cancelar</button>
      </div>
    </div>

    <div class="msg" id="msg"></div>
  </div>

  <script>
    const supabaseClient = window.supabase.createClient(
      "<?= SUPABASE_URL ?>",
      "<?= SUPABASE_ANON_KEY ?>"
    );

    const driverCode = document.getElementById("driverCode");
    const btnBuscar = document.getElementById("btnBuscar");
    const btnConfirmar = document.getElementById("btnConfirmar");
    const btnCancelar = document.getElementById("btnCancelar");
    const boxName = document.getElementById("boxName");
    const nomeMotorista = document.getElementById("nomeMotorista");
    const msg = document.getElementById("msg");

    let motoristaAtual = null;

    function erro(t){
      msg.className = "msg erro";
      msg.textContent = t;
    }

    function ok(t){
      msg.className = "msg ok";
      msg.textContent = t;
    }

    btnBuscar.addEventListener("click", async () => {
      msg.textContent = "";
      boxName.style.display = "none";
      motoristaAtual = null;

      const code = driverCode.value.trim();
      if (!code) {
        erro("Digite o ID do motorista.");
        return;
      }

      const { data } = await supabaseClient.auth.getSession();
      const user = data?.session?.user;
      if (!user) {
        window.location.href = "index.php";
        return;
      }

      try {
        const resposta = await fetch("api/vincular_motorista.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            mode: "preview",
            auth_user_id: user.id,
            email: user.email,
            driver_code: code
          })
        });

        const resultado = await resposta.json();

        if (!resultado.ok) {
          erro(resultado.error || "Motorista não encontrado.");
          return;
        }

        motoristaAtual = resultado.driver;
        nomeMotorista.textContent = motoristaAtual.full_name || "Sem nome";
        boxName.style.display = "block";
      } catch (e) {
        erro("Erro de comunicação.");
      }
    });

    btnCancelar.addEventListener("click", () => {
      motoristaAtual = null;
      boxName.style.display = "none";
    });

    btnConfirmar.addEventListener("click", async () => {
      if (!motoristaAtual) return;

      const { data } = await supabaseClient.auth.getSession();
      const user = data?.session?.user;
      if (!user) {
        window.location.href = "index.php";
        return;
      }

      try {
        const resposta = await fetch("api/vincular_motorista.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            mode: "confirm",
            auth_user_id: user.id,
            email: user.email,
            driver_code: motoristaAtual.driver_code
          })
        });

        const resultado = await resposta.json();

        if (!resultado.ok) {
          erro(resultado.error || "Erro ao vincular motorista.");
          return;
        }

        ok("Conta vinculada com sucesso.");
        setTimeout(() => {
          window.location.href = "dashboard.php";
        }, 900);
      } catch (e) {
        erro("Erro de comunicação.");
      }
    });

    window.addEventListener("load", async () => {
      const { data } = await supabaseClient.auth.getSession();
      if (!data?.session?.user) {
        window.location.href = "index.php";
      }
    });
  </script>
</body>
</html>