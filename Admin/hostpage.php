<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<link rel="stylesheet" href="/travel-now/Travel-now/public/css/host.css">
</head>
<?php
if (!isset($datos)) {
    $datos = [
        'upcoming' => [],
        'past' => [],
        'rejected' => []
    ];
}
?>
<body>
    
    <!-- ================================
         HEADER: LOGO + MENÚ DE NAVEGACIÓN
         ================================ -->
    <header class="header">

        <!-- Logo principal -->
        <img src="/travel-now/Travel-now/public/css/img/icono-removebg-preview.png" class="logo-img" alt="Logo">

        <!-- Menú de navegación -->
        <nav>
            <!-- Mensaje de inicio de sesion exitoso -->
            <?php
                if(isset($_GET['msg'])){
                    echo "<p style='color:green;'>".$_GET['msg']."</p>";
                }
            ?>
            <a href="index.php?controller=login&action=admin">Inicio</a>
            <a href="index.php?controller=propiedad&action=index">Propiedades</a>
            <a href="index.php?controller=paquete&action=admin">Paquetes</a>
            <a href="index.php?controller=reserva&action=history">Historial</a>
            <a href="index.php?controller=reserva&action=host">Reservas</a>
            <a href="index.php?controller=login&action=logout">Cerrar</a>  
            <a href="index.php?controller=map&action=index">Mapa</a>
        </nav>
         <?= $_SESSION['user']; ?>

    <!-- Muestra el rol del usuario -->
    <?= $_SESSION['rol']; ?>

        <!-- Botones del header (Administrador + Perfil) -->
        <div class="header-buttons">

                       <a href="Admin/perfilhost.php">
                <button class="btn">Perfil</button>
            </a>
            

        </div>
    </header>
    
<h1>Reservaciones</h1>

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
                <div class="reservation">
                    <img class="img-box img-box-tag" src="public/css/img/habitacion1.jpg" alt="Habitacion">

                    <div class="info">
                        <div class="title"><?= htmlspecialchars($item['nombre']) ?></div>
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
                        <?php if ($item['estado_reserva'] === 'pendiente'): ?>
                            <form action="index.php?controller=reserva&action=aprobar" method="post" class="inline-form">
                                <input type="hidden" name="id_reserva" value="<?= htmlspecialchars($item['Id_reserva']) ?>">
                                <button class="btn approve" type="submit">Aprobar</button>
                            </form>
                            <form action="index.php?controller=reserva&action=rechazar" method="post" class="inline-form">
                                <input type="hidden" name="id_reserva" value="<?= htmlspecialchars($item['Id_reserva']) ?>">
                                <button class="btn reject" type="submit">Rechazar</button>
                            </form>
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
                <div class="reservation">
                    <img class="img-box img-box-tag" src="public/css/img/habitacion2.png" alt="Habitacion">

                    <div class="info">
                        <div class="title"><?= htmlspecialchars($item['nombre']) ?></div>
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
                <div class="reservation">
                    <img class="img-box img-box-tag" src="public/css/img/imagen3.jpg" alt="Habitacion">

                    <div class="info">
                        <div class="title"><?= htmlspecialchars($item['nombre']) ?></div>
                        <div>Habitacion: <?= htmlspecialchars($item['id_habitacion']) ?></div>
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



