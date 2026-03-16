<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head(); ?>
<link rel="stylesheet" href="<?php echo module_dir_url('axiomchannel', 'assets/css/axiomchannel.css'); ?>">

<div id="wrapper">
<div class="content" style="max-width:700px;margin:0 auto;padding:32px 16px">

  <div class="ax-page-header" style="margin-bottom:24px;padding:0">
    <div>
      <h4 class="ax-page-title">
        <i class="fa fa-facebook-square" style="color:#1877F2;margin-right:8px"></i>
        Conectar Facebook &amp; Instagram
      </h4>
      <p class="ax-page-sub">Receba mensagens do Messenger e Instagram Direct no AXIOM</p>
    </div>
    <a href="<?= admin_url('axiomchannel/devices') ?>" class="ax-btn ax-btn-sm">
      <i class="fa fa-arrow-left"></i> Voltar
    </a>
  </div>

  <?php if (!empty($meta_connection)): ?>
  <div style="background:var(--ax-teal-light);border:1px solid var(--ax-teal-mid);border-radius:12px;padding:16px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
    <i class="fa fa-check-circle" style="color:var(--ax-teal);font-size:20px"></i>
    <div>
      <div style="font-size:13px;font-weight:600;color:var(--ax-navy)">Conectado!</div>
      <div style="font-size:12px;color:var(--ax-teal)">
        Página: <?= htmlspecialchars($meta_connection->page_name) ?>
        <?php if (!empty($meta_connection->instagram_username)): ?>
          &middot; Instagram: @<?= htmlspecialchars($meta_connection->instagram_username) ?>
        <?php endif; ?>
      </div>
    </div>
    <button onclick="disconnectMeta()" class="ax-btn ax-btn-danger ax-btn-sm" style="margin-left:auto">
      <i class="fa fa-unlink"></i> Desconectar
    </button>
  </div>
  <?php endif; ?>

  <div style="background:#fff;border:1px solid var(--ax-gray-200);border-radius:16px;padding:24px;margin-bottom:16px">

    <div class="ax-form-group">
      <label class="ax-label">Vincular ao dispositivo</label>
      <select id="device_id" class="ax-input">
        <?php foreach ($devices as $d): ?>
          <option value="<?= $d->id ?>"><?= htmlspecialchars($d->name) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="text-align:center;padding:20px 0">
      <div id="fb-login-btn">
        <button onclick="connectFacebook()"
          style="background:#1877F2;color:#fff;border:none;border-radius:8px;padding:12px 24px;font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:10px">
          <i class="fa fa-facebook" style="font-size:18px"></i>
          Entrar com Facebook
        </button>
        <p style="font-size:12px;color:var(--ax-gray-400);margin-top:12px">
          Você será redirecionado para o Facebook para autorizar o acesso
        </p>
      </div>

      <div id="pages-section" style="display:none;text-align:left">
        <div class="ax-label" style="margin-bottom:8px">Selecione a Página do Facebook:</div>
        <div id="pages-list" style="display:flex;flex-direction:column;gap:8px"></div>
        <button id="save-btn" onclick="saveConnection()"
          class="ax-btn ax-btn-primary" style="margin-top:16px;width:100%;justify-content:center" disabled>
          <i class="fa fa-save"></i> Salvar conexão
        </button>
        <div id="save-status" style="text-align:center;margin-top:8px;font-size:12px"></div>
      </div>
    </div>
  </div>

  <div style="background:var(--ax-gray-50);border-radius:12px;padding:16px">
    <div style="font-size:12px;font-weight:600;color:var(--ax-navy);margin-bottom:10px">
      <i class="fa fa-info-circle" style="color:var(--ax-teal)"></i>
      Como vai funcionar após conectar:
    </div>
    <div style="display:flex;flex-direction:column;gap:6px">
      <div style="font-size:12px;color:var(--ax-gray-600);display:flex;gap:8px">
        <span style="color:var(--ax-teal);font-weight:600">1.</span>
        Mensagens do Facebook Messenger chegam no inbox do AXIOM
      </div>
      <div style="font-size:12px;color:var(--ax-gray-600);display:flex;gap:8px">
        <span style="color:var(--ax-teal);font-weight:600">2.</span>
        Mensagens do Instagram Direct também aparecem no mesmo inbox
      </div>
      <div style="font-size:12px;color:var(--ax-gray-600);display:flex;gap:8px">
        <span style="color:var(--ax-teal);font-weight:600">3.</span>
        O Assistente IA responde automaticamente em todos os canais
      </div>
      <div style="font-size:12px;color:var(--ax-gray-600);display:flex;gap:8px">
        <span style="color:var(--ax-teal);font-weight:600">4.</span>
        Configure o webhook na Meta: <?= base_url('admin/axiomchannel/meta_webhook') ?>
      </div>
    </div>
  </div>

</div>
</div>

<script>
const ADMIN_URL  = '<?= admin_url() ?>';
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
const META_APP_ID = '921276330774376';

let selectedPage = null;

// Carrega o Facebook SDK
window.fbAsyncInit = function() {
  FB.init({
    appId   : META_APP_ID,
    cookie  : true,
    xfbml   : true,
    version : 'v18.0'
  });
};

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "https://connect.facebook.net/pt_BR/sdk.js";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function connectFacebook() {
  FB.login(function(response) {
    if (response.authResponse) {
      loadPages(response.authResponse.accessToken);
    } else {
      alert('Login cancelado ou não autorizado.');
    }
  }, {
    scope: 'pages_show_list,pages_messaging,instagram_basic,instagram_manage_messages,pages_read_engagement',
    return_scopes: true
  });
}

