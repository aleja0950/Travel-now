<?php
$errores = $errores ?? [];
$datos = $datos ?? [];
$propiedad_editar = $propiedad_editar ?? [];
$imagenes = $imagenes ?? [];
?> 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propiedades</title>
    <link rel="stylesheet" href="public/css/editroom.css">
</head>
<body>
    <header class="admin-header">
        <div class="admin-brand">Travel Now Admin</div>
        <nav class="admin-nav">
            <a href="index.php?controller=login&action=admin">Inicio</a>
            <a href="index.php?controller=propiedad&action=index">Propiedades</a>
            <a href="index.php?controller=reserva&action=history">Historial</a>
            <a href="index.php?controller=reserva&action=host">Reservas</a>
            <a href="index.php?controller=login&action=logout">Cerrar</a>
            <span><?= htmlspecialchars($_SESSION['user'] ?? '') ?></span>
            <span><?= htmlspecialchars($_SESSION['rol'] ?? '') ?></span>
        </nav>
    </header>

    <h2 class="title">Propiedades publicadas</h2>

    <?php if (isset($_GET['msg'])): ?>
        <p class="feedback-success"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif ?>

    <?php if (count($errores ?? []) > 0): ?>
        <?php foreach ($errores as $error): ?>
            <p class="feedback-danger"><?= htmlspecialchars($error) ?></p>
        <?php endforeach ?>
    <?php endif ?>

    <div class="properties-layout">
        <div>
            <?php if (count($datos) > 0): ?>
                <?php foreach ($datos as $item): ?>
                    <div class="property-card property-card-list">
                        <img class="property-img property-img-tag" src="public/css/img/<?= htmlspecialchars($item['imagen'] ?? 'habitacion1.jpg') ?>" alt="<?= htmlspecialchars($item['nombre']) ?>">
                        <div class="property-info">
                            <h4><?= htmlspecialchars($item['nombre']) ?></h4>
                            <p><?= htmlspecialchars($item['ubicacion']) ?></p>
                            <p>Habitacion: #<?= htmlspecialchars($item['Id_habitacion']) ?></p>
                            <p>Tipo: <?= htmlspecialchars($item['tipo_habitacion']) ?></p>
                            <p>Precio: $<?= number_format((int) ($item['precio'] ?? 0), 0, ',', '.') ?></p>
                            <p>Estado: <?= htmlspecialchars($item['estado'] ?? 'sin estado') ?></p>
                            <p>Capacidad: <?= htmlspecialchars((string) ($item['capacidad'] ?? 0)) ?> personas</p>
                            <p>Banos: <?= htmlspecialchars((string) ($item['banos'] ?? 0)) ?></p>
                            <p>Cupos: <?= htmlspecialchars((string) ($item['cupos'] ?? 0)) ?></p>
                            <p>Servicios: <?= htmlspecialchars($item['servicios'] ?? 'sin servicios') ?></p>
                            <p><?= htmlspecialchars($item['descripcion_texto'] ?? 'sin descripcion') ?></p>
                            <div class="buttons">
                                <a href="index.php?controller=reserva&action=index&id=<?= htmlspecialchars($item['Id_habitacion']) ?>">
                                    <button class="modify" type="button">Ver reserva</button>
                                </a>
                                <button
                                    class="modify btn-editar-propiedad"
                                    type="button"
                                    data-id="<?= htmlspecialchars($item['Id_habitacion']) ?>"
                                    data-nombre="<?= htmlspecialchars($item['nombre']) ?>"
                                    data-ubicacion="<?= htmlspecialchars($item['ubicacion']) ?>"
                                    data-tipo="<?= htmlspecialchars((string) $item['tipo_habitacion']) ?>"
                                    data-precio="<?= htmlspecialchars((string) ($item['precio'] ?? '')) ?>"
                                    data-estado="<?= htmlspecialchars($item['estado'] ?? '') ?>"
                                    data-imagen="<?= htmlspecialchars($item['imagen'] ?? 'habitacion1.jpg') ?>"
                                    data-descripcion="<?= htmlspecialchars($item['descripcion_texto'] ?? '') ?>"
                                    data-capacidad="<?= htmlspecialchars((string) ($item['capacidad'] ?? '2')) ?>"
                                    data-banos="<?= htmlspecialchars((string) ($item['banos'] ?? '1')) ?>"
                                    data-cupos="<?= htmlspecialchars((string) ($item['cupos'] ?? '2')) ?>"
                                    data-servicios="<?= htmlspecialchars($item['servicios'] ?? '') ?>"
                                >
                                    Editar
                                </button>
                                <form action="index.php?controller=propiedad&action=eliminar" method="post" class="inline-form">
                                    <input type="hidden" name="id_habitacion" value="<?= htmlspecialchars($item['Id_habitacion']) ?>">
                                    <button class="remove" type="submit">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php else: ?>
                <div class="property-card">
                    <div class="property-info">
                        <h4>No hay propiedades publicadas</h4>
                        <p>Crea la primera desde el formulario de esta misma vista.</p>
                    </div>
                </div>
            <?php endif ?>
        </div>

        <div>
            <h2 class="title" id="titulo-form-propiedad"><?= $propiedad_editar ? 'Editar propiedad' : 'Crear propiedad' ?></h2>
            <?php $imagen_preview = $propiedad_editar['imagen'] ?? 'habitacion1.jpg'; ?>
            <form
                id="form-propiedad"
                action="index.php?controller=propiedad&action=<?= $propiedad_editar ? 'actualizar' : 'guardar' ?>"
                method="post"
                enctype="multipart/form-data"
                data-action-guardar="index.php?controller=propiedad&action=guardar"
                data-action-actualizar="index.php?controller=propiedad&action=actualizar"
                data-imagen-base="<?= htmlspecialchars($imagen_preview) ?>"
            >
                <input type="hidden" id="id_habitacion" name="id_habitacion" value="<?= htmlspecialchars($propiedad_editar['Id_habitacion'] ?? '') ?>">
                <div class="preview-block">
                    <p class="preview-label">Vista previa de la imagen</p>
                    <img id="preview-imagen-propiedad" src="public/css/img/<?= htmlspecialchars($imagen_preview) ?>" alt="Vista previa" class="preview-image">
                </div>
                <label for="nombre">Nombre del hotel o lugar</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($propiedad_editar['nombre'] ?? '') ?>" required>

                <label for="ubicacion">Ubicacion</label>
                <input type="text" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($propiedad_editar['ubicacion'] ?? '') ?>" required>

                <label for="tipo_habitacion">Tipo de habitacion</label>
                <select id="tipo_habitacion" name="tipo_habitacion" required class="select-field">
                    <option value="1" <?= ((int) ($propiedad_editar['tipo_habitacion'] ?? 0) === 1) ? 'selected' : '' ?>>1 - Habitacion</option>
                    <option value="2" <?= ((int) ($propiedad_editar['tipo_habitacion'] ?? 0) === 2) ? 'selected' : '' ?>>2 - Piso</option>
                    <option value="3" <?= ((int) ($propiedad_editar['tipo_habitacion'] ?? 0) === 3) ? 'selected' : '' ?>>3 - Hotel</option>
                </select>

                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" min="1" value="<?= htmlspecialchars((string) ($propiedad_editar['precio'] ?? '')) ?>" required>

                <label for="estado">Estado</label>
                <select id="estado" name="estado" required class="select-field">
                    <option value="Disponible" <?= (($propiedad_editar['estado'] ?? '') === 'Disponible') ? 'selected' : '' ?>>Disponible</option>
                    <option value="Ocupado" <?= (($propiedad_editar['estado'] ?? '') === 'Ocupado') ? 'selected' : '' ?>>Ocupado</option>
                    <option value="Mantenimiento" <?= (($propiedad_editar['estado'] ?? '') === 'Mantenimiento') ? 'selected' : '' ?>>Mantenimiento</option>
                </select>

                <label for="imagen">Imagen</label>
                <select id="imagen" name="imagen" required class="select-field">
                    <?php foreach ($imagenes as $imagen): ?>
                        <option value="<?= htmlspecialchars($imagen) ?>" <?= (($propiedad_editar['imagen'] ?? '') === $imagen) ? 'selected' : '' ?>><?= htmlspecialchars($imagen) ?></option>
                    <?php endforeach ?>
                </select>
                <label for="imagen_archivo">O subir imagen propia</label>
                <input type="file" id="imagen_archivo" name="imagen_archivo" accept=".jpg,.jpeg,.png,.webp,.gif">
                <?php if ($propiedad_editar && !empty($propiedad_editar['imagen'])): ?>
                    <p class="info-note">Imagen actual: <?= htmlspecialchars($propiedad_editar['imagen']) ?></p>
                <?php endif ?>

                <label for="descripcion_texto">Descripcion</label>
                <textarea id="descripcion_texto" name="descripcion_texto" required><?= htmlspecialchars($propiedad_editar['descripcion_texto'] ?? '') ?></textarea>

                <label for="capacidad">Capacidad</label>
                <input type="number" id="capacidad" name="capacidad" min="1" value="<?= htmlspecialchars((string) ($propiedad_editar['capacidad'] ?? '2')) ?>" required>

                <label for="banos">Banos</label>
                <input type="number" id="banos" name="banos" min="1" value="<?= htmlspecialchars((string) ($propiedad_editar['banos'] ?? '1')) ?>" required>

                <label for="cupos">Cupos</label>
                <input type="number" id="cupos" name="cupos" min="1" value="<?= htmlspecialchars((string) ($propiedad_editar['cupos'] ?? '2')) ?>" required>

                <label for="servicios">Servicios</label>
                <input type="text" id="servicios" name="servicios" value="<?= htmlspecialchars($propiedad_editar['servicios'] ?? 'wifi, tv, bano privado') ?>" required>

                <p class="info-note">Puedes elegir una imagen existente o subir una nueva. Si subes archivo, esa imagen tendra prioridad.</p>
                 <a href="index.php?controller=map&action=index">Poner ubicacion en el mapa</a><br>
                <button class="next-btn" id="submit-propiedad" type="submit"><?= $propiedad_editar ? 'Actualizar propiedad' : 'Guardar propiedad' ?></button>
                <a id="cancelar-edicion-propiedad" href="index.php?controller=propiedad&action=index" class="cancel-edit-link <?= $propiedad_editar ? '' : 'is-hidden' ?>">Cancelar edicion</a>
            </form>
        </div>
    </div>
    <script>
        (function () {
            const form = document.getElementById('form-propiedad');
            const titulo = document.getElementById('titulo-form-propiedad');
            const preview = document.getElementById('preview-imagen-propiedad');
            const selectImagen = document.getElementById('imagen');
            const inputArchivo = document.getElementById('imagen_archivo');
            const inputId = document.getElementById('id_habitacion');
            const botonSubmit = document.getElementById('submit-propiedad');
            const cancelarEdicion = document.getElementById('cancelar-edicion-propiedad');
            const botonesEditar = document.querySelectorAll('.btn-editar-propiedad');
            const defaults = {
                id: inputId ? inputId.value : '',
                nombre: document.getElementById('nombre') ? document.getElementById('nombre').value : '',
                ubicacion: document.getElementById('ubicacion') ? document.getElementById('ubicacion').value : '',
                tipo: document.getElementById('tipo_habitacion') ? document.getElementById('tipo_habitacion').value : '1',
                precio: document.getElementById('precio') ? document.getElementById('precio').value : '',
                estado: document.getElementById('estado') ? document.getElementById('estado').value : 'Disponible',
                imagen: selectImagen ? selectImagen.value : (form ? form.dataset.imagenBase : 'habitacion1.jpg'),
                descripcion: document.getElementById('descripcion_texto') ? document.getElementById('descripcion_texto').value : '',
                capacidad: document.getElementById('capacidad') ? document.getElementById('capacidad').value : '2',
                banos: document.getElementById('banos') ? document.getElementById('banos').value : '1',
                cupos: document.getElementById('cupos') ? document.getElementById('cupos').value : '2',
                servicios: document.getElementById('servicios') ? document.getElementById('servicios').value : 'wifi, tv, bano privado'
            };

            if (!preview || !selectImagen || !inputArchivo || !form) {
                return;
            }

            function setPreviewFromImage(nombreImagen) {
                preview.src = 'public/css/img/' + nombreImagen;
            }

            function actualizarDesdeSelect() {
                if (!inputArchivo.files || inputArchivo.files.length === 0) {
                    setPreviewFromImage(selectImagen.value);
                }
            }

            function actualizarDesdeArchivo() {
                const archivo = inputArchivo.files && inputArchivo.files[0];

                if (!archivo) {
                    setPreviewFromImage(selectImagen.value);
                    return;
                }

                const lector = new FileReader();
                lector.onload = function (evento) {
                    preview.src = evento.target.result;
                };
                lector.readAsDataURL(archivo);
            }

            function cargarModoCrear() {
                form.action = form.dataset.actionGuardar;
                if (titulo) {
                    titulo.textContent = 'Crear propiedad';
                }
                if (botonSubmit) {
                    botonSubmit.textContent = 'Guardar propiedad';
                }
                if (cancelarEdicion) {
                    cancelarEdicion.style.display = 'none';
                }
                if (inputId) inputId.value = defaults.id;
                document.getElementById('nombre').value = defaults.nombre;
                document.getElementById('ubicacion').value = defaults.ubicacion;
                document.getElementById('tipo_habitacion').value = defaults.tipo;
                document.getElementById('precio').value = defaults.precio;
                document.getElementById('estado').value = defaults.estado;
                selectImagen.value = defaults.imagen;
                document.getElementById('descripcion_texto').value = defaults.descripcion;
                document.getElementById('capacidad').value = defaults.capacidad;
                document.getElementById('banos').value = defaults.banos;
                document.getElementById('cupos').value = defaults.cupos;
                document.getElementById('servicios').value = defaults.servicios;
                inputArchivo.value = '';
                setPreviewFromImage(defaults.imagen);
            }

            function cargarModoEdicion(dataset) {
                form.action = form.dataset.actionActualizar;
                if (titulo) {
                    titulo.textContent = 'Editar propiedad';
                }
                if (botonSubmit) {
                    botonSubmit.textContent = 'Actualizar propiedad';
                }
                if (cancelarEdicion) {
                    cancelarEdicion.style.display = 'inline-block';
                }
                if (inputId) inputId.value = dataset.id || '';
                document.getElementById('nombre').value = dataset.nombre || '';
                document.getElementById('ubicacion').value = dataset.ubicacion || '';
                document.getElementById('tipo_habitacion').value = dataset.tipo || '1';
                document.getElementById('precio').value = dataset.precio || '';
                document.getElementById('estado').value = dataset.estado || 'Disponible';
                selectImagen.value = dataset.imagen || 'habitacion1.jpg';
                document.getElementById('descripcion_texto').value = dataset.descripcion || '';
                document.getElementById('capacidad').value = dataset.capacidad || '2';
                document.getElementById('banos').value = dataset.banos || '1';
                document.getElementById('cupos').value = dataset.cupos || '2';
                document.getElementById('servicios').value = dataset.servicios || '';
                inputArchivo.value = '';
                setPreviewFromImage(selectImagen.value);
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            selectImagen.addEventListener('change', actualizarDesdeSelect);
            inputArchivo.addEventListener('change', actualizarDesdeArchivo);
            botonesEditar.forEach(function (boton) {
                boton.addEventListener('click', function () {
                    cargarModoEdicion(boton.dataset);
                });
            });
            if (cancelarEdicion) {
                cancelarEdicion.addEventListener('click', function (evento) {
                    evento.preventDefault();
                    cargarModoCrear();
                });
            }
        })();
    </script>
</body>
</html>
