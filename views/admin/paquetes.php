<?php
$errores = $errores ?? [];
$datos = $datos ?? [];
$servicios = $servicios ?? [];
$habitaciones = $habitaciones ?? [];
$paquete_editar = $paquete_editar ?? null;
$componentes_editar = $componentes_editar ?? [];
$editando = is_array($paquete_editar) && !empty($paquete_editar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paquetes turisticos</title>
    <link rel="stylesheet" href="public/css/editroom.css">
    <link rel="stylesheet" href="public/css/paquetes.css">
    <link rel="stylesheet" href="public/css/dashboard.css">
</head>
<body class="dashboard-body">
    <?php $nav_activo = 'paquetes'; require_once __DIR__ . '/_nav.php'; ?>

    <div class="paquetes-layout">
        <h2 class="title">Paquetes turisticos</h2>

        <?php if (isset($_GET['msg'])): ?>
            <p class="feedback-success"><?= htmlspecialchars($_GET['msg']) ?></p>
        <?php endif ?>

        <form class="paquete-admin-form" action="index.php?controller=paquete&action=guardar" method="post" enctype="multipart/form-data">
            <?php if ($editando): ?>
                <input type="hidden" name="id_paquete" value="<?= (int) $paquete_editar['Id_paquete'] ?>">
            <?php endif ?>

            <h3><?= $editando ? 'Editar paquete' : 'Registrar paquete' ?></h3>

            <label>Nombre</label>
            <input type="text" name="nombre" required value="<?= htmlspecialchars($paquete_editar['nombre'] ?? '') ?>">

            <label>Descripcion</label>
            <textarea name="descripcion" rows="4"><?= htmlspecialchars($paquete_editar['descripcion'] ?? '') ?></textarea>

            <label>Precio base del paquete (COP)</label>
            <input type="number" name="precio_base" min="0" value="<?= (int) ($paquete_editar['precio_base'] ?? 0) ?>">
            <small>Si dejas 0, el sistema suma el precio de cada componente.</small>

            <label>Dias del paquete</label>
            <input type="number" name="dias" min="1" value="<?= (int) ($paquete_editar['dias'] ?? 1) ?>">

            <label>Estado</label>
            <select name="estado">
                <option value="activo" <?= (($paquete_editar['estado'] ?? 'activo') === 'activo') ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= (($paquete_editar['estado'] ?? '') === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
            </select>

            <label>Imagen (nombre o subir archivo)</label>
            <input type="text" name="imagen" value="<?= htmlspecialchars($paquete_editar['imagen'] ?? 'paquete.jpg') ?>">
            <input type="file" name="imagen_archivo" accept="image/*">

            <h4>Componentes (habitacion, tour u otro)</h4>
            <div id="componentes-container">
                <?php if (count($componentes_editar) > 0): ?>
                    <?php foreach ($componentes_editar as $componente): ?>
                        <div class="componente-row">
                            <div>
                                <label>Tipo</label>
                                <select name="componente_tipo[]">
                                    <option value="habitacion" <?= (($componente['tipo'] ?? '') === 'habitacion') ? 'selected' : '' ?>>Habitacion</option>
                                    <option value="tour" <?= (($componente['tipo'] ?? '') === 'tour') ? 'selected' : '' ?>>Tour</option>
                                    <option value="otro" <?= (($componente['tipo'] ?? '') === 'otro') ? 'selected' : '' ?>>Otro</option>
                                </select>
                            </div>
                            <div>
                                <label>Titulo</label>
                                <input type="text" name="componente_titulo[]" value="<?= htmlspecialchars($componente['titulo'] ?? '') ?>">
                            </div>
                            <div>
                                <label>Descripcion</label>
                                <input type="text" name="componente_descripcion[]" value="<?= htmlspecialchars($componente['descripcion'] ?? '') ?>">
                            </div>
                            <div>
                                <label>Habitacion vinculada</label>
                                <select name="componente_habitacion[]">
                                    <option value="0">Ninguna</option>
                                    <?php foreach ($habitaciones as $hab): ?>
                                        <option value="<?= (int) $hab['Id_habitacion'] ?>" <?= ((int) ($componente['id_habitacion'] ?? 0) === (int) $hab['Id_habitacion']) ? 'selected' : '' ?>>
                                            #<?= (int) $hab['Id_habitacion'] ?> - <?= htmlspecialchars($hab['nombre']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div>
                                <label>Precio componente</label>
                                <input type="number" name="componente_precio[]" min="0" value="<?= (int) ($componente['precio_componente'] ?? 0) ?>">
                            </div>
                        </div>
                    <?php endforeach ?>
                <?php else: ?>
                    <div class="componente-row">
                        <div>
                            <label>Tipo</label>
                            <select name="componente_tipo[]">
                                <option value="habitacion">Habitacion</option>
                                <option value="tour">Tour</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label>Titulo</label>
                            <input type="text" name="componente_titulo[]" placeholder="Ej: Tour ciudad vieja">
                        </div>
                        <div>
                            <label>Descripcion</label>
                            <input type="text" name="componente_descripcion[]">
                        </div>
                        <div>
                            <label>Habitacion vinculada</label>
                            <select name="componente_habitacion[]">
                                <option value="0">Ninguna</option>
                                <?php foreach ($habitaciones as $hab): ?>
                                    <option value="<?= (int) $hab['Id_habitacion'] ?>">
                                        #<?= (int) $hab['Id_habitacion'] ?> - <?= htmlspecialchars($hab['nombre']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div>
                            <label>Precio componente</label>
                            <input type="number" name="componente_precio[]" min="0" value="0">
                        </div>
                    </div>
                <?php endif ?>
            </div>

            <button type="button" class="btn-secondary" id="agregar-componente">Agregar componente</button>
            <button class="btn-primary" type="submit"><?= $editando ? 'Actualizar paquete' : 'Guardar paquete' ?></button>
        </form>

        <form class="paquete-admin-form" action="index.php?controller=paquete&action=guardar_servicio" method="post">
            <h3>Servicios adicionales (extras del cliente)</h3>
            <label>Nombre del servicio</label>
            <input type="text" name="nombre_servicio" required>
            <label>Precio (COP)</label>
            <input type="number" name="precio_servicio" min="0" required>
            <label><input type="checkbox" name="activo_servicio" checked> Activo</label>
            <button class="btn-primary" type="submit">Guardar servicio adicional</button>
        </form>

        <h3>Paquetes registrados</h3>
        <div class="paquetes-grid">
            <?php foreach ($datos as $item): ?>
                <div class="paquete-card">
                    <img src="public/css/img/<?= htmlspecialchars($item['imagen'] ?? 'paquete.jpg') ?>" alt="<?= htmlspecialchars($item['nombre']) ?>">
                    <div class="paquete-card-body">
                        <h4><?= htmlspecialchars($item['nombre']) ?></h4>
                        <p><?= htmlspecialchars($item['descripcion'] ?? '') ?></p>
                        <p class="paquete-price">$<?= number_format((int) ($item['precio_base'] ?? 0), 0, ',', '.') ?></p>
                        <p><?= (int) ($item['dias'] ?? 0) ?> dias · <?= htmlspecialchars($item['estado'] ?? '') ?></p>
                        <a href="index.php?controller=paquete&action=admin&id=<?= (int) $item['Id_paquete'] ?>">
                            <button class="btn-secondary" type="button">Editar</button>
                        </a>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

        <h3 style="margin-top:32px;">Servicios adicionales activos</h3>
        <ul>
            <?php foreach ($servicios as $servicio): ?>
                <li><?= htmlspecialchars($servicio['nombre']) ?> — $<?= number_format((int) $servicio['precio'], 0, ',', '.') ?> <?= ((int) ($servicio['activo'] ?? 0) === 1) ? '' : '(inactivo)' ?></li>
            <?php endforeach ?>
        </ul>
    </div>

    <template id="tpl-componente">
        <div class="componente-row">
            <div>
                <label>Tipo</label>
                <select name="componente_tipo[]">
                    <option value="habitacion">Habitacion</option>
                    <option value="tour">Tour</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div>
                <label>Titulo</label>
                <input type="text" name="componente_titulo[]">
            </div>
            <div>
                <label>Descripcion</label>
                <input type="text" name="componente_descripcion[]">
            </div>
            <div>
                <label>Habitacion vinculada</label>
                <select name="componente_habitacion[]">
                    <option value="0">Ninguna</option>
                    <?php foreach ($habitaciones as $hab): ?>
                        <option value="<?= (int) $hab['Id_habitacion'] ?>">#<?= (int) $hab['Id_habitacion'] ?> - <?= htmlspecialchars($hab['nombre']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div>
                <label>Precio componente</label>
                <input type="number" name="componente_precio[]" min="0" value="0">
            </div>
        </div>
    </template>

    <script>
        document.getElementById('agregar-componente')?.addEventListener('click', function () {
            const tpl = document.getElementById('tpl-componente');
            const contenedor = document.getElementById('componentes-container');
            if (!tpl || !contenedor) return;
            contenedor.appendChild(tpl.content.cloneNode(true));
        });
    </script>
</body>
</html>