function loadPages(accessToken) {
  FB.api('/me/accounts', { access_token: accessToken }, function(data) {
    if (!data || !data.data || data.data.length === 0) {
      alert('Nenhuma Página encontrada. Você precisa ser admin de uma Página do Facebook.');
      return;
    }

    const list = document.getElementById('pages-list');
    list.innerHTML = '';

    data.data.forEach(function(page) {
      const div = document.createElement('div');
      div.style.cssText = 'border:1px solid var(--ax-gray-200);border-radius:8px;padding:12px;cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:10px';
      div.innerHTML =
        '<div style="width:36px;height:36px;border-radius:50%;background:#1877F2;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0">'
          + page.name[0].toUpperCase() +
        '</div>' +
        '<div style="flex:1">' +
          '<div style="font-size:13px;font-weight:500;color:var(--ax-navy)">' + _esc(page.name) + '</div>' +
          '<div style="font-size:11px;color:var(--ax-gray-400)">ID: ' + page.id + '</div>' +
        '</div>' +
        '<div id="ig-' + page.id + '" style="font-size:11px;color:var(--ax-gray-400)">Verificando Instagram...</div>';

      div.onclick = function() {
        document.querySelectorAll('#pages-list > div').forEach(function(c) {
          c.style.borderColor = 'var(--ax-gray-200)';
          c.style.background  = '#fff';
        });
        div.style.borderColor = 'var(--ax-teal)';
        div.style.background  = 'var(--ax-teal-light)';
        selectedPage = {
          id           : page.id,
          name         : page.name,
          access_token : page.access_token
        };
        document.getElementById('save-btn').disabled = false;
        checkInstagram(page.id, page.access_token);
      };

      list.appendChild(div);
      checkInstagramQuick(page.id, page.access_token);
    });

    document.getElementById('fb-login-btn').style.display   = 'none';
    document.getElementById('pages-section').style.display  = 'block';
  });
}

function checkInstagramQuick(pageId, pageToken) {
  FB.api('/' + pageId + '?fields=instagram_business_account&access_token=' + pageToken, function(data) {
    var el = document.getElementById('ig-' + pageId);
    if (!el) return;
    if (data && data.instagram_business_account) {
      el.innerHTML = '<span style="background:#E1306C;color:#fff;padding:2px 7px;border-radius:4px;font-size:10px;font-weight:600"><i class="fa fa-instagram"></i> Instagram conectado</span>';
    } else {
      el.innerHTML = '<span style="color:var(--ax-gray-400);font-size:10px">Sem Instagram</span>';
    }
  });
}

function checkInstagram(pageId, pageToken) {
  FB.api('/' + pageId + '?fields=instagram_business_account{username,id}&access_token=' + pageToken, function(data) {
    if (data && data.instagram_business_account) {
      selectedPage.instagram_id       = data.instagram_business_account.id;
      selectedPage.instagram_username = data.instagram_business_account.username || '';
    }
  });
}

function saveConnection() {
  if (!selectedPage) return;

  var btn    = document.getElementById('save-btn');
  var status = document.getElementById('save-status');
  btn.disabled    = true;
  btn.innerHTML   = '<i class="fa fa-spinner fa-spin"></i> Salvando...';
  status.innerHTML = '';

  fetch(ADMIN_URL + 'axiomchannel/meta_save', {
    method  : 'POST',
    headers : {
      'Content-Type'     : 'application/x-www-form-urlencoded',
      'X-Requested-With' : 'XMLHttpRequest'
    },
    body: new URLSearchParams({
      device_id            : document.getElementById('device_id').value,
      page_id              : selectedPage.id,
      page_name            : selectedPage.name,
      page_access_token    : selectedPage.access_token,
      instagram_account_id : selectedPage.instagram_id       || '',
      instagram_username   : selectedPage.instagram_username || '',
      [CSRF_NAME]          : CSRF_TOKEN
    })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      window.location.reload();
    } else {
      status.innerHTML = '<span style="color:#E53E3E">' + _esc(data.message || 'Erro ao salvar') + '</span>';
      btn.disabled  = false;
      btn.innerHTML = '<i class="fa fa-save"></i> Salvar conexão';
    }
  })
  .catch(function() {
    status.innerHTML = '<span style="color:#E53E3E">Erro de rede. Tente novamente.</span>';
    btn.disabled  = false;
    btn.innerHTML = '<i class="fa fa-save"></i> Salvar conexão';
  });
}

function disconnectMeta() {
  if (!confirm('Desconectar Facebook e Instagram?')) return;
  fetch(ADMIN_URL + 'axiomchannel/meta_disconnect', {
    method  : 'POST',
    headers : {
      'Content-Type'     : 'application/x-www-form-urlencoded',
      'X-Requested-With' : 'XMLHttpRequest'
    },
    body: new URLSearchParams({
      device_id   : document.getElementById('device_id').value,
      [CSRF_NAME] : CSRF_TOKEN
    })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) { if (data.success) window.location.reload(); });
}

function _esc(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
</script>

<?php init_tail(); ?>
