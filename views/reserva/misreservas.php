<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css/style.css">
    <title>Mis reservas</title>
</head>
<body>
    <header class="header">
        <img src="public/css/img/travel_now_no_bg.png" class="logo-img" alt="Logo">

        <nav>
            <a href="index.php?controller=login&action=user">Inicio</a>
            <a href="index.php?controller=reserva&action=index">Reserva</a>
            <a href="index.php?controller=search&action=index">Buscar</a>
            <a href="index.php?controller=reserva&action=misreservas">Mis reservas</a>
            <a href="index.php?controller=login&action=logout">Cerrar</a>
            <?= $_SESSION['user']; ?>
            <?= $_SESSION['rol']; ?>
        </nav>
    </header>

    <div class="container">
        <?php if (isset($_GET['msg'])): ?>
            <p class="feedback-ok"><?= htmlspecialchars($_GET['msg']) ?></p>
        <?php endif ?>

        <section class="title-section">
            <h2>Mis reservas</h2>
            <p class="location">Solo ves las reservas asociadas a tu usuario autenticado.</p>
            <p class="location">Los estados de reserva, pagos y acciones privadas se administran aqui.</p>
            <p class="location">Politica actual: pago pendiente con minimo 24 horas; pago realizado con 48 horas para reembolso total, entre 24 y 48 horas para reembolso parcial del 50%.</p>
        </section>

        <?php if (count($datos) > 0): ?>
            <?php foreach ($datos as $item): ?>
                <?php $pago_actual = $pagos_reserva[$item['Id_reserva']] ?? null; ?>
                <?php $politica_actual = $politicas_cancelacion[$item['Id_reserva']] ?? ['permitida' => false, 'mensaje' => 'sin informacion', 'horas_restantes' => 0, 'plazo_horas' => 0]; ?>
                <?php $estado_pago = $pago_actual['estado_pago'] ?? 'sin-pago'; ?>
                <?php $estado_pago_clase = str_replace(' ', '-', $estado_pago); ?>
                <?php $monto_pago = (int) ($pago_actual['monto'] ?? 0); ?>
                <?php $monto_reembolso = (int) ($pago_actual['monto_reembolso'] ?? 0); ?>
                <?php $neto_pago = max(0, $monto_pago - $monto_reembolso); ?>
                <div class="review-item review-item-card">
                    <img src="public/css/img/user.jpg" class="review-photo" alt="Reserva">
                    <div>
                        <h4><?= htmlspecialchars($item['nombre']) ?> - Habitacion #<?= htmlspecialchars($item['id_habitacion']) ?></h4>
                        <p>Ubicacion: <?= htmlspecialchars($item['ubicacion']) ?></p>
                        <p>Entrada: <?= htmlspecialchars($item['fecha_ingreso']) ?></p>
                        <p>Salida: <?= htmlspecialchars($item['fecha_salida']) ?></p>
                        <p>Servicio especial: <?= htmlspecialchars($item['servicio_especial']) ?></p>
                        <p>Estado de la reserva: <strong><?= htmlspecialchars($item['estado_reserva']) ?></strong></p>
                        <p>Precio: $<?= number_format((int) ($item['precio'] ?? 0), 0, ',', '.') ?></p>
                        <?php if ($pago_actual): ?>
                            <!-- Este resumen deja visible el estado financiero actual
                                 antes de mostrar el recibo detallado. -->
                            <div class="payment-summary">
                                <p>Estado del pago:
                                    <span class="payment-chip payment-chip-<?= htmlspecialchars($estado_pago_clase) ?>">
                                        <?= htmlspecialchars($pago_actual['estado_pago']) ?>
                                    </span>
                                </p>
                                <p>Monto registrado: $<?= number_format((int) ($pago_actual['monto'] ?? 0), 0, ',', '.') ?></p>
                                <p>Metodo de pago: <?= htmlspecialchars($pago_actual['metodo_pago']) ?></p>
                                <p>Comprobante: <?= htmlspecialchars($pago_actual['referencia'] ?? 'sin comprobante') ?></p>
                                <p>Fecha de pago: <?= htmlspecialchars($pago_actual['fecha_pago'] ?? 'pendiente') ?></p>
                            </div>
                            <!-- El recibo concentra la informacion necesaria para
                                 imprimir o guardar un comprobante simple del pago. -->
                            <div class="payment-receipt" id="receipt-<?= htmlspecialchars($item['Id_reserva']) ?>">
                                <div class="payment-receipt-title">
                                    <div class="payment-receipt-brand">
                                        <img src="public/css/img/travel_now_no_bg.png" alt="Travel Now" class="payment-receipt-logo">
                                        <div>
                                            <h5>Recibo de pago</h5>
                                            <div class="payment-receipt-subtitle">Travel Now · Reserva #<?= htmlspecialchars($item['Id_reserva']) ?> · Habitacion #<?= htmlspecialchars($item['id_habitacion']) ?></div>
                                        </div>
                                    </div>
                                    <span class="payment-chip payment-chip-<?= htmlspecialchars($estado_pago_clase) ?>">
                                        <?= htmlspecialchars($pago_actual['estado_pago']) ?>
                                    </span>
                                </div>
                                <div class="receipt-grid">
                                    <div class="receipt-item">
                                        <strong>Comprobante</strong>
                                        <?= htmlspecialchars($pago_actual['referencia'] ?? 'sin comprobante') ?>
                                    </div>
                                    <div class="receipt-item">
                                        <strong>Metodo</strong>
                                        <?= htmlspecialchars($pago_actual['metodo_pago']) ?>
                                    </div>
                                    <div class="receipt-item">
                                        <strong>Fecha de pago</strong>
                                        <?= htmlspecialchars($pago_actual['fecha_pago'] ?? 'pendiente') ?>
                                    </div>
                                    <div class="receipt-item">
                                        <strong>Estado de reserva</strong>
                                        <?= htmlspecialchars($item['estado_reserva']) ?>
                                    </div>
                                    <div class="receipt-item">
                                        <strong>Monto original</strong>
                                        $<?= number_format($monto_pago, 0, ',', '.') ?>
                                    </div>
                                    <div class="receipt-item">
                                        <strong>Reembolso</strong>
                                        $<?= number_format($monto_reembolso, 0, ',', '.') ?>
                                    </div>
                                </div>
                                <div class="receipt-total">
                                    <span>Total neto del recibo</span>
                                    <span>$<?= number_format($neto_pago, 0, ',', '.') ?></span>
                                </div>
                                <div class="receipt-actions">
                                    <button type="button" class="receipt-action-button receipt-action-print" data-receipt-id="receipt-<?= htmlspecialchars($item['Id_reserva']) ?>" data-receipt-title="Recibo de pago #<?= htmlspecialchars($item['Id_reserva']) ?>">
                                        Imprimir recibo
                                    </button>
                                    <button type="button" class="receipt-action-button receipt-action-pdf" data-receipt-id="receipt-<?= htmlspecialchars($item['Id_reserva']) ?>" data-receipt-title="Recibo de pago #<?= htmlspecialchars($item['Id_reserva']) ?>">
                                        Guardar PDF
                                    </button>
                                </div>
                            </div>
                            <?php if (($pago_actual['estado_pago'] ?? '') === 'en revision'): ?>
                                <p>Tu pago fue enviado a revision. El host debe confirmarlo o marcarlo como fallido.</p>
                                <p>Detalle de revision: <?= htmlspecialchars($pago_actual['detalle_reembolso'] ?? 'sin detalle') ?></p>
                            <?php endif ?>
                            <?php if (($pago_actual['estado_pago'] ?? '') === 'fallido'): ?>
                                <p>El pago fue marcado como fallido. Puedes reenviarlo nuevamente desde esta reserva.</p>
                                <p>Motivo del fallo: <?= htmlspecialchars($pago_actual['detalle_reembolso'] ?? 'sin detalle') ?></p>
                            <?php endif ?>
                            <?php if (($pago_actual['estado_pago'] ?? '') === 'reembolsado'): ?>
                                <p>Monto reembolsado: $<?= number_format((int) ($pago_actual['monto_reembolso'] ?? 0), 0, ',', '.') ?></p>
                                <p>Detalle reembolso: <?= htmlspecialchars($pago_actual['detalle_reembolso'] ?? 'sin detalle') ?></p>
                            <?php endif ?>
                            <?php if (($pago_actual['estado_pago'] ?? '') === 'cancelado'): ?>
                                <p>Detalle cancelacion de pago: <?= htmlspecialchars($pago_actual['detalle_reembolso'] ?? 'sin cobro') ?></p>
                            <?php endif ?>
                            <?php if (($item['estado_reserva'] ?? '') === 'aprobada' && in_array(($pago_actual['estado_pago'] ?? ''), ['pendiente', 'fallido'], true)): ?>
                                <!-- Si el pago aun no esta confirmado, el usuario puede
                                     reenviarlo a revision con metodo y comprobante. -->
                                <form action="index.php?controller=reserva&action=pagar" method="post" class="payment-form">
                                    <input type="hidden" name="id_reserva" value="<?= htmlspecialchars($item['Id_reserva']) ?>">
                                    <input type="hidden" name="id_habitacion" value="<?= htmlspecialchars($item['id_habitacion']) ?>">
                                    <input type="hidden" name="return_view" value="user_misreservas">
                                    <input type="hidden" name="accion_pago" value="revision">
                                    <select name="metodo_pago" class="payment-select">
                                        <option value="transferencia">transferencia</option>
                                        <option value="efectivo">efectivo</option>
                                        <option value="tarjeta">tarjeta</option>
                                    </select>
                                    <input type="text" name="referencia" value="" placeholder="Comprobante opcional" class="payment-input">
                                    <button type="submit" class="payment-button">Enviar pago a revision</button>
                                </form>
                                <p class="payment-note">Si dejas el comprobante vacio, el sistema generara uno automaticamente.</p>
                            <?php endif ?>
                        <?php else: ?>
                            <p>Estado del pago: <strong>sin pago generado</strong></p>
                        <?php endif ?>
                        <p>
                            Cancelacion:
                            <strong><?= $politica_actual['permitida'] ? 'disponible' : 'no disponible' ?></strong>
                        </p>
                        <p><?= htmlspecialchars($politica_actual['mensaje']) ?></p>
                        <?php if (($politica_actual['plazo_horas'] ?? 0) > 0 && ($item['estado_reserva'] ?? '') !== 'cancelada' && ($item['estado_reserva'] ?? '') !== 'rechazada'): ?>
                            <p>Plazo requerido: <?= htmlspecialchars((string) $politica_actual['plazo_horas']) ?> horas antes del ingreso.</p>
                            <p>Tiempo restante aproximado: <?= htmlspecialchars((string) $politica_actual['horas_restantes']) ?> horas.</p>
                        <?php endif ?>
                        <?php if (($politica_actual['tipo_reembolso'] ?? '') === 'reembolso total'): ?>
                            <p>Si cancelas ahora, aplica reembolso total del pago.</p>
                            <p>Monto estimado de reembolso: $<?= number_format((int) ($politica_actual['monto_estimado_reembolso'] ?? 0), 0, ',', '.') ?></p>
                        <?php endif ?>
                        <?php if (($politica_actual['tipo_reembolso'] ?? '') === 'reembolso parcial'): ?>
                            <p>Si cancelas ahora, aplica reembolso parcial del 50%.</p>
                            <p>Monto estimado de reembolso: $<?= number_format((int) ($politica_actual['monto_estimado_reembolso'] ?? 0), 0, ',', '.') ?></p>
                        <?php endif ?>
                        <?php if (($politica_actual['tipo_reembolso'] ?? '') === 'sin reembolso'): ?>
                            <p>Si cancelaras en este tramo, no aplicaria reembolso.</p>
                        <?php endif ?>
                        <?php if (in_array(($item['estado_reserva'] ?? ''), ['pendiente', 'aprobada'], true) && $politica_actual['permitida']): ?>
                            <form action="index.php?controller=reserva&action=cancelar" method="post" class="cancel-form">
                                <input type="hidden" name="id_reserva" value="<?= htmlspecialchars($item['Id_reserva']) ?>">
                                <button type="submit" class="cancel-button">Cancelar reserva</button>
                            </form>
                            <?php if (($pago_actual['estado_pago'] ?? '') === 'pendiente'): ?>
                                <p>Si cancelas esta reserva, el pago pendiente pasara a cancelado.</p>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <section class="description">
                <h3>No tienes reservas registradas</h3>
                <p>Cuando hagas una reserva con tu usuario, aparecera aqui.</p>
            </section>
        <?php endif ?>
    </div>
    <script src="public/js/misreservas-recibo.js"></script>
</body>
</html>
