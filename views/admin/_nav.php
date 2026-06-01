<?php
$nav_activo = $nav_activo ?? '';
?>
<header class="admin-header admin-header-dashboard">
    <a href="index.php?controller=login&action=admin" class="admin-header-logo">
        <img src="public/css/img/travel_now_no_bg.png" alt="Travel Now" class="admin-logo-img">
        <span class="admin-logo-text">Travel Now</span>
    </a>
    <nav class="admin-nav">
        <a href="index.php?controller=login&action=admin" class="<?= $nav_activo === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="index.php?controller=propiedad&action=index" class="<?= $nav_activo === 'propiedades' ? 'active' : '' ?>">Propiedades</a>
        <a href="index.php?controller=paquete&action=admin" class="<?= $nav_activo === 'paquetes' ? 'active' : '' ?>">Paquetes</a>
        <a href="index.php?controller=reserva&action=host" class="<?= $nav_activo === 'reservas' ? 'active' : '' ?>">Reservas</a>
        <a href="index.php?controller=reserva&action=history" class="<?= $nav_activo === 'historial' ? 'active' : '' ?>">Historial</a>
        <a href="index.php?controller=map&action=index" class="<?= $nav_activo === 'mapa' ? 'active' : '' ?>">Mapa</a>
        <a href="index.php?controller=login&action=logout">Cerrar</a>
        <span class="admin-user-chip"><?= htmlspecialchars($_SESSION['user'] ?? '') ?> · <?= htmlspecialchars($_SESSION['rol'] ?? '') ?></span>
    </nav>
</header>
