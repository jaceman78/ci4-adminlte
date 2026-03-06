<aside class="app-sidebar bg-primary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="https://www.aejoaodebarros.pt/" class="brand-link">
      <img src="<?= base_url('adminlte/img/passaro_vermelho.png') ?>" alt="HardWork550 Logo"
           class="brand-image opacity-75 shadow">
      <span class="brand-text fw-light">HardWork550 JB</span>
    </a>
  </div>

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
        <!--li class="nav-header">MENU</!--li>-->
           <?php $segments = explode('/', trim(uri_string(), '/'));
      $isTickets = ($segments[0] ?? '') === 'tickets';
      $isTicketsNovo = $isTickets && (($segments[1] ?? '') === 'novo' || ($segments[1] ?? '') === '');
?>
<li class="nav-item <?= $isTickets ? 'menu-open' : 'menu-close' ?>">
  <a href="#" class="nav-link <?= $isTickets ? 'active' : '' ?>">
    <i class="nav-icon bi bi-ticket-perforated"></i>
    <p>
      Tickets de Avarias
      <i class="nav-arrow bi bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="<?= base_url('tickets/novo') ?>" class="nav-link <?= $isTicketsNovo ? 'active' : '' ?>">
        <i class="nav-icon bi bi-plus-circle"></i>
        <p>Criar Novo Ticket</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('tickets/meus') ?>" class="nav-link <?= $isTickets && (($segments[1] ?? '') === 'meus') ? 'active' : '' ?>">
        <i class="nav-icon bi bi-list-check"></i>
        <p>Meus Tickets</p>
      </a>
    </li>
    <?php $userLevel = session()->get('LoggedUserData')['level'] ?? 0; ?>
    <?php if ($userLevel >= 5): ?>
    <li class="nav-item">
      <a href="<?= base_url('tickets/tratamento') ?>" class="nav-link <?= $isTickets && (($segments[1] ?? '') === 'tratamento') ? 'active' : '' ?>">
        <i class="nav-icon bi bi-tools"></i>
        <p>Tratamento de Tickets</p>
      </a>
    </li>
    <?php endif; ?>
    <?php if ($userLevel >= 8): ?>
    <li class="nav-item">
      <a href="<?= base_url('tickets/todos') ?>" class="nav-link <?= $isTickets && (($segments[1] ?? '') === 'todos') ? 'active' : '' ?>">
        <i class="nav-icon bi bi-clipboard-data"></i>
        <p>Todos os Tickets</p>
      </a>
    </li>
    <?php endif; ?>
  </ul>
</li>

<?php 
// Verificar se está em alguma página de Permutas
$isPermutas = ($segments[0] ?? '') === 'permutas';
?>
<li class="nav-item <?= $isPermutas ? 'menu-open' : '' ?>">
  <a href="#" class="nav-link <?= $isPermutas ? 'active' : '' ?>">
    <i class="nav-icon bi bi-calendar-week"></i>
    <p>
      Horário & Permutas
      <i class="nav-arrow bi bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <?php $userLevel = session()->get('LoggedUserData')['level'] ?? 0; ?>
    <?php if ($userLevel != 0): ?>
    <li class="nav-item">
      <a href="<?= base_url('permutas') ?>" class="nav-link <?= $isPermutas && (($segments[1] ?? '') === '' || !isset($segments[1])) ? 'active' : '' ?>">
        <i class="nav-icon bi bi-eye"></i>
        <p>Meu Horário</p>
      </a>
    </li>
    <?php endif; ?>
    <?php if ($userLevel != 0): ?>
    <li class="nav-item">
      <a href="<?= base_url('permutas/minhas') ?>" class="nav-link <?= $isPermutas && (($segments[1] ?? '') === 'minhas') ? 'active' : '' ?>">
        <i class="nav-icon bi bi-list-check"></i>
        <p>As Minhas Permutas</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('permutas/creditos') ?>" class="nav-link <?= $isPermutas && (($segments[1] ?? '') === 'creditos') ? 'active' : '' ?>">
        <i class="nav-icon bi bi-clock-history"></i>
        <p>Créditos de Aulas</p>
      </a>
    </li>
    <?php endif; ?>
    <?php if ($userLevel >= 6): ?>
    <li class="nav-item">
      <a href="<?= base_url('permutas/lista') ?>" class="nav-link <?= $isPermutas && (($segments[1] ?? '') === 'lista') ? 'active' : '' ?>">
        <i class="nav-icon bi bi-table"></i>
        <p>Lista de Permutas</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('permutas/aprovar') ?>" class="nav-link <?= $isPermutas && (($segments[1] ?? '') === 'aprovar') ? 'active' : '' ?>">
        <i class="nav-icon bi bi-check-circle"></i>
        <p>Aprovar Permutas</p>
      </a>
    </li>
    <?php endif; ?>
    <?php if ($userLevel == 0 || $userLevel >= 8): ?>
    <li class="nav-item">
      <a href="<?= base_url('permutas/aprovadas') ?>" class="nav-link <?= $isPermutas && (($segments[1] ?? '') === 'aprovadas') ? 'active' : '' ?>">
        <i class="nav-icon bi bi-check2-square"></i>
        <p>Permutas Aprovadas</p>
      </a>
    </li>
    <?php endif; ?>
  </ul>
