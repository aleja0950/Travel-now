// Datos de las transacciones separadas por categoría
const data = {
    completed: [
        { title: "Alejandra Torres", date: "12 Marzo 2025 a las 2:00 PM", amount: "1.000.000 " },
        { title: "Juan Carlos Torres", date: "26 Abril 2021 a las 5:00 PM", amount: "5.000.000" },
    ],
    upcoming: [
        { title: "En Proceso de pago", date: "01 Dic 2025 a las 10:00 AM", amount: "600.000" }
    ],
    gross: [
        { title: "Ganancias", date: "Año 2025", amount: "10.000.000" }
    ]
};

// Renderiza las transacciones según el tipo seleccionado
function renderTransactions(type) {
    const container = document.getElementById("transactionContainer"); // Contenedor del HTML
    container.innerHTML = ""; // Limpia contenido previo

    // Crea cada tarjeta de transacción dinámicamente
    data[type].forEach(item => {
        container.innerHTML += `
            <div class="transaction-box">
                <div class="transaction-info">
                    <div class="title">${item.title}</div>
                    <div>${item.date}</div>
                </div>
                <div class="amount">$ ${item.amount}</div>
            </div>
        `;
    });
}

// Cambia la pestaña activa y vuelve a renderizar
function changeTab(tab) {
    document.querySelectorAll(".tabs span").forEach(el => el.classList.remove("active")); // Quitar activo
    event.target.classList.add("active"); // Poner activo en clic actual

    renderTransactions(tab); // Mostrar las transacciones del tab seleccionado
}

// Render inicial de la pestaña "completed"
renderTransactions("completed");
