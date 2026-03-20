<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="axiom-topbar">
  <!-- Left: hamburger + logo -->
  <div class="axiom-topbar-left">
    <button id="axiom-sidebar-toggle" class="axiom-topbar-btn" title="Recolher menu" aria-label="Toggle sidebar">
      <i class="fa fa-bars"></i>
    </button>
    <a href="<?= admin_url('axiomchannel/spa') ?>" class="axiom-topbar-brand">
      <span class="axiom-topbar-brand-text">AXIOM</span>
      <span class="axiom-topbar-brand-sub">CRM</span>
    </a>
  </div>

  <!-- Center: search -->
  <div class="axiom-topbar-center">
    <div class="axiom-topbar-search">
      <i class="fa fa-search axiom-search-icon"></i>
      <input type="text" id="axiom-topbar-search-input" placeholder="Buscar contatos, leads, contratos..." autocomplete="off">
    </div>
  </div>

  <!-- Right: weather, notifications, SPA, avatar -->
  <div class="axiom-topbar-right">
    <div id="axiom-weather" class="axiom-topbar-weather">
      <i class="fa fa-cloud"></i>
    </div>

    <a href="<?= admin_url('axiomchannel/spa') ?>" class="axiom-topbar-btn axiom-spa-btn" title="Abrir AxiomChannel">
      <i class="fa fa-comments"></i>
      <span class="axiom-topbar-btn-label">Inbox</span>
    </a>

    <!-- Avatar dropdown -->
    <div class="axiom-avatar-wrapper">
      <button id="axiom-avatar-trigger" class="axiom-avatar-trigger" aria-haspopup="true" aria-expanded="false">
        <?php if (!empty($avatar_url)): ?>
          <img src="<?= htmlspecialchars($avatar_url) ?>" alt="Avatar" class="axiom-topbar-avatar axiom-avatar-img">
        <?php else: ?>
          <div class="axiom-avatar-initials">
            <?= strtoupper(substr($staff->firstname ?? 'A', 0, 1)) . strtoupper(substr($staff->lastname ?? '', 0, 1)) ?>
          </div>
        <?php endif; ?>
        <span class="axiom-topbar-staff-name"><?= htmlspecialchars(($staff->firstname ?? '') . ' ' . ($staff->lastname ?? '')) ?></span>
        <i class="fa fa-chevron-down" style="font-size:10px;opacity:.6"></i>
      </button>

      <div id="axiom-avatar-menu" class="axiom-avatar-menu" role="menu">
        <div class="axiom-avatar-menu-header">
          <div class="axiom-avatar-menu-name"><?= htmlspecialchars(($staff->firstname ?? '') . ' ' . ($staff->lastname ?? '')) ?></div>
          <div class="axiom-avatar-menu-email"><?= htmlspecialchars($staff->email ?? '') ?></div>
        </div>
        <div class="axiom-avatar-menu-body">
          <label class="axiom-avatar-menu-item" style="cursor:pointer" title="Alterar foto de perfil">
            <i class="fa fa-camera"></i> Alterar foto
            <input type="file" id="axiom-avatar-file-input" accept="image/*" style="display:none">
          </label>
          <a class="axiom-avatar-menu-item" href="<?= admin_url('profile') ?>">
            <i class="fa fa-user"></i> Meu perfil
          </a>
          <a class="axiom-avatar-menu-item" href="<?= admin_url('settings') ?>">
            <i class="fa fa-cog"></i> Configurações
          </a>
          <div class="axiom-avatar-menu-divider"></div>
          <a class="axiom-avatar-menu-item axiom-avatar-menu-logout" href="<?= site_url('authentication/logout') ?>">
            <i class="fa fa-sign-out"></i> Sair
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Expose staff name for greeting JS -->
<span id="axiom-greeting-name" style="display:none"><?= htmlspecialchars($staff->firstname ?? '') ?></span>
