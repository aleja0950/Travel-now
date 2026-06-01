<?php $datos = $datos ?? []; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css/style.css">
    <link rel="stylesheet" href="public/css/paquetes.css">
    <title>Paquetes turisticos</title>
</head>
<body>
    <header class="header">
        <img src="public/css/img/travel_now_no_bg.png" class="logo-img" alt="Logo">
        <nav>
            <a href="index.php?controller=login&action=user">Inicio</a>
            <a href="index.php?controller=paquete&action=catalogo">Paquetes</a>
            <a href="index.php?controller=reserva&action=index">Reserva</a>
            <a href="index.php?controller=search&action=index">Buscar</a>
            <a href="index.php?controller=reserva&action=misreservas">Mis reservas</a>
            <a href="index.php?controller=paquete&action=mis_paquetes">Mis paquetes</a>
            <a href="index.php?controller=login&action=logout">Cerrar</a>
        </nav>
    </header>

    <div class="paquetes-layout">
        <h2>Paquetes turisticos</h2>
        <p>Combina habitaciones, tours y servicios en un solo plan con precio calculado.</p>

        <?php if (count($datos) === 0): ?>
            <p>No hay paquetes activos por ahora.</p>
        <?php endif ?>

        <div class="paquetes-grid">
            <?php foreach ($datos as $item): ?>
                <article class="paquete-card">
                    <img src="public/css/img/<?= htmlspecialchars($item['imagen'] ?? 'paquete.jpg') ?>" alt="<?= htmlspecialchars($item['nombre']) ?>">
                    <div class="paquete-card-body">
                        <h3><?= htmlspecialchars($item['nombre']) ?></h3>
                        <p><?= htmlspecialchars($item['descripcion'] ?? '') ?></p>
                        <p class="paquete-price">Desde $<?= number_format((int) ($item['precio_base'] ?? 0), 0, ',', '.') ?></p>
                        <p><?= (int) ($item['dias'] ?? 0) ?> dias</p>
                        <a href="index.php?controller=paquete&action=detalle&id=<?= (int) $item['Id_paquete'] ?>">
                            <button class="btn-primary" type="button">Ver detalle y reservar</button>
                        </a>
                    </div>
                </article>
            <?php endforeach ?>
        </div>
    </div>
</body>
</html>
