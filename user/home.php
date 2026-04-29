<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="user.css/style.css">
    <link rel="icon" type="image/png" href="public/css/img/icono-removebg-preview.png">
</head>

<body>

    <header class="header">
        <!-- logo -->
        <img src="public/css/img/travel_now_no_bg.png" class="logo-img" alt="Logo">
        <?php
        if (isset($_GET['msg'])) {
            echo "<p class='feedback-ok'>" . htmlspecialchars($_GET['msg']) . "</p>";
        }
        ?>
        <nav>

            <a href="index.php?controller=login&action=user">Inicio</a>
            <a href="index.php?controller=reserva&action=index">Reserva</a>
            <a href="index.php?controller=search&action=index">Buscar</a>
            <a href="index.php?controller=reserva&action=misreservas">Mis reservas</a>
            <a href="index.php?controller=login&action=logout">Cerrar</a>
            <?= $_SESSION['user']; ?>

            <!-- Muestra el rol del usuario -->
            <?= $_SESSION['rol']; ?>
        </nav>
        <div class="header-buttons">
              
            <a href="user/Perfil.php">
                <button class="user-icon">☺</button>
            </a>
            <a href="user/Perfil.php">
                <button class="edit-btn">Perfil</button>
            </a>
    </header>

    <div class="banner">
        <img src="public/css/img/banner (1).png" alt="Banner">
    </div>

    <?php
        // Separa el listado general por tipo para reutilizar una sola
        // consulta y pintar tabs distintas en la vista de inicio.
        $habitaciones_data = [];
        $pisos_data = [];
        $hoteles_data = [];

        foreach (($datos ?? []) as $item) {
            if ((int) ($item['tipo_habitacion'] ?? 0) === 1) {
                $habitaciones_data[] = $item;
                continue;
            }

            if ((int) ($item['tipo_habitacion'] ?? 0) === 2) {
                $pisos_data[] = $item;
                continue;
            }

            $hoteles_data[] = $item;
        }
    ?>

    <div class="find-container">
        <h2>BUSCA</h2>

        <div class="find-tabs">
            <span class="tab active" data-target="rooms">Habitaciones</span>
            <span class="tab" data-target="flats">Pisos</span>
            <span class="tab" data-target="hostels">Hoteles</span>


            <div class="underline"></div>
        </div>
    </div>
    <div class="tab-content active" id="rooms">
        <div class="grid">
            <?php foreach ($habitaciones_data as $item): ?>
                <!-- Las tarjetas publicadas por el admin son clickeables
                     y llevan al detalle real de la habitacion. -->
                <a href="index.php?controller=reserva&action=index&id=<?= htmlspecialchars($item['Id_habitacion']) ?>" class="property-card-link">
                    <div class="property-card property-card-clickable">
                        <img class="card-img card-img-tag" src="public/css/img/<?= htmlspecialchars($item['imagen'] ?? 'habitacion1.jpg') ?>" alt="<?= htmlspecialchars($item['nombre']) ?>">
                        <div class="price">$<?= number_format((int) ($item['precio'] ?? 0), 0, ',', '.') ?> COP</div>
                        <h4><?= htmlspecialchars($item['nombre']) ?></h4>
                        <p class="location"><?= htmlspecialchars($item['ubicacion']) ?></p>
                        <div class="rating">Capacidad <?= htmlspecialchars((string) ($item['capacidad'] ?? '0')) ?> | Banos <?= htmlspecialchars((string) ($item['banos'] ?? '0')) ?></div>
                        <div class="fav">♡</div>
                    </div>
                </a>
            <?php endforeach ?>
            <?php if (count($habitaciones_data) === 0): ?>
                <div class="property-card">
                    <h4>No hay habitaciones publicadas</h4>
                    <p class="location">El admin aun no ha creado habitaciones reales.</p>
                </div>
            <?php endif ?>
        </div>
    </div>


    <div class="tab-content active" id="flats">
        <div class="grid">
            <?php foreach ($pisos_data as $item): ?>
                <!-- Repite el mismo patron para pisos usando datos reales
                     del tipo de habitacion 2. -->
                <a href="index.php?controller=reserva&action=index&id=<?= htmlspecialchars($item['Id_habitacion']) ?>" class="property-card-link">
                    <div class="property-card property-card-clickable">
                        <img class="card-img card-img-tag" src="public/css/img/<?= htmlspecialchars($item['imagen'] ?? 'piso1.jpg') ?>" alt="<?= htmlspecialchars($item['nombre']) ?>">
                        <div class="price">$<?= number_format((int) ($item['precio'] ?? 0), 0, ',', '.') ?> COP</div>
                        <h4><?= htmlspecialchars($item['nombre']) ?></h4>
                        <p class="location"><?= htmlspecialchars($item['ubicacion']) ?></p>
                        <div class="rating">Capacidad <?= htmlspecialchars((string) ($item['capacidad'] ?? '0')) ?> | Banos <?= htmlspecialchars((string) ($item['banos'] ?? '0')) ?></div>
                        <div class="fav">♡</div>
                    </div>
                </a>
            <?php endforeach ?>
            <?php if (count($pisos_data) === 0): ?>
                <div class="property-card">
                    <h4>No hay pisos publicados</h4>
                    <p class="location">El admin aun no ha creado pisos reales.</p>
                </div>
            <?php endif ?>
        </div>
    </div>

    <div class="tab-content active" id="hostels">
        <div class="grid">
            <?php foreach ($hoteles_data as $item): ?>
                <!-- Repite el mismo patron para hoteles y mantiene
                     la navegacion consistente hacia la reserva. -->
                <a href="index.php?controller=reserva&action=index&id=<?= htmlspecialchars($item['Id_habitacion']) ?>" class="property-card-link">
                    <div class="property-card property-card-clickable">
                        <img class="card-img card-img-tag" src="public/css/img/<?= htmlspecialchars($item['imagen'] ?? 'hotel1.jpg') ?>" alt="<?= htmlspecialchars($item['nombre']) ?>">
                        <div class="price">$<?= number_format((int) ($item['precio'] ?? 0), 0, ',', '.') ?> COP</div>
                        <h4><?= htmlspecialchars($item['nombre']) ?></h4>
                        <p class="location"><?= htmlspecialchars($item['ubicacion']) ?></p>
                        <div class="rating">Capacidad <?= htmlspecialchars((string) ($item['capacidad'] ?? '0')) ?> | Banos <?= htmlspecialchars((string) ($item['banos'] ?? '0')) ?></div>
                        <div class="fav">♡</div>
                    </div>
                </a>
            <?php endforeach ?>
            <?php if (count($hoteles_data) === 0): ?>
                <div class="property-card">
                    <h4>No hay hoteles publicados</h4>
                    <p class="location">El admin aun no ha creado hoteles reales.</p>
                </div>
            <?php endif ?>
        </div>
    </div>




    <div class="search-box">
        <input type="text" placeholder="Locacion">
        <input type="text" placeholder="Tipo de propiedad">
        <input type="text" placeholder="Precio">
        <button class="search-btn">Buscar</button>
    </div>
    </section>
    <section class="listing-section">
        <div class="flex-between">
            <h3>Propiedades de ubicación</h3>
            <a href="#" class="show-map">🗺 Mapa</a>
        </div>

        <div class="cards-container">
            <div class="card">
                <div class="card-img map1"></div>
                <p>Zona cercas</p>
            </div>

            <div class="card">
                <div class="card-img pa1"></div>
                <p>Paquetes</p>
            </div>

            <div class="card">
                <div class="card-img turismo1"></div>
                <p>Turismo</p>
            </div>


        </div>
    </section>
    <script src="public/css/moviiento.js"></script>
</body>

</html>
