(function () {
    const modal = document.getElementById('modal-reserva');
    const abrir = document.getElementById('abrir-modal-reserva');
    const cerrar = document.getElementById('cerrar-modal-reserva');
    const zonasAbrir = document.querySelectorAll('.modal-launch-area');
    const formReserva = document.getElementById('form-reserva-modal');
    const inputIngreso = document.getElementById('fecha_ingreso');
    const inputSalida = document.getElementById('fecha_salida');
    const inputMetodoPago = document.getElementById('metodo_pago');
    const cajaError = document.getElementById('modal-error-fechas');
    const estadiaTexto = document.getElementById('modal-estadia');
    const totalPrecioTexto = document.getElementById('modal-total-precio');
    const totalMetodoTexto = document.getElementById('modal-total-metodo');
    const metodoPagoNote = document.getElementById('modal-metodo-pago-note');

    if (!modal || !abrir || !cerrar) {
        return;
    }

    const precioBase = Number(modal.dataset.precioBase || 0);
    const reservasOcupadas = JSON.parse(modal.dataset.reservasOcupadas || '[]');
    const abrirConErrores = modal.dataset.abrirErrores === '1';

    function abrirModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function cerrarModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function mostrarError(mensaje) {
        if (!cajaError) {
            return;
        }

        if (!mensaje) {
            cajaError.textContent = '';
            cajaError.classList.remove('active');
            return;
        }

        cajaError.textContent = mensaje;
        cajaError.classList.add('active');
    }

    function actualizarTotalEstimado() {
        if (!inputIngreso || !inputSalida || !estadiaTexto || !totalPrecioTexto) {
            return;
        }

        if (!inputIngreso.value || !inputSalida.value) {
            estadiaTexto.textContent = 'Selecciona fechas para calcular tu estadia.';
            totalPrecioTexto.textContent = '$ ' + precioBase.toLocaleString('es-CO') + ' por rango estimado';
            return;
        }

        const ingreso = new Date(inputIngreso.value);
        const salida = new Date(inputSalida.value);

        if (Number.isNaN(ingreso.getTime()) || Number.isNaN(salida.getTime()) || salida <= ingreso) {
            estadiaTexto.textContent = 'Ingresa un rango valido para calcular el total.';
            totalPrecioTexto.textContent = '$ 0';
            return;
        }

        const diferenciaMs = salida.getTime() - ingreso.getTime();
        const noches = Math.max(1, Math.ceil(diferenciaMs / 86400000));
        const total = noches * precioBase;
        estadiaTexto.textContent = 'Estadia estimada: ' + noches + (noches === 1 ? ' noche.' : ' noches.');
        totalPrecioTexto.textContent = 'Total estimado: $ ' + total.toLocaleString('es-CO');
    }

    function obtenerMensajeValidacion() {
        if (!inputIngreso.value || !inputSalida.value) {
            return 'Debes completar la fecha de ingreso y la fecha de salida.';
        }

        const ingreso = new Date(inputIngreso.value);
        const salida = new Date(inputSalida.value);

        if (Number.isNaN(ingreso.getTime()) || Number.isNaN(salida.getTime())) {
            return 'Debes ingresar fechas validas para continuar.';
        }

        if (salida <= ingreso) {
            return 'La fecha de salida debe ser posterior a la fecha de ingreso.';
        }

        const cruce = reservasOcupadas.find(function (reserva) {
            const inicioOcupado = new Date(reserva.ingreso);
            const finOcupado = new Date(reserva.salida);
            return ingreso < finOcupado && salida > inicioOcupado;
        });

        if (cruce) {
            return 'El rango seleccionado se cruza con una fecha ocupada de esta habitacion.';
        }

        return '';
    }

    function actualizarValidacionVisual() {
        const mensaje = obtenerMensajeValidacion();

        if (!inputIngreso.value && !inputSalida.value) {
            mostrarError('');
            return;
        }

        mostrarError(mensaje);
    }

    function actualizarNotaMetodoPago() {
        if (!inputMetodoPago || !metodoPagoNote) {
            return;
        }

        if (inputMetodoPago.value === 'transferencia') {
            metodoPagoNote.textContent = 'Metodo seleccionado: transferencia. Se registrara como pago inicial para verificacion del host.';
            if (totalMetodoTexto) {
                totalMetodoTexto.textContent = 'Metodo de pago: Transferencia';
            }
            return;
        }

        if (inputMetodoPago.value === 'efectivo') {
            metodoPagoNote.textContent = 'Metodo seleccionado: efectivo. El host vera este metodo desde que la reserva quede registrada.';
            if (totalMetodoTexto) {
                totalMetodoTexto.textContent = 'Metodo de pago: Efectivo';
            }
            return;
        }

        if (inputMetodoPago.value === 'tarjeta') {
            metodoPagoNote.textContent = 'Metodo seleccionado: tarjeta. Se usara para registrar el pago inicial de tu reserva.';
            if (totalMetodoTexto) {
                totalMetodoTexto.textContent = 'Metodo de pago: Tarjeta';
            }
            return;
        }

        metodoPagoNote.textContent = 'Metodo seleccionado: se usara para registrar el pago inicial de tu reserva.';
        if (totalMetodoTexto) {
            totalMetodoTexto.textContent = 'Metodo de pago: Por definir';
        }
    }

    abrir.addEventListener('click', abrirModal);
    cerrar.addEventListener('click', cerrarModal);
    zonasAbrir.forEach(function (zona) {
        zona.addEventListener('click', abrirModal);
        zona.addEventListener('keydown', function (evento) {
            if (evento.key === 'Enter' || evento.key === ' ') {
                evento.preventDefault();
                abrirModal();
            }
        });
    });

    modal.addEventListener('click', function (evento) {
        if (evento.target === modal) {
            cerrarModal();
        }
    });

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape' && modal.classList.contains('active')) {
            cerrarModal();
        }
    });

    if (inputIngreso) {
        inputIngreso.addEventListener('change', function () {
            actualizarTotalEstimado();
            actualizarValidacionVisual();
        });
    }

    if (inputSalida) {
        inputSalida.addEventListener('change', function () {
            actualizarTotalEstimado();
            actualizarValidacionVisual();
        });
    }

    if (inputMetodoPago) {
        inputMetodoPago.addEventListener('change', actualizarNotaMetodoPago);
    }

    if (formReserva && inputIngreso && inputSalida) {
        formReserva.addEventListener('submit', function (evento) {
            const mensaje = obtenerMensajeValidacion();
            mostrarError(mensaje);

            if (mensaje) {
                evento.preventDefault();
            }
        });
    }

    actualizarTotalEstimado();
    actualizarNotaMetodoPago();

    if (abrirConErrores) {
        abrirModal();
    }
})();
