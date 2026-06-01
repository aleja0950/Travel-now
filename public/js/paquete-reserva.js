(function () {
    const form = document.getElementById('form-reserva-paquete');
    if (!form) {
        return;
    }

    const precioPaquete = Number(form.dataset.precioPaquete || 0);
    const totalPaquete = document.getElementById('total-paquete');
    const totalExtras = document.getElementById('total-extras');
    const totalGeneral = document.getElementById('total-general');
    const checkboxes = form.querySelectorAll('.extra-checkbox');

    function formatear(valor) {
        return '$' + Number(valor).toLocaleString('es-CO');
    }

    function actualizarTotal() {
        let extras = 0;

        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                extras += Number(checkbox.dataset.precio || 0);
            }
        });

        const total = precioPaquete + extras;

        if (totalPaquete) {
            totalPaquete.textContent = formatear(precioPaquete);
        }

        if (totalExtras) {
            totalExtras.textContent = 'Extras: ' + formatear(extras);
        }

        if (totalGeneral) {
            totalGeneral.textContent = 'Total: ' + formatear(total);
        }
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', actualizarTotal);
    });

    actualizarTotal();
})();