</li>

<?php 
// Verificar se está em alguma página de Kit Digital Admin
$isKitDigital = ($segments[0] ?? '') === 'kit-digital-admin';
$isAvariasKit = ($segments[0] ?? '') === 'avarias-kit-admin';
$isReparacoesExternas = ($segments[0] ?? '') === 'reparacoes-externas';
$isInutilizadosKitdigital = ($segments[0] ?? '') === 'inutilizados-kitdigital';
?>
<?php if ($userLevel >= 5): ?>
<li class="nav-item <?= ($isKitDigital || $isAvariasKit || $isReparacoesExternas || $isInutilizadosKitdigital) ? 'menu-open' : '' ?>">
  <a href="#" class="nav-link <?= ($isKitDigital || $isAvariasKit || $isReparacoesExternas || $isInutilizadosKitdigital) ? 'active' : '' ?>">
    <i class="nav-icon bi bi-laptop"></i>
    <p>
      Kit Digital
      <i class="nav-arrow bi bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="<?= base_url('kit-digital-admin') ?>" class="nav-link <?= $isKitDigital && (($segments[1] ?? '') === '' || !isset($segments[1])) ? 'active' : '' ?>">
        <i class="nav-icon bi bi-list-ul"></i>
        <p>Listagem de Pedidos</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('avarias-kit-admin') ?>" class="nav-link <?= $isAvariasKit ? 'active' : '' ?>">
        <i class="nav-icon bi bi-exclamation-triangle"></i>
        <p>Avarias Reportadas</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('kit-digital-admin/estatisticas') ?>" class="nav-link <?= $isKitDigital && (($segments[1] ?? '') === 'estatisticas') ? 'active' : '' ?>">
        <i class="nav-icon bi bi-bar-chart"></i>
        <p>Estatísticas</p>
      </a>
    </li>
    <?php if ($userLevel >= 7): ?>
    <li class="nav-item">
      <a href="<?= base_url('reparacoes-externas') ?>" class="nav-link <?= $isReparacoesExternas ? 'active' : '' ?>">
        <i class="nav-icon bi bi-tools"></i>
        <p>Reparações Externas</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('inutilizados-kitdigital') ?>" class="nav-link <?= $isInutilizadosKitdigital ? 'active' : '' ?>">
        <i class="nav-icon bi bi-pc-display-horizontal"></i>
        <p>Equipamentos Inutilizados</p>
      </a>
    </li>
    <?php endif; ?>
  </ul>
</li>
<?php endif; ?>

<?php 
// Verificar se está em alguma página de Sec. Exames
$isExames = ($segments[0] ?? '') === 'exames';
$isSessoesExame = ($segments[0] ?? '') === 'sessoes-exame';
$isConvocatorias = ($segments[0] ?? '') === 'convocatorias';
$isSecExamesActive = $isExames || $isSessoesExame || $isConvocatorias;
?>
<?php if ($userLevel == 4 || $userLevel == 8 || $userLevel == 9): ?>
<li class="nav-item <?= $isSecExamesActive ? 'menu-open' : '' ?>">
  <a href="#" class="nav-link <?= $isSecExamesActive ? 'active' : '' ?>">
    <i class="nav-icon bi bi-clipboard2-check"></i>
    <p>
      Sec. Exames
      <i class="nav-arrow bi bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="<?= base_url('sessoes-exame/calendario') ?>" class="nav-link <?= $isSessoesExame && (($segments[1] ?? '') === 'calendario') ? 'active' : '' ?>">
        <i class="nav-icon bi bi-calendar3"></i>
        <p>Calendário de Exames</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('convocatorias') ?>" class="nav-link <?= $isConvocatorias && ($segments[1] ?? '') !== 'marcar-presencas' && ($segments[1] ?? '') !== 'estatisticas' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-person-check"></i>
        <p>Permutas Vigilâncias</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('convocatorias/estatisticas') ?>" class="nav-link <?= ($segments[0] ?? '') === 'convocatorias' && ($segments[1] ?? '') === 'estatisticas' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-graph-up"></i>
        <p>Estatísticas</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('convocatorias/marcar-presencas') ?>" class="nav-link <?= ($segments[0] ?? '') === 'convocatorias' && ($segments[1] ?? '') === 'marcar-presencas' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-clipboard-check"></i>
        <p>Marcar Presenças</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('sessoes-exame') ?>" class="nav-link <?= $isSessoesExame && (($segments[1] ?? '') === '' || !isset($segments[1])) ? 'active' : '' ?>">
        <i class="nav-icon bi bi-calendar-event"></i>
        <p>Sessões de Exame</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('exames') ?>" class="nav-link <?= $isExames ? 'active' : '' ?>">
        <i class="nav-icon bi bi-file-earmark-text"></i>
        <p>Exames/Provas</p>
      </a>
    </li>
  </ul>
