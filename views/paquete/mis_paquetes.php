<?php $datos = $datos ?? []; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css/style.css">
    <link rel="stylesheet" href="public/css/paquetes.css">
    <title>Mis paquetes</title>
</head>
<body>
    <header class="header">
        <img src="public/css/img/travel_now_no_bg.png" class="logo-img" alt="Logo">
        <nav>
            <a href="index.php?controller=login&action=user">Inicio</a>
            <a href="index.php?controller=paquete&action=catalogo">Paquetes</a>
            <a href="index.php?controller=paquete&action=mis_paquetes">Mis paquetes</a>
            <a href="index.php?controller=login&action=logout">Cerrar</a>
        </nav>
    </header>

    <div class="paquetes-layout">
        <?php if (isset($_GET['msg'])): ?>
            <p class="feedback-success"><?= htmlspecialchars($_GET['msg']) ?></p>
        <?php endif ?>

        <h2>Mis reservas de paquetes</h2>

        <?php if (count($datos) === 0): ?>
            <p>Aun no tienes reservas de paquetes.</p>
            <a href="index.php?controller=paquete&action=catalogo">Ver catalogo</a>
        <?php endif ?>

        <?php foreach ($datos as $item): ?>
            <?php $extras = $item['extras_detalle'] ?? []; ?>
            <article class="paquete-card" style="margin-bottom:16px;">
                <div class="paquete-card-body">
                    <h3><?= htmlspecialchars($item['nombre']) ?></h3>
                    <p>Inicio: <?= htmlspecialchars($item['fecha_inicio']) ?> · Fin: <?= htmlspecialchars($item['fecha_fin']) ?></p>
                    <p>Estado: <strong><?= htmlspecialchars($item['estado_reserva']) ?></strong></p>
                    <p>Paquete: $<?= number_format((int) $item['monto_paquete'], 0, ',', '.') ?></p>
                    <p>Extras: $<?= number_format((int) $item['monto_extras'], 0, ',', '.') ?></p>
                    <p class="paquete-price">Total: $<?= number_format((int) $item['monto_total'], 0, ',', '.') ?></p>
                    <p>Metodo: <?= htmlspecialchars($item['metodo_pago']) ?></p>
                    <?php if (count($extras) > 0): ?>
                        <p>Servicios extra:
                            <?php foreach ($extras as $extra): ?>
                                <?= htmlspecialchars($extra['nombre']) ?>,
                            <?php endforeach ?>
                        </p>
                    <?php endif ?>
                </div>
            </article>
        <?php endforeach ?>
    </div>
</body>
</html>
