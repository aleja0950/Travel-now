<?php
$resumen = $resumen ?? [];
$movimientos = $resumen['movimientos'] ?? [];
$nav_activo = 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin · Travel Now</title>
    <link rel="stylesheet" href="public/css/editroom.css">
    <link rel="stylesheet" href="public/css/dashboard.css">
    <link rel="icon" type="image/png" href="public/css/img/icono-removebg-preview.png">
</head>
<body class="dashboard-body">
    <?php require_once __DIR__ . '/_nav.php'; ?>

    <?php if (isset($_GET['msg'])): ?>
        <p class="feedback-success"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif ?>

    <section class="dashboard-hero">
        <div class="dashboard-welcome">
            <h1>Hola, <?= htmlspecialchars($_SESSION['user'] ?? 'Administrador') ?></h1>
            <p>Panel de control de Travel Now. Revisa el resumen de reservas, pagos y paquetes turisticos desde un solo lugar.</p>
            <div class="dashboard-welcome-actions">
                <a href="index.php?controller=reserva&action=host" class="dashboard-btn dashboard-btn-primary">Ver reservas pendientes</a>
                <a href="index.php?controller=propiedad&action=index" class="dashboard-btn dashboard-btn-secondary">Gestionar propiedades</a>
            </div>
        </div>
        <div class="dashboard-brand-card">
            <img src="public/css/img/travel_now_no_bg.png" alt="Travel Now">
            <span>Sistema de alojamiento y paquetes turisticos</span>
        </div>
    </section>

    <section class="dashboard-stats">
        <article class="stat-card highlight">
            <strong>$<?= number_format((int) ($resumen['ingresos_pagados'] ?? 0), 0, ',', '.') ?></strong>
            <span>Ingresos confirmados (pagados)</span>
        </article>
        <article class="stat-card">
            <strong><?= (int) ($resumen['reservas_pendientes'] ?? 0) ?></strong>
            <span>Reservas pendientes de aprobar</span>
        </article>
        <article class="stat-card">
            <strong><?= (int) ($resumen['reservas_proximas'] ?? 0) ?></strong>
            <span>Reservas proximas activas</span>
        </article>
        <article class="stat-card">
            <strong><?= (int) ($resumen['propiedades'] ?? 0) ?></strong>
            <span>Propiedades publicadas</span>
        </article>
        <article class="stat-card">
            <strong><?= (int) ($resumen['paquetes_activos'] ?? 0) ?></strong>
            <span>Paquetes turisticos activos</span>
        </article>
        <article class="stat-card">
            <strong><?= (int) ($resumen['pagos_en_revision'] ?? 0) ?></strong>
            <span>Pagos pendientes o en revision</span>
        </article>
        <article class="stat-card">
            <strong><?= (int) ($resumen['total_reservas'] ?? 0) ?></strong>
            <span>Total reservas registradas</span>
        </article>
    </section>

    <section class="dashboard-grid">
        <div class="dashboard-panel">
            <h2>Ultimos movimientos</h2>
            <?php if (count($movimientos) > 0): ?>
                <ul class="movement-list">
                    <?php foreach ($movimientos as $mov): ?>
                        <?php
                        $tipo = $mov['tipo'] ?? 'habitacion';
                        $etiqueta_tipo = $tipo === 'paquete' ? 'Paquete' : ($tipo === 'pago' ? 'Pago' : 'Habitacion');
                        $fecha = $mov['fecha_evento'] ?? '';
                        $fecha_fmt = $fecha !== '' ? date('d/m/Y H:i', strtotime($fecha)) : 'sin fecha';
                        ?>
                        <li class="movement-item">
                            <span class="movement-badge <?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($etiqueta_tipo) ?></span>
                            <div class="movement-info">
                                <strong><?= htmlspecialchars($mov['titulo'] ?? 'Sin titulo') ?></strong>
                                <small><?= htmlspecialchars($mov['detalle'] ?? '') ?> · Estado: <?= htmlspecialchars($mov['estado'] ?? '') ?></small>
                            </div>
                            <div class="movement-meta">
                                <span class="monto">$<?= number_format((int) ($mov['monto'] ?? 0), 0, ',', '.') ?></span>
                                <span><?= htmlspecialchars($fecha_fmt) ?></span>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php else: ?>
                <p class="dashboard-empty">Aun no hay movimientos recientes para mostrar.</p>
            <?php endif ?>
        </div>

        <div class="dashboard-panel">
            <h2>Accesos rapidos</h2>
            <div class="quick-links">
                <a href="index.php?controller=reserva&action=host" class="quick-link">
                    <span>Reservas de habitaciones y paquetes</span>
                    <span>→</span>
                </a>
                <a href="index.php?controller=reserva&action=history" class="quick-link">
                    <span>Historial financiero</span>
                    <span>→</span>
                </a>
                <a href="index.php?controller=propiedad&action=index" class="quick-link">
                    <span>Propiedades y habitaciones</span>
                    <span>→</span>
                </a>
                <a href="index.php?controller=paquete&action=admin" class="quick-link">
                    <span>Paquetes turisticos</span>
                    <span>→</span>
                </a>
                <a href="index.php?controller=map&action=index" class="quick-link">
                    <span>Mapa de ubicaciones</span>
                    <span>→</span>
                </a>
                <a href="Admin/perfilhost.php" class="quick-link">
                    <span>Perfil del administrador</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </section>
</body>
</html>
