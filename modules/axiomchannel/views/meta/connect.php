<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<div id="wrapper">
<div class="content">

  <div class="row">
    <div class="col-md-8 col-md-offset-2">

      <div class="panel_s">
        <div class="panel-body">

          <h4 class="no-margin-top" style="color:#2D7A6B;display:flex;align-items:center;gap:10px">
            <i class="fa fa-facebook-square" style="color:#1877F2;font-size:28px"></i>
            <i class="fa fa-instagram" style="color:#E1306C;font-size:28px"></i>
            Conectar Facebook & Instagram
          </h4>
          <p class="text-muted" style="margin-top:8px">
            Conecte sua Página do Facebook para receber mensagens do <strong>Messenger</strong> e
            <strong>Instagram Direct</strong> diretamente no AXIOM Todas as Conversas.
          </p>
          <hr>

          <!-- Seletor de dispositivo -->
          <div class="form-group">
            <label>Dispositivo AXIOM</label>
            <select id="meta-device-id" class="form-control">
              <option value="">Selecione um dispositivo...</option>
              <?php foreach ($devices as $d): ?>
                <option value="<?= $d->id ?>"><?= htmlspecialchars($d->name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- App ID -->
          <div class="form-group">
            <label>App ID do Facebook</label>
            <input type="text" id="meta-app-id" class="form-control"
              placeholder="Ex: 1234567890123456"
              value="<?= htmlspecialchars($app_id) ?>">
            <small class="text-muted">Encontre em developers.facebook.com → seu app → Painel</small>
          </div>

          <!-- Botão conectar com Facebook -->
          <button id="btn-fb-login" class="btn btn-primary" style="background:#1877F2;border-color:#1877F2">
            <i class="fa fa-facebook"></i> Conectar com Facebook
          </button>

          <!-- Resultado do login — oculto até conectar -->
          <div id="meta-pages-section" style="display:none;margin-top:20px">
            <hr>
            <h5>Páginas disponíveis</h5>
            <p class="text-muted">Selecione a página que deseja conectar ao AXIOM:</p>
            <div id="meta-pages-list"></div>

            <!-- Dados da página selecionada (preenchidos via JS) -->
            <input type="hidden" id="meta-page-id" value="">
            <input type="hidden" id="meta-page-name" value="">
            <input type="hidden" id="meta-page-token" value="">
            <input type="hidden" id="meta-ig-id" value="">
            <input type="hidden" id="meta-ig-user" value="">

            <div id="meta-ig-info" style="display:none;margin:12px 0;padding:12px;background:#fdf0f8;border-radius:8px;border-left:4px solid #E1306C">
              <i class="fa fa-instagram" style="color:#E1306C"></i>
              Instagram vinculado: <strong id="meta-ig-username-display"></strong>
            </div>

            <button id="btn-meta-save" class="btn btn-success" style="margin-top:10px" disabled>
              <i class="fa fa-save"></i> Salvar Conexão
            </button>
            <span id="meta-save-status" style="margin-left:10px"></span>
          </div>

        </div>
      </div>

      <!-- Como configurar -->
      <div class="panel_s">
        <div class="panel-body">
          <h5 style="color:#2D7A6B"><i class="fa fa-info-circle"></i> Como configurar</h5>
          <ol style="line-height:2">
            <li>Acesse <a href="https://developers.facebook.com" target="_blank">developers.facebook.com</a> e crie um App (tipo: <em>Business</em>)</li>
            <li>Ative os produtos: <strong>Messenger</strong> e <strong>Instagram Basic Display</strong></li>
            <li>Em <em>Configurações do Webhook</em>, aponte para:
              <code style="background:#f4f4f4;padding:2px 8px;border-radius:4px">
                <?= base_url('admin/axiomchannel/meta_webhook') ?>
              </code>
            </li>
            <li>Token de verificação: <code style="background:#f4f4f4;padding:2px 8px;border-radius:4px">axiom_meta_webhook</code></li>
            <li>Cole o <strong>App ID</strong> acima, selecione o dispositivo e clique em <em>Conectar com Facebook</em></li>
          </ol>
        </div>
      </div>

    </div>
  </div>

</div>
</div>

<script>
const ADMIN_URL  = '<?= admin_url() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';

// Facebook SDK
window.fbAsyncInit = function() {
  FB.init({
    appId   : document.getElementById('meta-app-id').value || '<?= htmlspecialchars($app_id) ?>',
    cookie  : true,
    xfbml   : true,
    version : 'v18.0'
  });
};

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/pt_BR/sdk.js';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

document.getElementById('btn-fb-login').addEventListener('click', function() {
  const appId = document.getElementById('meta-app-id').value.trim();
  if (!appId) { alert('Informe o App ID do Facebook primeiro.'); return; }

  // Reinicia o SDK com o App ID digitado
  FB.init({ appId: appId, cookie: true, xfbml: true, version: 'v18.0' });

  FB.login(function(response) {
    if (response.authResponse) {
      const accessToken = response.authResponse.accessToken;
      _loadPages(accessToken);
    } else {
      alert('Login cancelado ou não autorizado.');
    }
  }, { scope: 'pages_messaging,pages_show_list,instagram_basic,instagram_manage_messages,pages_read_engagement' });
});

function _loadPages(userToken) {
  FB.api('/me/accounts', { access_token: userToken }, function(res) {
    if (!res || res.error) { alert('Erro ao buscar páginas: ' + (res.error ? res.error.message : 'desconhecido')); return; }

    const container = document.getElementById('meta-pages-list');
    container.innerHTML = '';
    document.getElementById('meta-pages-section').style.display = 'block';

    if (!res.data || res.data.length === 0) {
      container.innerHTML = '<p class="text-warning">Nenhuma página encontrada nessa conta.</p>';
      return;
    }

    res.data.forEach(function(page) {
      const card = document.createElement('div');
      card.style.cssText = 'border:1px solid #ddd;border-radius:8px;padding:12px;margin-bottom:8px;cursor:pointer;transition:background .2s';
      card.innerHTML = '<strong>' + page.name + '</strong><br><small class="text-muted">ID: ' + page.id + '</small>';
      card.addEventListener('click', function() {
        document.querySelectorAll('#meta-pages-list > div').forEach(c => c.style.background = '');
        card.style.background = '#e8f5e9';
        document.getElementById('meta-page-id').value    = page.id;
        document.getElementById('meta-page-name').value  = page.name;
        document.getElementById('meta-page-token').value = page.access_token;
        _loadInstagram(page.id, page.access_token);
        document.getElementById('btn-meta-save').disabled = false;
      });
      container.appendChild(card);
    });
  });
}

function _loadInstagram(pageId, pageToken) {
  FB.api('/' + pageId + '?fields=instagram_business_account{id,username}&access_token=' + pageToken, function(res) {
    if (res && res.instagram_business_account) {
      const ig = res.instagram_business_account;
      document.getElementById('meta-ig-id').value           = ig.id;
      document.getElementById('meta-ig-user').value         = ig.username || '';
      document.getElementById('meta-ig-username-display').textContent = ig.username || ig.id;
      document.getElementById('meta-ig-info').style.display = 'block';
    } else {
      document.getElementById('meta-ig-id').value           = '';
      document.getElementById('meta-ig-user').value         = '';
      document.getElementById('meta-ig-info').style.display = 'none';
    }
  });
}

document.getElementById('btn-meta-save').addEventListener('click', function() {
  const deviceId = document.getElementById('meta-device-id').value;
  const pageId   = document.getElementById('meta-page-id').value;
  const token    = document.getElementById('meta-page-token').value;

  if (!deviceId) { alert('Selecione um dispositivo.'); return; }
  if (!pageId || !token) { alert('Selecione uma página primeiro.'); return; }

  const btn = this;
  btn.disabled = true;
  document.getElementById('meta-save-status').textContent = 'Salvando...';

  fetch(ADMIN_URL + 'axiomchannel/meta_save', {
    method  : 'POST',
    headers : { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body    : new URLSearchParams({
      device_id              : deviceId,
      page_id                : pageId,
      page_name              : document.getElementById('meta-page-name').value,
      page_access_token      : token,
      instagram_account_id   : document.getElementById('meta-ig-id').value,
      instagram_username     : document.getElementById('meta-ig-user').value,
      [CSRF_NAME]            : CSRF_TOKEN,
    })
  })
  .then(r => r.json())
  .then(function(data) {
    if (data.success) {
      document.getElementById('meta-save-status').innerHTML = '<span style="color:#38A169"><i class="fa fa-check"></i> Conexão salva!</span>';
    } else {
      document.getElementById('meta-save-status').innerHTML = '<span style="color:#E53E3E">' + (data.message || 'Erro ao salvar') + '</span>';
      btn.disabled = false;
    }
  })
  .catch(function() {
    document.getElementById('meta-save-status').innerHTML = '<span style="color:#E53E3E">Erro de rede</span>';
    btn.disabled = false;
  });
});
</script>

<?php init_tail(); ?>
