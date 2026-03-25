
 <?php
 session_start()
 
 ?>
 <!DOCTYPE html>
<html lang="en">
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
                if(isset($_GET['msg'])){
                    echo "<p style='color:green;'>".$_GET['msg']."</p>";
                }
            ?>
        <nav>
            
            <a href="user/home.php"> Inicio</a>
            <a href="user/reserva.html">Reserva</a>
            <a href="user/search.html">Buscar</a>
              <a href="index.php?controller=login&action=logout">Cerrar</a> 
                    <?= $_SESSION['user']; ?>

    <!-- Muestra el rol del usuario -->
    <?= $_SESSION['rol']; ?>
        </nav>
        <div class="header-buttons">
          <a href="index.php?controller=user&action=crear">
         
    <button class="btn">Conviertete en Huesped</button>
    
</a>  <a href="user/perfil.html">
    <button class="user-icon">☺</button>
</a>
            </header>

  <div class="banner">
    <img src="public/css/img/banner (1).png" alt="Banner">
</div>

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
        <!-- card -->

        <div class="property-card">
            <div class="card-img habitacion1"></div>
            <div class="price">50.000 – 100.000 COP</div>
            <h4>Hotel Nutibara</h4>
            <p class="location">Tolima</p>
            <div class="rating">★★★★☆</div>
            <div class="fav">♡</div>
        </div>

        <div class="property-card">
            <div class="card-img habitacion2"></div>
            <div class="price">180.000 – 300.000 COP</div>
            <h4>Sofitel Bogotá Victoria Regia</h4>
            <p class="location">Bogota</p>
            <div class="rating">★★★★☆</div>
            <div class="fav">♡</div>
        </div>

        <div class="property-card">
            <div class="card-img habitacion3"></div>
            <div class="price">150.000 – 250.000 COP</div>
            <h4>Sofitel Barú Calablanca</h4>
            <p class="location">Tolu</p>
            <div class="rating">★★★★☆</div>
            <div class="fav">♡</div>
        </div>

    </div>
</div>


<div class="tab-content active" id="flats">
    <div class="grid">

        <div class="property-card">
            <div class="card-img piso1"></div>
            <div class="price">250.000 – 400.000 COP</div>
            <h4>Casa Pestagua</h4>
            <p class="location">Cartagena</p>
            <div class="rating">★★★★☆</div>
            <div class="fav">♡</div>
        </div>

        <div class="property-card">
            <div class="card-img piso2"></div>
            <div class="price">400.000 – 600.000 COP</div>
            <h4>Hotel Estelar Bocagrande</h4>
            <p class="location">Bogota</p>
            <div class="rating">★★★★☆</div>
            <div class="fav">♡</div>
        </div>

        <div class="property-card">
            <div class="card-img piso3"></div>
            <div class="price">500.000 – 800.000 COP</div>
            <h4>Hotel Cartagena Plaza</h4>
            <p class="location">Cartagena</p>
            <div class="rating">★★★★☆</div>
            <div class="fav">♡</div>
        </div>

    </div>
</div>
 
<div class="tab-content active" id="hostels">
    <div class="grid">

        <div class="property-card">
            <div class="card-img img1"></div>
            <div class="price">70.000 – 140.000 COP</div>
            <h4>Hotel Las Américas Torre del Mar</h4>
            <p class="location">Santa Marta</p>
            <div class="rating">★★★★☆</div>
            <div class="fav">♡</div>
        </div>

       

        <div class="property-card">
            <div class="card-img hotel3"></div>
            <div class="price">350.000 – 600.000 COP</div>
            <h4>Hotel Dann Carlton Medellín</h4>
            <p class="location">Medellin</p>
            <div class="rating">★★★★☆</div>
            <div class="fav">♡</div>
        </div>

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
        <h3>Lista de propiedades</h3>

        <div class="cards-container">
            <div class="card">
                <div class="card-img apart1"></div>
                <p>Apartamento equipado</p>
            </div>

            <div class="card">
                <div class="card-img espacio1"></div>
                <p>Espacio familiar</p>
            </div>

            <div class="card">
                <div class="card-img playa1"></div>
                <p>Casa en la playa</p>
            </div>

            <div class="card">
                <div class="card-img cama1"></div>
                <p>Doble cama</p>
            </div>
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
