<?php
$datos = $datos ?? null;
$componentes = $componentes ?? [];
$servicios = $servicios ?? [];
$precio_paquete = (int) ($precio_paquete ?? 0);
$errores = $errores ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css/style.css">
    <link rel="stylesheet" href="public/css/paquetes.css">
    <title>Detalle paquete</title>
</head>
<body>
    <header class="header">
        <img src="public/css/img/travel_now_no_bg.png" class="logo-img" alt="Logo">
        <nav>
            <a href="index.php?controller=login&action=user">Inicio</a>
            <a href="index.php?controller=paquete&action=catalogo">Paquetes</a>
            <a href="index.php?controller=reserva&action=index">Reserva</a>
            <a href="index.php?controller=reserva&action=misreservas">Mis reservas</a>
            <a href="index.php?controller=login&action=logout">Cerrar</a>
        </nav>
    </header>

    <div class="paquetes-layout">
        <?php if (!$datos): ?>
            <p>Paquete no encontrado.</p>
            <a href="index.php?controller=paquete&action=catalogo">Volver al catalogo</a>
        <?php else: ?>
            <h2><?= htmlspecialchars($datos['nombre']) ?></h2>
            <p><?= htmlspecialchars($datos['descripcion'] ?? '') ?></p>
            <p class="paquete-price">Precio paquete: $<?= number_format($precio_paquete, 0, ',', '.') ?></p>
            <p><?= (int) ($datos['dias'] ?? 0) ?> dias</p>

            <p class="location">Si el paquete incluye habitaciones, no podras reservarlo si esas fechas ya estan ocupadas por otro cliente o por otro paquete.</p>

            <h3>Incluye</h3>
            <ul>
                <?php foreach ($componentes as $componente): ?>
                    <li>
                        <strong><?= htmlspecialchars($componente['tipo']) ?>:</strong>
                        <?= htmlspecialchars($componente['titulo']) ?>
                        <?php if (!empty($componente['nombre_hotel'])): ?>
                            (<?= htmlspecialchars($componente['nombre_hotel']) ?>)
                        <?php endif ?>
                        — $<?= number_format((int) ($componente['precio_componente'] ?? 0), 0, ',', '.') ?>
                    </li>
                <?php endforeach ?>
            </ul>

            <?php foreach ($errores as $error): ?>
                <p class="feedback-danger"><?= htmlspecialchars($error) ?></p>
            <?php endforeach ?>

            <form
                class="paquete-form"
                method="post"
                id="form-reserva-paquete"
                data-precio-paquete="<?= $precio_paquete ?>"
            >
                <label>Fecha inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" required>

                <label>Fecha fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" required>

                <h3>Servicios adicionales</h3>
                <?php foreach ($servicios as $servicio): ?>
                    <label class="extra-item">
                        <input
                            type="checkbox"
                            name="servicios_extra[]"
                            value="<?= (int) $servicio['Id_servicio'] ?>"
                            data-precio="<?= (int) $servicio['precio'] ?>"
                            class="extra-checkbox"
                        >
                        <?= htmlspecialchars($servicio['nombre']) ?>
                        (+ $<?= number_format((int) $servicio['precio'], 0, ',', '.') ?>)
                    </label>
                <?php endforeach ?>

                <label>Metodo de pago</label>
                <select name="metodo_pago">
                    <option value="transferencia">Transferencia</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                </select>

                <div class="total-box">
                    <strong>Total estimado del paquete</strong>
                    <p id="total-paquete">$<?= number_format($precio_paquete, 0, ',', '.') ?></p>
                    <p id="total-extras">Extras: $0</p>
                    <p id="total-general">Total: $<?= number_format($precio_paquete, 0, ',', '.') ?></p>
                </div>

                <button class="btn-primary" type="submit">Reservar paquete</button>
            </form>
        <?php endif ?>
    </div>

    <script src="public/js/paquete-reserva.js"></script>
</body>
</html>