</li>
<?php endif; ?>

<?php 
// Verificar se está em alguma página de Gestão Letiva
$gestaoLetivaPages = ['turmas', 'disciplinas', 'horarios', 'blocos', 'tipologias', 'anos-letivos'];
$isGestaoLetivaActive = in_array($segments[0] ?? '', $gestaoLetivaPages);
$userLevel = session()->get('LoggedUserData')['level'] ?? 0;
?>

<?php if ($userLevel >= 6): ?>
<li class="nav-item <?= $isGestaoLetivaActive ? 'menu-open' : '' ?>">
  <a href="#" class="nav-link <?= $isGestaoLetivaActive ? 'active' : '' ?>">
    <i class="nav-icon bi bi-calendar-check"></i>
    <p>
      Gestão Letiva
      <i class="nav-arrow bi bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="<?= base_url('turmas') ?>" class="nav-link <?= ($segments[0] ?? '') == 'turmas' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-people-fill"></i>
        <p>Gestão de Turmas</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('disciplinas') ?>" class="nav-link <?= ($segments[0] ?? '') == 'disciplinas' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-book"></i>
        <p>Gestão de Disciplinas</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('horarios') ?>" class="nav-link <?= ($segments[0] ?? '') == 'horarios' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-calendar3"></i>
        <p>Gestão de Horários</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('blocos') ?>" class="nav-link <?= ($segments[0] ?? '') == 'blocos' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-clock"></i>
        <p>Gestão de Blocos</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('tipologias') ?>" class="nav-link <?= ($segments[0] ?? '') == 'tipologias' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-tags"></i>
        <p>Gestão de Tipologias</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('anos-letivos') ?>" class="nav-link <?= ($segments[0] ?? '') == 'anos-letivos' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-calendar-range"></i>
        <p>Gestão de Anos Letivos</p>
      </a>
    </li>
  </ul>
</li>
<?php endif; ?>

<?php 
// Verificar se está em alguma página do Dashboard
$dashboardPages = ['users', 'escolas', 'salas', 'equipamentos', 'tipos_equipamentos', 'tipos_avaria', 'materiais', 'logs', 'sugestoes', 'empresas-chaves'];
$isDashboardActive = in_array($segments[0] ?? '', $dashboardPages);
?>

<?php if ($userLevel >= 5): ?>
<li class="nav-item <?= $isDashboardActive ? 'menu-open' : '' ?>">
  <a href="#" class="nav-link <?= $isDashboardActive ? 'active' : '' ?>">
    <i class="nav-icon bi bi-speedometer"></i>
    <p>
      Dashboard
      <i class="nav-arrow bi bi-chevron-right"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <?php if ($userLevel >= 6): ?>
    <li class="nav-item">
      <a href="<?= base_url('sugestoes') ?>" class="nav-link <?= ($segments[0] ?? '') == 'sugestoes' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-lightbulb"></i>
        <p>Caixa de Sugestões</p>
      </a>
    </li>
    <?php endif; ?>
    <?php if ($userLevel >= 6): ?>
    <li class="nav-item">
      <a href="<?= base_url('users') ?>" class="nav-link <?= ($segments[0] ?? '') == 'users' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-circle"></i>
        <p>Utilizadores</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('escolas') ?>" class="nav-link <?= ($segments[0] ?? '') == 'escolas' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-circle"></i>
        <p>Escolas</p>
      </a>
    </li>
    <?php endif; ?>
    <li class="nav-item">
      <a href="<?= base_url('salas') ?>" class="nav-link <?= ($segments[0] ?? '') == 'salas' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-circle"></i>
        <p>Salas</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('equipamentos') ?>" class="nav-link <?= ($segments[0] ?? '') == 'equipamentos' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-circle"></i>
        <p>Equipamentos</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('tipos_equipamentos') ?>" class="nav-link <?= ($segments[0] ?? '') == 'tipos_equipamentos' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-circle"></i>
        <p>Tipos de Equipamentos</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('tipos_avaria') ?>" class="nav-link <?= ($segments[0] ?? '') == 'tipos_avaria' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-circle"></i>
        <p>Tipos de Avaria</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('materiais') ?>" class="nav-link <?= ($segments[0] ?? '') == 'materiais' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-circle"></i>
        <p>Materiais</p>
      </a>
    </li>
    <?php if ((session()->get('LoggedUserData')['level'] ?? 0) == 9): ?>
    <li class="nav-item">
      <a href="<?= base_url('logs') ?>" class="nav-link <?= ($segments[0] ?? '') == 'logs' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-circle"></i>
        <p>Logs</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('empresas-chaves') ?>" class="nav-link <?= ($segments[0] ?? '') == 'empresas-chaves' ? 'active' : '' ?>">
        <i class="nav-icon bi bi-key"></i>
        <p>Chaves de Acesso</p>
      </a>
    </li>
    <?php endif; ?>
                </ul>
              </li>
<?php endif; ?>
      </ul>
    </nav>
  </div>
</aside>
<!--end::Sidebar-->