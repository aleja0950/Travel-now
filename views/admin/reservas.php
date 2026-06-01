<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservaciones</title>
    <link rel="stylesheet" href="public/css/reserve.css">
    <link rel="stylesheet" href="public/css/dashboard.css">
</head>
<body class="dashboard-body">
    <?php $nav_activo = 'reservas'; require_once __DIR__ . '/_nav.php'; ?>

    <h1>Reservaciones</h1>
    <p>Incluye reservas de habitaciones y de paquetes turisticos.</p>

    <?php if (isset($_GET['msg'])): ?>
        <p class="feedback-success"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif ?>
    
<?php
    $total_proximas = count($totales['upcoming'] ?? []);
    $total_pasadas = count($totales['past'] ?? []);
    $total_rechazadas = 0;
    $total_canceladas = 0;

    foreach (($totales['rejected'] ?? []) as $itemResumen) {
        if (($itemResumen['estado_reserva'] ?? '') === 'rechazada') {
            $total_rechazadas++;
        }

        if (($itemResumen['estado_reserva'] ?? '') === 'cancelada') {
            $total_canceladas++;
        }
    }
?>

    <div class="stats-row">
        <div class="stat-pill">Proximas: <?= $total_proximas ?></div>
        <div class="stat-pill">Pasadas: <?= $total_pasadas ?></div>
        <div class="stat-pill">Rechazadas: <?= $total_rechazadas ?></div>
        <div class="stat-pill">Canceladas: <?= $total_canceladas ?></div>
    </div>

    <!-- Filtros del host para revisar reservas por estado y fechas. -->
    <form method="get" action="index.php" class="filter-form">
        <input type="hidden" name="controller" value="reserva">
        <input type="hidden" name="action" value="host">
        <select name="estado" class="filter-control">
            <option value="">todos los estados</option>
            <option value="pendiente" <?= (($_GET['estado'] ?? '') === 'pendiente') ? 'selected' : '' ?>>pendiente</option>
            <option value="aprobada" <?= (($_GET['estado'] ?? '') === 'aprobada') ? 'selected' : '' ?>>aprobada</option>
            <option value="rechazadas" <?= (($_GET['estado'] ?? '') === 'rechazada') ? 'selected' : '' ?>>rechazadas</option>
            <option value="cancelada" <?= (($_GET['estado'] ?? '') === 'cancelada') ? 'selected' : '' ?>>cancelada</option>
        </select>
        <input type="date" name="desde" value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>" class="filter-control">
        <input type="date" name="hasta" value="<?= htmlspecialchars($_GET['hasta'] ?? '') ?>" class="filter-control">
        <button type="submit" class="filter-button">Filtrar</button>
        <a href="index.php?controller=reserva&action=host" class="filter-link">Limpiar</a>
    </form>

    <!-- Separa la gestion en proximas, pasadas y rechazadas/canceladas. -->
    <div class="tabs" id="reservas-tabs">
        <span class="active" data-tab="upcoming">Próximas</span>
        <span data-tab="past">Pasadas</span>
        <span data-tab="rejected">Rechazadas</span>
    </div>

    <div class="line"></div>

    <!-- Reservas activas sobre las que el host todavia puede decidir. -->
    <div id="upcomingTab" class="tab-panel">
        <?php if (count($datos['upcoming']) > 0): ?>
            <?php foreach ($datos['upcoming'] as $item): ?>
                <?php $es_paquete = (($item['tipo_reserva'] ?? 'habitacion') === 'paquete'); ?>
                <div class="reservation">
                    <img class="img-box img-box-tag" src="public/css/img/<?= $es_paquete ? 'paquete.jpg' : 'habitacion1.jpg' ?>" alt="Reserva">

                    <div class="info">
                        <div class="title">
                            <?= htmlspecialchars($item['nombre']) ?>
                            <?php if ($es_paquete): ?>
                                <span class="stat-pill">Paquete turistico</span>
                            <?php endif ?>
                        </div>
                        <div>
                            <?= $es_paquete ? 'Inicio' : 'Entrada' ?>: <?= htmlspecialchars($item['fecha_ingreso']) ?>
                            &nbsp;&nbsp; <?= $es_paquete ? 'Fin' : 'Salida' ?>: <?= htmlspecialchars($item['fecha_salida']) ?>
                        </div>
                        <?php if ($es_paquete): ?>
                            <div>Cliente: <?= htmlspecialchars(trim(($item['nombre_usuario'] ?? '') . ' ' . ($item['apellido'] ?? ''))) ?></div>
                            <div>Total paquete: $<?= number_format((int) ($item['monto_total'] ?? 0), 0, ',', '.') ?></div>
                        <?php else: ?>
                            <div>
                                Habitacion: <?= htmlspecialchars($item['id_habitacion']) ?>
                                &nbsp;&nbsp; Tipo: <?= htmlspecialchars($item['tipo_habitacion']) ?>
                            </div>
                            <div>
                                Servicio: <?= htmlspecialchars($item['servicio_especial']) ?>
                                &nbsp;&nbsp; Precio: $<?= number_format((int) ($item['precio'] ?? 0), 0, ',', '.') ?>
                            </div>
                        <?php endif ?>
                        <div>Estado: <?= htmlspecialchars($item['estado_reserva']) ?></div>
                        <?php if (!$es_paquete): ?>
                            <div>Ubicacion: <?= htmlspecialchars($item['ubicacion']) ?></div>
                        <?php endif ?>
                    </div>

                    <div class="buttons">
                        <?php if ($item['estado_reserva'] === 'pendiente'): ?>
                            <?php if ($es_paquete): ?>
                                <form action="index.php?controller=paquete&action=aprobar" method="post" class="inline-form">
                                    <input type="hidden" name="id_reserva_paquete" value="<?= (int) $item['Id_reserva_paquete'] ?>">
                                    <button class="btn approve" type="submit">Aprobar</button>
                                </form>
                                <form action="index.php?controller=paquete&action=rechazar" method="post" class="inline-form">
                                    <input type="hidden" name="id_reserva_paquete" value="<?= (int) $item['Id_reserva_paquete'] ?>">
                                    <button class="btn reject" type="submit">Rechazar</button>
                                </form>
                            <?php else: ?>
                                <form action="index.php?controller=reserva&action=aprobar" method="post" class="inline-form">
                                    <input type="hidden" name="id_reserva" value="<?= htmlspecialchars($item['Id_reserva']) ?>">
                                    <button class="btn approve" type="submit">Aprobar</button>
                                </form>
                                <form action="index.php?controller=reserva&action=rechazar" method="post" class="inline-form">
                                    <input type="hidden" name="id_reserva" value="<?= htmlspecialchars($item['Id_reserva']) ?>">
                                    <button class="btn reject" type="submit">Rechazar</button>
                                </form>
                            <?php endif ?>
                        <?php else: ?>
                            <button class="btn approve" type="button"><?= htmlspecialchars($item['estado_reserva']) ?></button>
                        <?php endif ?>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="reservation">
                <div class="info">
                    <div class="title">No hay reservas próximas</div>
                    <div>Las nuevas reservas aparecerán aquí.</div>
                </div>
            </div>
        <?php endif ?>
    </div>

    <!-- Reservas que ya pasaron por fecha o ya fueron completadas. -->
    <div id="pastTab" class="tab-panel tab-panel-hidden">
        <?php if (count($datos['past']) > 0): ?>
            <?php foreach ($datos['past'] as $item): ?>
                <?php $es_paquete = (($item['tipo_reserva'] ?? 'habitacion') === 'paquete'); ?>
                <div class="reservation">
                    <img class="img-box img-box-tag" src="public/css/img/<?= $es_paquete ? 'paquete.jpg' : 'habitacion2.png' ?>" alt="Reserva">

                    <div class="info">
                        <div class="title">
                            <?= htmlspecialchars($item['nombre']) ?>
                            <?php if ($es_paquete): ?><span class="stat-pill">Paquete</span><?php endif ?>
                        </div>
                        <div>
                            Entrada: <?= htmlspecialchars($item['fecha_ingreso']) ?>
                            &nbsp;&nbsp; Salida: <?= htmlspecialchars($item['fecha_salida']) ?>
                        </div>
                        <div>
                            Habitacion: <?= htmlspecialchars($item['id_habitacion']) ?>
                            &nbsp;&nbsp; Tipo: <?= htmlspecialchars($item['tipo_habitacion']) ?>
                        </div>
                        <div>
                            Servicio: <?= htmlspecialchars($item['servicio_especial']) ?>
                            &nbsp;&nbsp; Precio: $<?= number_format((int) ($item['precio'] ?? 0), 0, ',', '.') ?>
                        </div>
                        <div>Estado: <?= htmlspecialchars($item['estado_reserva']) ?></div>
                        <div>Ubicacion: <?= htmlspecialchars($item['ubicacion']) ?></div>
                    </div>

                    <div class="buttons">
                        <button class="btn reject" type="button">Finalizada</button>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="reservation">
                <div class="info">
                    <div class="title">No hay reservas pasadas</div>
                    <div>Las reservas finalizadas aparecerán aquí.</div>
                </div>
            </div>
        <?php endif ?>
    </div>

    <!-- Agrupa las rechazadas por el host y las canceladas por el usuario. -->
    <div id="rejectedTab" class="tab-panel tab-panel-hidden">
        <?php if (count($datos['rejected']) > 0): ?>
            <?php foreach ($datos['rejected'] as $item): ?>
                <?php $es_paquete = (($item['tipo_reserva'] ?? 'habitacion') === 'paquete'); ?>
                <div class="reservation">
                    <img class="img-box img-box-tag" src="public/css/img/imagen3.jpg" alt="Reserva">

                    <div class="info">
                        <div class="title">
                            <?= htmlspecialchars($item['nombre']) ?>
                            <?php if ($es_paquete): ?><span class="stat-pill">Paquete</span><?php endif ?>
                        </div>
                        <?php if (!$es_paquete): ?>
                            <div>Habitacion: <?= htmlspecialchars($item['id_habitacion']) ?></div>
                        <?php endif ?>
                        <div>Servicio: <?= htmlspecialchars($item['servicio_especial']) ?></div>
                        <div>Estado: <?= htmlspecialchars($item['estado_reserva']) ?></div>
                        <?php if (($item['estado_reserva'] ?? '') === 'cancelada'): ?>
                            <div>Motivo: cancelada por el usuario</div>
                        <?php endif ?>
                        <?php if (($item['estado_reserva'] ?? '') === 'rechazada'): ?>
                            <div>Motivo: rechazada por el host</div>
                        <?php endif ?>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="reservation">
                <div class="info">
                    <div class="title">No hay reservas canceladas o rechazadas</div>
                    <div>Aqui se mostraran las rechazadas por el host y las canceladas por el usuario.</div>
                </div>
            </div>
        <?php endif ?>
    </div>

    <script src="public/js/reservas-tabs.js"></script>
</body>
</html>
