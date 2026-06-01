<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css/style.css">
    <title>Reserva</title>
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
            <a href="index.php?controller=login&action=logout">Cerrar</a>
            <?= $_SESSION['user']; ?>
            <?= $_SESSION['rol']; ?>
        </nav>

        <a href="index.php?controller=user&action=crear">
            <button class="btn">Conviertete en Huesped</button>
        </a>
        <a href="user/Perfil.php">
            <button class="user-icon">☺</button>
        </a>
    </header>

    <div class="container">
        <?php if (isset($_GET['msg'])): ?>
            <p class="feedback-ok"><?= htmlspecialchars($_GET['msg']) ?></p>
        <?php endif ?>

        <?php if (!empty($errores)): ?>
            <?php foreach ($errores as $error): ?>
                <p class="feedback-error"><?= htmlspecialchars($error) ?></p>
            <?php endforeach ?>
        <?php endif ?>

        <?php if ($habitacion): ?>
            <section class="gallery">
                <div class="gallery-left">
                    <img src="public/css/img/<?= htmlspecialchars($habitacion['imagen'] ?? 'habitacion1.jpg') ?>" class="big-img" alt="Habitacion">

                    <div class="host-info">
                        <img src="public/css/img/<?= htmlspecialchars($habitacion['imagen'] ?? 'habitacion1.jpg') ?>" class="host-photo" alt="Host">
                        <div>
                            <p class="host-name">Habitacion #<?= htmlspecialchars($habitacion['Id_habitacion']) ?></p>
                            <p class="host-price">Precio: $<?= number_format((int) ($habitacion['precio'] ?? 0), 0, ',', '.') ?></p>
                        </div>
                    </div>
                </div>

                <div class="gallery-right">
                    <img src="public/css/img/<?= htmlspecialchars($habitacion['imagen'] ?? 'habitacion1.jpg') ?>" class="small-img" alt="Habitacion principal">
                    <img src="public/css/img/habitacion2.png" class="small-img" alt="Habitacion 2">
                    <img src="public/css/img/imagen3.jpg" class="small-img" alt="Habitacion 3">
                </div>
            </section>

            <section class="title-section">
                <h2><?= htmlspecialchars($habitacion['nombre']) ?></h2>
                <p class="location"><?= htmlspecialchars($habitacion['ubicacion']) ?></p>
            </section>

            <section class="overview">
                <div class="card">Habitacion <?= htmlspecialchars($habitacion['Id_habitacion']) ?></div>
                <div class="card">Tipo <?= htmlspecialchars($habitacion['tipo_habitacion']) ?></div>
                <div class="card"><?= htmlspecialchars((string) ($habitacion['capacidad'] ?? 0)) ?> personas</div>
                <div class="card"><?= htmlspecialchars((string) ($habitacion['banos'] ?? 0)) ?> banos</div>
                <div class="card"><?= htmlspecialchars((string) ($habitacion['cupos'] ?? 0)) ?> cupos</div>
                <div class="card"><?= count($reservas) ?> Reservas</div>
            </section>

            <?php
                $pendientes = 0;
                $aprobadas = 0;
                $rechazadas = 0;

                foreach ($reservas as $estadoReserva) {
                    if (($estadoReserva['estado_reserva'] ?? '') === 'pendiente') {
                        $pendientes++;
                    }

                    if (($estadoReserva['estado_reserva'] ?? '') === 'aprobada') {
                        $aprobadas++;
                    }

                    if (($estadoReserva['estado_reserva'] ?? '') === 'rechazada') {
                        $rechazadas++;
                    }
                }
            ?>

            <section class="overview">
                <div class="card">Pendientes <?= $pendientes ?></div>
                <div class="card">Aprobadas <?= $aprobadas ?></div>
                <div class="card">Rechazadas <?= $rechazadas ?></div>
            </section>

            <?php
                $reservas_activas = [];
                $mis_pendientes = 0;
                $mis_aprobadas = 0;
                $mis_rechazadas = 0;

                foreach ($reservas as $reservaActiva) {
                    if (($reservaActiva['estado_reserva'] ?? '') !== 'rechazada') {
                        $reservas_activas[] = $reservaActiva;
                    }
                }

                foreach ($mis_reservas ?? [] as $miReserva) {
                    if (($miReserva['estado_reserva'] ?? '') === 'pendiente') {
                        $mis_pendientes++;
                    }

                    if (($miReserva['estado_reserva'] ?? '') === 'aprobada') {
                        $mis_aprobadas++;
                    }

                    if (($miReserva['estado_reserva'] ?? '') === 'rechazada') {
                        $mis_rechazadas++;
                    }
                }
            ?>

            <section class="description">
                <h3>Habitacion Descripcion</h3>
                <p><?= nl2br(htmlspecialchars($habitacion['descripcion_texto'] ?? 'Sin descripcion')) ?></p>
                <p>Estado actual: <?= htmlspecialchars($habitacion['estado'] ?? 'sin estado') ?></p>
                <p>Servicios: <?= htmlspecialchars($habitacion['servicios'] ?? 'sin servicios') ?></p>
            </section>

            <section class="overview">
                <div class="card">Mis reservas <?= count($mis_reservas) ?></div>
                <div class="card">Mis pendientes <?= $mis_pendientes ?></div>
                <div class="card">Mis aprobadas <?= $mis_aprobadas ?></div>
            </section>

            <div class="reservation-box">
                <div class="price-range modal-launch-area" role="button" tabindex="0" aria-label="Abrir modal de reserva">
                    <span class="price-main">$ <?= number_format((int) ($habitacion['precio'] ?? 0), 0, ',', '.') ?></span>
                    <span class="price-launch-hint">Toca aqui para reservar</span>
                </div>

                <div class="price-details modal-launch-area" role="button" tabindex="0" aria-label="Abrir modal de reserva">
                    <p><strong>Empresa:</strong> <?= htmlspecialchars($habitacion['nombre']) ?></p>
                    <p><strong>Ubicacion:</strong> <?= htmlspecialchars($habitacion['ubicacion']) ?></p>
                    <p><strong>Estado:</strong> <?= htmlspecialchars($habitacion['estado'] ?? 'sin estado') ?></p>
                    <p><strong>Capacidad:</strong> <?= htmlspecialchars((string) ($habitacion['capacidad'] ?? 0)) ?> personas</p>
                    <p><strong>Banos:</strong> <?= htmlspecialchars((string) ($habitacion['banos'] ?? 0)) ?></p>
                    <p><strong>Cupos:</strong> <?= htmlspecialchars((string) ($habitacion['cupos'] ?? 0)) ?></p>
                    <p><strong>Nuevas reservas:</strong> quedan en estado pendiente</p>
                </div>

                <div class="price-details price-details-block">
                    <p><strong>Fechas ocupadas de esta habitacion:</strong></p>
                    <?php if (count($reservas_activas) > 0): ?>
                        <?php foreach ($reservas_activas as $ocupada): ?>
                            <p>
                                <?= date('d/m/Y H:i', strtotime($ocupada['fecha_ingreso'])) ?>
                                a
                                <?= date('d/m/Y H:i', strtotime($ocupada['fecha_salida'])) ?>
                                -
                                <strong><?= htmlspecialchars($ocupada['estado_reserva']) ?></strong>
                            </p>
                        <?php endforeach ?>
                    <?php else: ?>
                        <p>No hay fechas ocupadas en este momento.</p>
                    <?php endif ?>
                </div>

                <button class="reserve-btn reserve-trigger" type="button" id="abrir-modal-reserva">Reservar ahora</button>

                <div class="reservation-actions">
                    <a href="index.php?controller=search&action=index"><button class="secondary-btn" type="button">Propiedades</button></a>
                    <a href="user/Perfil.php"><button class="secondary-btn" type="button">Contacto con el huesped</button></a>
                </div>
            </div>

            <!-- El modal concentra el formulario de reserva y el resumen
                 para no dejar el flujo principal perdido al final de la vista. -->
            <div
                class="modal-overlay"
                id="modal-reserva"
                data-precio-base="<?= (int) ($habitacion['precio'] ?? 0) ?>"
                data-reservas-ocupadas="<?= htmlspecialchars(json_encode(array_map(function ($ocupada) {
                    return [
                        'ingreso' => date('Y-m-d\TH:i:s', strtotime($ocupada['fecha_ingreso'])),
                        'salida' => date('Y-m-d\TH:i:s', strtotime($ocupada['fecha_salida'])),
                        'estado' => $ocupada['estado_reserva'] ?? ''
                    ];
                }, $reservas_activas), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES) ?>"
                data-abrir-errores="<?= !empty($errores) ? '1' : '0' ?>"
            >
                <div class="modal-card">
                    <div class="modal-header">
                        <div>
                            <h3 class="modal-title"><?= htmlspecialchars($habitacion['nombre']) ?></h3>
                            <p class="modal-note"><?= htmlspecialchars($habitacion['ubicacion']) ?></p>
                        </div>
                        <button class="modal-close" type="button" id="cerrar-modal-reserva">&times;</button>
                    </div>

                    <p class="modal-note">Completa los datos de la reserva sin salir del detalle de la propiedad.</p>
                    <!-- Resume la ficha clave de la propiedad antes de reservar. -->
                    <div class="modal-summary">
                        <div class="item"><strong>Precio</strong><br>$ <?= number_format((int) ($habitacion['precio'] ?? 0), 0, ',', '.') ?></div>
                        <div class="item"><strong>Capacidad</strong><br><?= htmlspecialchars((string) ($habitacion['capacidad'] ?? 0)) ?> personas</div>
                        <div class="item"><strong>Banos</strong><br><?= htmlspecialchars((string) ($habitacion['banos'] ?? 0)) ?></div>
                        <div class="item"><strong>Cupos</strong><br><?= htmlspecialchars((string) ($habitacion['cupos'] ?? 0)) ?></div>
                    </div>
                    <!-- Muestra una ventana corta de fechas ocupadas para ayudar
                         al usuario a escoger un rango libre. -->
                    <div class="modal-occupied">
                        <strong>Fechas ocupadas resumidas</strong>
                        <?php if (count($reservas_activas) > 0): ?>
                            <?php foreach (array_slice($reservas_activas, 0, 3) as $ocupada): ?>
                                <p>
                                    <?= date('d/m/Y H:i', strtotime($ocupada['fecha_ingreso'])) ?>
                                    a
                                    <?= date('d/m/Y H:i', strtotime($ocupada['fecha_salida'])) ?>
                                    -
                                    <strong><?= htmlspecialchars($ocupada['estado_reserva']) ?></strong>
                                </p>
                            <?php endforeach ?>
                            <?php if (count($reservas_activas) > 3): ?>
                                <p>Hay <?= count($reservas_activas) - 3 ?> rangos adicionales ocupados en esta habitacion.</p>
                            <?php endif ?>
                        <?php else: ?>
                            <p>No hay fechas ocupadas en este momento.</p>
                        <?php endif ?>
                    </div>
                    <!-- El resumen inferior se actualiza con fechas y metodo
                         elegidos para dejar claro el impacto antes de confirmar. -->
                    <div class="modal-total" id="modal-total-estimado">
                        <strong>Total estimado</strong>
                        <span id="modal-estadia">Selecciona fechas para calcular tu estadia.</span>
                        <span id="modal-total-precio">$ <?= number_format((int) ($habitacion['precio'] ?? 0), 0, ',', '.') ?> por rango estimado</span>
                        <span id="modal-total-metodo" class="modal-total-method">Metodo de pago: Transferencia</span>
                        <span class="modal-total-state">Estado inicial del pago: Pendiente. Luego podras enviarlo a revision desde Mis reservas.</span>
                    </div>
                    <div class="modal-error" id="modal-error-fechas"></div>

                    <!-- El formulario crea la reserva y al mismo tiempo deja
                         preparado el pago pendiente con el metodo seleccionado. -->
                    <form action="index.php?controller=reserva&action=guardar" method="post" id="form-reserva-modal" novalidate>
                        <input type="hidden" name="id_habitacion" value="<?= htmlspecialchars($habitacion['Id_habitacion']) ?>">
                        <input type="hidden" name="tipo_habitacion" value="<?= htmlspecialchars($habitacion['tipo_habitacion']) ?>">

                        <div class="modal-field">
                            <label for="fecha_ingreso"><strong>Fecha de ingreso</strong></label><br>
                            <input type="datetime-local" id="fecha_ingreso" name="fecha_ingreso" required class="modal-input">
                        </div>

                        <div class="modal-field">
                            <label for="fecha_salida"><strong>Fecha de salida</strong></label><br>
                            <input type="datetime-local" id="fecha_salida" name="fecha_salida" required class="modal-input">
                        </div>

                        <div class="modal-field">
                            <label for="servicio_especial"><strong>Servicio especial</strong></label><br>
                            <input type="text" id="servicio_especial" name="servicio_especial" placeholder="Ej: Desayuno, parqueadero" class="modal-input">
                        </div>

                        <div class="modal-field">
                            <label for="metodo_pago"><strong>Metodo de pago</strong></label><br>
                            <select id="metodo_pago" name="metodo_pago" class="modal-input" required>
                                <option value="transferencia" <?= (($_POST['metodo_pago'] ?? 'transferencia') === 'transferencia') ? 'selected' : '' ?>>Transferencia</option>
                                <option value="efectivo" <?= (($_POST['metodo_pago'] ?? '') === 'efectivo') ? 'selected' : '' ?>>Efectivo</option>
                                <option value="tarjeta" <?= (($_POST['metodo_pago'] ?? '') === 'tarjeta') ? 'selected' : '' ?>>Tarjeta</option>
                            </select>
                            <p class="modal-payment-note" id="modal-metodo-pago-note">
                                Metodo seleccionado: se usara para registrar el pago inicial de tu reserva.
                            </p>
                        </div>

                        <button class="reserve-btn" type="submit">Confirmar reserva</button>
                    </form>
                </div>
            </div>

            <section class="services">
                <h3>Servicios</h3>

                <div class="services-row">
                    <div class="service-card">
                        <h4><?= htmlspecialchars($habitacion['nombre']) ?></h4>
                        <p><?= htmlspecialchars($habitacion['ubicacion']) ?></p>
                        <?= !empty($habitacion['estado']) ? htmlspecialchars($habitacion['estado']) : 'Disponible' ?>
                    </div>

                    <div class="service-card">
                        <h4>Tipo de habitacion</h4>
                        <p><?= htmlspecialchars($habitacion['tipo_habitacion']) ?></p>
                        Travel Now
                    </div>

                    <div class="service-card">
                        <h4>Precio actual</h4>
                        <p>$<?= number_format((int) ($habitacion['precio'] ?? 0), 0, ',', '.') ?></p>
                        COP
                    </div>

                    <div class="service-card">
                        <h4>Servicios</h4>
                        <p><?= htmlspecialchars($habitacion['servicios'] ?? 'sin servicios') ?></p>
                        Travel Now
                    </div>
                </div>
            </section>

        <?php else: ?>
            <section class="description">
                <h3>No hay habitaciones disponibles</h3>
                <p>No se encontro una habitacion para mostrar desde la base de datos.</p>
                <p>Puedes crear registros en `empresa`, `habitacion` y `precio` para ver esta vista con datos reales.</p>
                <p><a href="index.php?controller=search&action=index">Ir a buscar habitaciones</a></p>
            </section>
        <?php endif ?>
    </div>

    <script src="public/css/moviiento.js"></script>
    <script src="public/js/reserva-modal.js"></script>
</body>
</html>
