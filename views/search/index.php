
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css/search.css">
    <title>Search</title>
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

        <div class="header-buttons">
            <a href="index.php?controller=user&action=crear">
                <button class="btn">Conviertete en Huesped</button>
            </a>

            <a href="user/Perfil.php">
                <button class="user-icon">☺</button>
            </a>
        </div>
    </header>

    <div class="container">
        <div class="left-column">
            <div class="results-title"><?= count($datos) ?> resultados encontrados</div>

            <div class="badges">
                <span>Busqueda real</span>
                <span>Habitaciones</span>
                <span>Precios</span>
            </div>

            <button class="filter-btn">Filtro</button>

            <?php if (count($datos) > 0): ?>
                <?php foreach ($datos as $item): ?>
                    <!-- Cada tarjeta muestra una propiedad real de BD y redirige
                         directo al detalle de reserva de la habitacion. -->
                    <a href="index.php?controller=reserva&action=index&id=<?= $item['Id_habitacion'] ?>" class="search-card-link">
                    <div class="card search-card-clickable">
                        <img class="card-image1 card-image-tag" src="public/css/img/<?= htmlspecialchars($item['imagen'] ?? 'habitacion1.jpg') ?>" alt="<?= htmlspecialchars($item['nombre']) ?>">

                        <div class="card-content">
                            <div class="card-user">
                                <img>
                                <div>
                                    <strong><?= htmlspecialchars($item['nombre']) ?></strong><br>
                                    Precio: $<?= number_format((int) ($item['precio'] ?? 0), 0, ',', '.') ?>
                                </div>
                            </div>

                            <div class="card-title">
                                Habitacion #<?= htmlspecialchars($item['Id_habitacion']) ?>
                            </div>

                            <div class="card-icons">
                                <span>🏠 Tipo <?= htmlspecialchars($item['tipo_habitacion']) ?></span>
                                <span>👥 <?= htmlspecialchars((string) ($item['capacidad'] ?? 0)) ?></span>
                                <span>🛁 <?= htmlspecialchars((string) ($item['banos'] ?? 0)) ?></span>
                            </div>

                            <div class="card-footer">
                                Estado: <?= htmlspecialchars($item['estado'] ?? 'sin estado') ?>
                            </div>

                            <div class="card-footer">
                                <?= htmlspecialchars($item['ubicacion']) ?>
                            </div>

                            <div class="card-footer">
                                <?= htmlspecialchars(mb_strimwidth((string) ($item['descripcion_texto'] ?? 'Sin descripcion'), 0, 90, '...')) ?>
                            </div>

                            <div class="card-footer">Ver reserva</div>
                        </div>
                    </div>
                    </a>
                <?php endforeach ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-content">
                        <div class="card-title">No hay habitaciones registradas</div>
                        <div class="card-footer">Primero debes crear empresa, habitacion y precio en la base de datos.</div>
                    </div>
                </div>
            <?php endif ?>
        </div>

        <div class="map-column">
            <div class="map-box">
                <div id="map" style="height: 500px; width: 100%; margin-bottom: 20px;"></div>
            </div>
            <!-- LEAFLET -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"/>
            <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
            // 📦 Datos desde PHP (habitaciones)
            let locations = <?php echo json_encode($datos ?? []); ?>;

            // 📍 Coordenadas por defecto (Bogotá)
            let defaultLat = 5.4457655;
            let defaultLng = -74.6618458;

            // 🚀 Crear mapa
            let map = L.map('map').setView([defaultLat, defaultLng], 12);

            // 🌍 Mapa base
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            // 📍 Pintar marcadores de habitaciones
            locations.forEach(loc => {
                // Si no hay lat/lng, no pintar
                if (!loc.ubicacion || typeof loc.ubicacion !== 'string') return;
                // Espera formato "lat,lng" en ubicacion
                let parts = loc.ubicacion.split(',');
                if (parts.length !== 2) return;
                let lat = parseFloat(parts[0]);
                let lng = parseFloat(parts[1]);
                if (isNaN(lat) || isNaN(lng)) return;

                let popup = `
                    <b>${loc.nombre ?? ''}</b><br>
                    <span>Habitación #${loc.Id_habitacion ?? ''}</span><br>
                    <span>Precio: $${loc.precio ?? ''}</span><br>
                    <span>Tipo: ${loc.tipo_habitacion ?? ''}</span><br>
                    <a href='index.php?controller=reserva&action=index&id=${loc.Id_habitacion ?? ''}'>Ver reserva</a><br>
                    <a href='https://www.google.com/maps?q=${lat},${lng}' target='_blank'>Ver en Google Maps</a>
                `;
                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(popup);
            });

            // 🔥 Arreglar render si está en tab oculto
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
            </script>
        </div>
    </div>
</body>
</html>
