<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($contract->title) ?> — Assinatura Digital</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f1f5f9;min-height:100vh;padding:20px}
    .container{max-width:760px;margin:0 auto}
    .header{background:#1e293b;color:#fff;border-radius:12px 12px 0 0;padding:20px 28px;display:flex;align-items:center;gap:12px}
    .header h1{font-size:18px;font-weight:700}
    .header p{font-size:12px;opacity:.7;margin-top:2px}
    .contract-body{background:#fff;padding:32px 36px;line-height:1.8;font-size:14px;color:#1e293b;white-space:pre-wrap;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0}
    .sign-panel{background:#fff;border:1px solid #e2e8f0;border-radius:0 0 12px 12px;padding:28px 36px}
    .sign-panel h2{font-size:16px;font-weight:700;color:#1e293b;margin-bottom:20px}
    .form-group{margin-bottom:16px}
    .form-group label{display:block;font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
    .form-group input{width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;color:#1e293b;outline:none;transition:.2s}
    .form-group input:focus{border-color:#2D7A6B;box-shadow:0 0 0 3px rgba(45,122,107,.1)}
    .checkbox-wrap{display:flex;align-items:flex-start;gap:10px;padding:14px;background:#f8fafc;border-radius:8px;margin-bottom:20px}
    .checkbox-wrap input[type=checkbox]{width:18px;height:18px;margin-top:2px;cursor:pointer;flex-shrink:0}
    .checkbox-wrap label{font-size:13px;color:#1e293b;cursor:pointer}
    .btn-sign{width:100%;padding:14px;background:#2D7A6B;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:.2s}
    .btn-sign:hover{background:#25664f}
    .btn-sign:disabled{background:#94a3b8;cursor:not-allowed}
    .success-box{text-align:center;padding:40px 20px}
    .success-box .check{width:72px;height:72px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:36px;margin:0 auto 20px}
    .success-box h2{font-size:20px;font-weight:700;color:#1e293b;margin-bottom:8px}
    .success-box p{font-size:14px;color:#64748b}
    .meta-info{font-size:11px;color:#94a3b8;text-align:center;margin-top:20px}
    .already-signed{background:#f0fdf9;border:1px solid #a7f3d0;border-radius:10px;padding:20px;text-align:center;margin-bottom:20px}
  </style>
</head>
<body>
<div class="container">

  <div class="header">
    <div style="width:40px;height:40px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px">📄</div>
    <div>
      <h1><?= htmlspecialchars($contract->title) ?></h1>
      <p>Documento para assinatura digital</p>
    </div>
  </div>

  <div class="contract-body"><?= nl2br(htmlspecialchars($contract->content)) ?></div>

  <div class="sign-panel">

    <?php if ($already_signed): ?>
      <div class="already-signed">
        <div style="font-size:24px;margin-bottom:8px">✅</div>
        <div style="font-size:14px;font-weight:600;color:#1e293b">Este contrato já foi assinado</div>
        <div style="font-size:12px;color:#64748b;margin-top:4px">
          Assinado por <strong><?= htmlspecialchars($contract->signer_name) ?></strong>
          em <?= date('d/m/Y \à\s H:i', strtotime($contract->signed_at)) ?>
        </div>
      </div>
    <?php else: ?>

      <h2>✍️ Assinar contrato</h2>

      <div id="sign-form">
        <div class="form-group">
          <label>Nome completo</label>
          <input type="text" id="signer-name" placeholder="Seu nome completo como no documento">
        </div>
        <div class="form-group">
          <label>CPF</label>
          <input type="text" id="signer-cpf" placeholder="000.000.000-00" maxlength="14"
            oninput="this.value=this.value.replace(/\D/g,'').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2')">
        </div>

        <div class="checkbox-wrap">
          <input type="checkbox" id="accept-terms">
          <label for="accept-terms">
            Li e entendi o conteúdo completo deste contrato e concordo com todos os termos e condições descritos acima.
            Reconheço que esta assinatura eletrônica tem validade jurídica nos termos da Lei nº 14.063/2020.
          </label>
        </div>

        <button class="btn-sign" id="btn-sign" onclick="submitSignature()" disabled>
          Assinar contrato digitalmente
        </button>
      </div>

      <div id="success-box" class="success-box" style="display:none">
        <div class="check">✅</div>
        <h2>Contrato assinado!</h2>
        <p>Sua assinatura foi registrada com sucesso.<br>Você receberá uma confirmação em breve.</p>
        <p class="meta-info" id="sign-hash"></p>
      </div>

    <?php endif; ?>

    <div class="meta-info" style="margin-top:16px">
      Este documento é protegido por hash SHA-256 e registra IP e data/hora da assinatura.
    </div>
  </div>

</div>

<script>
document.getElementById('accept-terms')?.addEventListener('change', function() {
  document.getElementById('btn-sign').disabled = !this.checked;
});

function submitSignature() {
  const name = document.getElementById('signer-name')?.value?.trim();
  const cpf  = document.getElementById('signer-cpf')?.value?.trim();

  if (!name) { alert('Informe seu nome completo'); return; }
  if (!cpf || cpf.length < 14) { alert('Informe um CPF válido'); return; }

  const btn = document.getElementById('btn-sign');
  btn.disabled = true;
  btn.textContent = 'Registrando assinatura...';

  fetch('<?= base_url('axiomchannel/contract_sign_submit') ?>', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({
      token:       '<?= htmlspecialchars($contract->sign_token) ?>',
      signer_name: name,
      signer_cpf:  cpf,
      accepted:    1
    })
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      document.getElementById('sign-form').style.display = 'none';
      document.getElementById('success-box').style.display = 'block';
    } else {
      alert(res.message || 'Erro ao registrar assinatura');
      btn.disabled = false;
      btn.textContent = 'Assinar contrato digitalmente';
    }
  })
  .catch(() => {
    alert('Erro de conexão. Tente novamente.');
    btn.disabled = false;
    btn.textContent = 'Assinar contrato digitalmente';
  });
}
</script>
</body>
</html>
