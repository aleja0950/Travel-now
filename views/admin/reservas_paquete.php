<?php $datos = $datos ?? []; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas de paquetes</title>
    <link rel="stylesheet" href="public/css/host.css">
    <link rel="stylesheet" href="public/css/paquetes.css">
</head>
<body>
    <header class="header">
        <img src="public/css/img/icono-removebg-preview.png" class="logo-img" alt="Logo">
        <nav>
            <a href="index.php?controller=login&action=admin">Inicio</a>
            <a href="index.php?controller=paquete&action=admin">Paquetes</a>
            <a href="index.php?controller=paquete&action=reservas_host">Reservas paquetes</a>
            <a href="index.php?controller=reserva&action=host">Reservas habitaciones</a>
            <a href="index.php?controller=login&action=logout">Cerrar</a>
        </nav>
    </header>

    <div class="paquetes-layout">
        <?php if (isset($_GET['msg'])): ?>
            <p class="feedback-success"><?= htmlspecialchars($_GET['msg']) ?></p>
        <?php endif ?>

        <h1>Reservas de paquetes turisticos</h1>

        <?php if (count($datos) === 0): ?>
            <p>No hay reservas de paquetes registradas.</p>
        <?php endif ?>

        <?php foreach ($datos as $item): ?>
            <div class="paquete-card" style="margin-bottom:16px;">
                <div class="paquete-card-body">
                    <h3><?= htmlspecialchars($item['nombre']) ?> — Reserva #<?= (int) $item['Id_reserva_paquete'] ?></h3>
                    <p>Cliente: <?= htmlspecialchars(trim(($item['nombre_usuario'] ?? '') . ' ' . ($item['apellido'] ?? ''))) ?> (<?= htmlspecialchars($item['correo'] ?? '') ?>)</p>
                    <p>Fechas: <?= htmlspecialchars($item['fecha_inicio']) ?> a <?= htmlspecialchars($item['fecha_fin']) ?></p>
                    <p>Total: $<?= number_format((int) $item['monto_total'], 0, ',', '.') ?> (paquete $<?= number_format((int) $item['monto_paquete'], 0, ',', '.') ?> + extras $<?= number_format((int) $item['monto_extras'], 0, ',', '.') ?>)</p>
                    <p>Estado: <strong><?= htmlspecialchars($item['estado_reserva']) ?></strong></p>
                    <p>Metodo pago: <?= htmlspecialchars($item['metodo_pago']) ?></p>

                    <?php if (($item['estado_reserva'] ?? '') === 'pendiente'): ?>
                        <form method="post" action="index.php?controller=paquete&action=aprobar" style="display:inline;">
                            <input type="hidden" name="id_reserva_paquete" value="<?= (int) $item['Id_reserva_paquete'] ?>">
                            <button class="btn-primary" type="submit">Aprobar</button>
                        </form>
                        <form method="post" action="index.php?controller=paquete&action=rechazar" style="display:inline;">
                            <input type="hidden" name="id_reserva_paquete" value="<?= (int) $item['Id_reserva_paquete'] ?>">
                            <button class="btn-secondary" type="submit">Rechazar</button>
                        </form>
                    <?php endif ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</body>
</html>
