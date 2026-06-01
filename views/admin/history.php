<?php
/** @var array $datos */
$datos = $datos ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/history.css">
    <link rel="stylesheet" href="public/css/dashboard.css">
    <title>Historial de transacciones</title>
</head>
<body class="dashboard-body">
    <?php $nav_activo = 'historial'; require_once __DIR__ . '/_nav.php'; ?>

    <h1>Historial de transacciones</h1>

    <?php if (isset($_GET['msg'])): ?>
        <p class="feedback-success"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif ?>

    <?php
        $total_completas = count($datos['completed'] ?? []);
        $total_proceso = count($datos['upcoming'] ?? []);
        $total_revertidas = count($datos['reverted'] ?? []);
        $monto_pendiente = 0;
        $monto_revertido = 0;
        $monto_cobrado = (int) ($datos['gross'] ?? 0);
        $total_reembolso_total = 0;
        $total_reembolso_parcial = 0;

        foreach (($datos['upcoming'] ?? []) as $itemPendiente) {
            $monto_pendiente += (int) ($itemPendiente['monto'] ?? 0);
        }

        foreach (($datos['reverted'] ?? []) as $itemRevertido) {
            $monto_revertido += (int) ($itemRevertido['monto_reembolso'] ?? 0);

            if (($itemRevertido['detalle_reembolso'] ?? '') === 'reembolso total') {
                $total_reembolso_total++;
            }

            if (($itemRevertido['detalle_reembolso'] ?? '') === 'reembolso parcial') {
                $total_reembolso_parcial++;
            }
        }

        $monto_neto = $monto_cobrado - $monto_revertido;
    ?>

    <div class="stats-row">
        <div class="stat-pill">Pagados: <?= $total_completas ?></div>
        <div class="stat-pill">Pendientes: <?= $total_proceso ?></div>
        <div class="stat-pill">Revertidos: <?= $total_revertidas ?></div>
        <div class="stat-pill">Cobrado: $ <?= number_format($monto_cobrado, 0, ',', '.') ?></div>
        <div class="stat-pill">Total pendiente: $ <?= number_format($monto_pendiente, 0, ',', '.') ?></div>
        <div class="stat-pill">Total revertido: $ <?= number_format($monto_revertido, 0, ',', '.') ?></div>
        <div class="stat-pill">Neto: $ <?= number_format($monto_neto, 0, ',', '.') ?></div>
        <div class="stat-pill">Reembolso total: <?= $total_reembolso_total ?></div>
        <div class="stat-pill">Reembolso parcial: <?= $total_reembolso_parcial ?></div>
    </div> 

    <!-- Filtros rapidos para que el host revise pagos por estado y rango de fechas. -->
    <form method="get" action="index.php" class="filter-form">
        <input type="hidden" name="controller" value="reserva">
        <input type="hidden" name="action" value="history">
        <select name="estado_pago" class="filter-control">
            <option value="">todos los pagos</option>
            <option value="pendiente" <?= (($_GET['estado_pago'] ?? '') === 'pendiente') ? 'selected' : '' ?>>pendiente</option>
            <option value="en revision" <?= (($_GET['estado_pago'] ?? '') === 'en revision') ? 'selected' : '' ?>>en revision</option>
            <option value="pagado" <?= (($_GET['estado_pago'] ?? '') === 'pagado') ? 'selected' : '' ?>>pagado</option>
            <option value="fallido" <?= (($_GET['estado_pago'] ?? '') === 'fallido') ? 'selected' : '' ?>>fallido</option>
            <option value="cancelado" <?= (($_GET['estado_pago'] ?? '') === 'cancelado') ? 'selected' : '' ?>>cancelado</option>
            <option value="reembolsado" <?= (($_GET['estado_pago'] ?? '') === 'reembolsado') ? 'selected' : '' ?>>reembolsado</option>
        </select>
        <input type="date" name="desde" value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>" class="filter-control">
        <input type="date" name="hasta" value="<?= htmlspecialchars($_GET['hasta'] ?? '') ?>" class="filter-control">
        <button type="submit" class="filter-button">Filtrar</button>
        <a href="index.php?controller=reserva&action=history" class="filter-link">Limpiar</a>
    </form>

    <div class="download">Resumen</div>

    <div class="tabs" id="history-tabs">
        <span class="active" data-tab="completed">completas</span>
        <span data-tab="upcoming">En proceso</span>
        <span data-tab="reverted">Cancelados</span>
        <span data-tab="gross">Total de costos</span>
    </div>

    <div class="line"></div>

    <!-- Pagos ya confirmados y cobrados completamente. -->
    <div id="completedTab" class="tab-panel">
        <?php if (count($datos['completed']) > 0): ?>
            <?php foreach ($datos['completed'] as $item): ?>
                <?php $monto_original = (int) ($item['monto'] ?? 0); ?>
                <div class="transaction-box">
                    <div class="transaction-info">
                        <div class="title"><?= htmlspecialchars($item['nombre']) ?></div>
                        <div>
                            Entrada: <?= htmlspecialchars($item['fecha_ingreso']) ?>
                            | Salida: <?= htmlspecialchars($item['fecha_salida']) ?>
                        </div>
                        <div>Servicio: <?= htmlspecialchars($item['servicio_especial']) ?></div>
                        <div>Estado reserva: <?= htmlspecialchars($item['estado_reserva']) ?></div>
                        <div>Estado pago: <?= htmlspecialchars($item['estado_pago']) ?></div>
                        <div>Metodo: <?= htmlspecialchars($item['metodo_pago']) ?></div>
                        <div>Referencia: <?= htmlspecialchars($item['referencia'] ?? 'sin referencia') ?></div>
                        <div>Detalle reembolso: no aplica</div>
                        <div>Monto original: $ <?= number_format($monto_original, 0, ',', '.') ?></div>
                        <div>Monto reembolsado: $ 0</div>
                        <div>Neto por reserva: $ <?= number_format($monto_original, 0, ',', '.') ?></div>
                    </div>
                    <div class="amount">$ <?= number_format($monto_original, 0, ',', '.') ?></div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="transaction-box">
                <div class="transaction-info">
                    <div class="title">No hay transacciones completas</div>
                    <div>Las reservas aprobadas o finalizadas apareceran aqui.</div>
                </div>
                <div class="amount">$ 0</div>
            </div>
        <?php endif ?>
    </div>

    <!-- Pagos pendientes, en revision o aun por resolver desde el host. -->
    <div id="upcomingTab" class="tab-panel tab-panel-hidden">
        <?php if (count($datos['upcoming']) > 0): ?>
            <?php foreach ($datos['upcoming'] as $item): ?>
                <div class="transaction-box">
                    <div class="transaction-info">
                        <div class="title"><?= htmlspecialchars($item['nombre']) ?></div>
                        <div>
                            Entrada: <?= htmlspecialchars($item['fecha_ingreso']) ?>
                            | Salida: <?= htmlspecialchars($item['fecha_salida']) ?>
                        </div>
                        <div>Servicio: <?= htmlspecialchars($item['servicio_especial']) ?></div>
                        <div>Estado reserva: <?= htmlspecialchars($item['estado_reserva']) ?></div>
                        <div>Estado pago: <?= htmlspecialchars($item['estado_pago']) ?></div>
                        <div>Detalle: <?= htmlspecialchars($item['detalle_reembolso'] ?? 'sin detalle') ?></div>
                        <div>Monto original: $ <?= number_format((int) ($item['monto'] ?? 0), 0, ',', '.') ?></div>
                        <div>Monto reembolsado: $ 0</div>
                        <div>Neto por reserva: $ 0</div>
                        <?php if (($item['estado_reserva'] ?? '') === 'aprobada' && in_array(($item['estado_pago'] ?? ''), ['pendiente', 'en revision', 'fallido'], true)): ?>
                            <form action="index.php?controller=reserva&action=pagar" method="post" class="pay-form">
                                <input type="hidden" name="id_reserva" value="<?= htmlspecialchars($item['Id_reserva']) ?>">
                                <select name="metodo_pago" class="pay-select">
                                    <option value="transferencia">transferencia</option>
                                    <option value="efectivo">efectivo</option>
                                    <option value="tarjeta">tarjeta</option>
                                </select>
                                <input type="text" name="referencia" value="<?= htmlspecialchars($item['referencia'] ?? '') ?>" class="pay-input">
                                <input type="text" name="detalle_pago" value="<?= htmlspecialchars($item['detalle_reembolso'] ?? '') ?>" class="pay-input" placeholder="Motivo o nota del pago">
                                <button type="submit" name="accion_pago" value="pagado" class="pay-button">Marcar pagado</button>
                                <button type="submit" name="accion_pago" value="revision" class="pay-button">Enviar a revision</button>
                                <button type="submit" name="accion_pago" value="fallido" class="pay-button">Marcar fallido</button>
                            </form>
                        <?php elseif (($item['estado_reserva'] ?? '') !== 'aprobada'): ?>
                            <div class="status-note">Espera aprobacion de la reserva para registrar el pago.</div>
                        <?php endif ?>
                    </div>
                    <div class="amount">$ <?= number_format((int) ($item['monto'] ?? 0), 0, ',', '.') ?></div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="transaction-box">
                <div class="transaction-info">
                    <div class="title">No hay transacciones en proceso</div>
                    <div>Las reservas pendientes apareceran aqui.</div>
                </div>
                <div class="amount">$ 0</div>
            </div>
        <?php endif ?>
    </div>

    <!-- Reservas canceladas, reembolsadas o pagos fallidos. -->
    <div id="revertedTab" class="tab-panel tab-panel-hidden">
        <?php if (count($datos['reverted']) > 0): ?>
            <?php foreach ($datos['reverted'] as $item): ?>
                <?php $monto_original = (int) ($item['monto'] ?? 0); ?>
                <?php $monto_reembolsado = (int) ($item['monto_reembolso'] ?? 0); ?>
                <?php $neto_reserva = (($item['estado_pago'] ?? '') === 'reembolsado') ? ($monto_original - $monto_reembolsado) : 0; ?>
                <div class="transaction-box">
                    <div class="transaction-info">
                        <div class="title"><?= htmlspecialchars($item['nombre']) ?></div>
                        <div>
                            Entrada: <?= htmlspecialchars($item['fecha_ingreso']) ?>
                            | Salida: <?= htmlspecialchars($item['fecha_salida']) ?>
                        </div>
                        <div>Servicio: <?= htmlspecialchars($item['servicio_especial']) ?></div>
                        <div>Estado reserva: <?= htmlspecialchars($item['estado_reserva']) ?></div>
                        <div>Estado pago: <?= htmlspecialchars($item['estado_pago']) ?></div>
                        <div>Metodo: <?= htmlspecialchars($item['metodo_pago']) ?></div>
                        <div>Referencia: <?= htmlspecialchars($item['referencia'] ?? 'sin referencia') ?></div>
                        <div>Detalle reembolso: <?= htmlspecialchars($item['detalle_reembolso'] ?? 'sin detalle') ?></div>
                        <div>Monto original: $ <?= number_format($monto_original, 0, ',', '.') ?></div>
                        <div>Monto reembolsado: $ <?= number_format($monto_reembolsado, 0, ',', '.') ?></div>
                        <div>Neto por reserva: $ <?= number_format($neto_reserva, 0, ',', '.') ?></div>
                    </div>
                    <div class="amount">$ <?= number_format($monto_reembolsado, 0, ',', '.') ?></div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="transaction-box">
                <div class="transaction-info">
                        <div class="title">No hay pagos cancelados, reembolsados o fallidos</div>
                        <div>Las reservas canceladas, devueltas o con fallo apareceran aqui.</div>
                </div>
                <div class="amount">$ 0</div>
            </div>
        <?php endif ?>
    </div>

    <!-- Resumen neto del flujo financiero acumulado del host. -->
    <div id="grossTab" class="tab-panel tab-panel-hidden">
        <div class="transaction-box">
            <div class="transaction-info">
                <div class="title">Ganancias acumuladas</div>
                <div>Resumen neto del flujo financiero del host</div>
                <div>Cobrado: $ <?= number_format($monto_cobrado, 0, ',', '.') ?></div>
                <div>Reembolsado: $ <?= number_format($monto_revertido, 0, ',', '.') ?></div>
                <div>Neto actual: $ <?= number_format($monto_neto, 0, ',', '.') ?></div>
            </div>
            <div class="amount">$ <?= number_format($monto_neto, 0, ',', '.') ?></div>
        </div>
    </div>

    <script src="public/js/history-tabs.js"></script>
</body>
</html>
