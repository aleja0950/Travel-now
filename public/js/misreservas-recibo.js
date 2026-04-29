(function () {
    const botones = document.querySelectorAll('[data-receipt-id]');

    if (!botones.length) {
        return;
    }

    function construirBaseHref() {
        const path = window.location.pathname.replace(/[^/]*$/, '');
        return window.location.origin + path;
    }

    function abrirReciboImprimible(receiptId, titulo) {
        const recibo = document.getElementById(receiptId);

        if (!recibo) {
            return;
        }

        const baseHref = construirBaseHref();
        const ventana = window.open('', '_blank', 'width=900,height=700');

        if (!ventana) {
            return;
        }

        const contenido = recibo.outerHTML;

        ventana.document.open();
        ventana.document.write(`
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="${baseHref}">
    <title>${titulo}</title>
    <link rel="stylesheet" href="user.css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            margin: 0;
            padding: 24px;
        }

        .print-wrapper {
            max-width: 760px;
            margin: 0 auto;
        }

        .receipt-actions {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="print-wrapper">
        ${contenido}
    </div>
</body>
</html>`);
        ventana.document.close();
        ventana.focus();
        setTimeout(function () {
            ventana.print();
        }, 250);
    }

    botones.forEach(function (boton) {
        boton.addEventListener('click', function () {
            abrirReciboImprimible(boton.dataset.receiptId, boton.dataset.receiptTitle || 'Recibo de pago');
        });
    });
})();
