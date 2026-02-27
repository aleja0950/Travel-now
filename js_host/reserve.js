// =======================
// Datos de las reservaciones
// =======================
const data = {
    // Reservas próximas
    upcoming: [
        {
            title: "Apartamento Totalmente Amoblado",
            date: "Entrada: 12 Mar 2021",
            duration: "Largo (2 - 5 Años)",
            guests: "4 Adultos",
            by: "Paris Hotel",
            img: "habitacion1.jpg"
        },
        {
            title: "Doble Apartamento con 3 Habitaciones",
            date: "Entrada: 12 Nov 2023",
            duration: "Largo (2 - 5 Años)",
            guests: "4 Adultos",
            by: " Cartagena hostal",
            img: "habitacion2.png"
        }
    ],

    // Reservas pasadas
    past: [
        {
            title: "Suite Familiar",
            date: "Entrada: 3 Ene 2022",
            duration: "Corto (1 Mes)",
            guests: "2 Adultos",
            by: "Medellin apartament",
            img: "hotel1.jpg"
        }
    ],

    // Reservas rechazadas
    rejected: [
        {
            title: "Loft Moderno",
            date: "Entrada: 20 Jun 2023",
            duration: "Mediano (6 Meses)",
            guests: "3 Adultos",
            by: "Hotel Las Américas Torre del Mar",
            img: "piso3.jpg"
        }
    ]
};

// =======================
// Función para cambiar de pestaña
// =======================
function showTab(tab) {
    // Quitar clase activa en todas las pestañas
    document.querySelectorAll(".tabs span").forEach(el => el.classList.remove("active"));
    
    // Activar solo la pestaña seleccionada
    if (tab === "upcoming") document.querySelectorAll(".tabs span")[0].classList.add("active");
    if (tab === "past") document.querySelectorAll(".tabs span")[1].classList.add("active");
    if (tab === "rejected") document.querySelectorAll(".tabs span")[2].classList.add("active");

    // Cargar contenido según pestaña
    loadReservations(tab);
}

// =======================
// Función que genera y muestra las reservas
// =======================
function loadReservations(type) {
    const container = document.getElementById("reservationContainer"); // contenedor HTML
    container.innerHTML = ""; // limpiar el contenido anterior

    // Crear cada tarjeta de reserva
    data[type].forEach((item, index) => {
        container.innerHTML += `
            <div class="reservation">
                <!-- Imagen de la habitación -->
                <div class="img-box" style="background-image: url('${item.img}')"></div>

                <!-- Información de la reserva -->
                <div class="info">
                    <div class="title">${item.title}</div>
                    <div>${item.date} &nbsp;&nbsp; Duración: ${item.duration} &nbsp;&nbsp; Huéspedes: ${item.guests}</div>
                    <div>Por: ${item.by}</div>
                </div>

                <!-- Botones de acción -->
                <div class="buttons">
                    <button class="btn approve" onclick="approve()">Aprobar</button>
                    <button class="btn reject" onclick="reject()">Rechazar</button>
                </div>
            </div>
        `;
    });
}

// =======================
// Funciones de respuesta a botones
// =======================
function approve() {
    alert("Reservación aprobada ");
}

function reject() {
    alert("Reservación rechazada ");
}

// =======================
// Mostrar por defecto la pestaña "upcoming"
// =======================
loadReservations("upcoming");
