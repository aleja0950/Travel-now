<h1>Mapa CRUD</h1>

<style>
#map {
    height: 500px;
    width: 100%;
    margin-bottom: 20px;
}

form {
    background: #f4f4f4;
    padding: 10px;
    border-radius: 5px;
    width: 300px;
}

input, button {
    width: 100%;
    padding: 6px;
    margin: 5px 0;
}
</style>

<div id="map"></div>

<form method="POST" id="formMapa" action="index.php?controller=map&action=guardar">

    <input type="hidden" name="id_map" id="id_map">

    <input type="text" name="name" id="name" placeholder="Nombre" required>
    <input type="text" name="latitude" id="latitude" placeholder="Latitud" required>
    <input type="text" name="longitude" id="longitude" placeholder="Longitud" required>

    <button type="submit">Guardar</button>
    <button type="button" onclick="resetForm()">Nuevo</button>
    
</form>
<h3>Ubicaciones guardadas</h3>

<ul id="listaUbicaciones">
    <?php foreach ($locations as $loc): ?>
        <li>
            <a href="#"
               onclick="irUbicacion(<?php echo $loc['latitude']; ?>, <?php echo $loc['longitude']; ?>)">
               
               <?php echo $loc['name']; ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
<!-- LEAFLET -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

<?php
$lat = $_GET['lat'] ?? 'null';
$lng = $_GET['lng'] ?? 'null';
?>

<script>

    function irUbicacion(lat, lng) {
    map.setView([lat, lng], 18);
}
// 📍 Coordenadas desde URL
let latURL = <?php echo $lat; ?>;
let lngURL = <?php echo $lng; ?>;

// 📍 Coordenadas por defecto (Bogotá)
let defaultLat = latURL ? latURL : 4.60971;
let defaultLng = lngURL ? lngURL : -74.08175;

// 🚀 Crear mapa
let map = L.map('map').setView([defaultLat, defaultLng], 15);

// 🌍 Mapa base
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
}).addTo(map);

// 📦 Datos desde PHP
let locations = <?php echo json_encode($locations ?? []); ?>;

// 📍 Pintar marcadores
locations.forEach(loc => {

    let popup = `
        <b>${loc.name}</b><br><br>

        <button onclick="irUbicacion(${loc.latitude}, ${loc.longitude})">
            Ir aquí
        </button><br><br>

        <a href="https://www.google.com/maps?q=${loc.latitude},${loc.longitude}" target="_blank">
            Ver en Google Maps
        </a><br><br>

        <button onclick="editar(${loc.id_map}, '${loc.name}', ${loc.latitude}, ${loc.longitude})">
            Editar
        </button>

        <form method="POST" action="index.php?controller=map&action=eliminar">
            <input type="hidden" name="id_map" value="${loc.id_map}">
            <button type="submit">Eliminar</button>
        </form>
    `;

    L.marker([
        parseFloat(loc.latitude),
        parseFloat(loc.longitude)
    ])
    .addTo(map)
    .bindPopup(popup);
});

// 📍 CLICK EN MAPA → AUTOLLENAR
map.on('click', function(e) {
    document.getElementById('latitude').value = e.latlng.lat;
    document.getElementById('longitude').value = e.latlng.lng;
});

// 🚀 IR A UBICACIÓN
function irUbicacion(lat, lng) {
    map.setView([lat, lng], 18);
}

// ✏️ EDITAR
function editar(id, name, lat, lng) {
    document.getElementById('id_map').value = id;
    document.getElementById('name').value = name;
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;

    document.getElementById('formMapa').action = "index.php?controller=map&action=actualizar";
}

// 🔄 RESET FORM
function resetForm() {
    document.getElementById('formMapa').reset();
    document.getElementById('id_map').value = "";
    document.getElementById('formMapa').action = "index.php?controller=map&action=guardar";
}

// 🔥 ARREGLAR RENDER
setTimeout(() => {
    map.invalidateSize();
}, 100);
</script>