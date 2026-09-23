<?php
require_once 'config.php';
require_once 'includes/definiciones.php';
require_login();

$tablas = definiciones_tablas();

if (!isset($_GET['tabla']) || !array_key_exists($_GET['tabla'], $tablas)) {
    redirect('index.php');
}

$tabla = $_GET['tabla'];
$def = $tablas[$tabla];
$pk = $def['pk'];
$destino = $def['pagina'];

/**
 * ACCION: eliminar (via GET)
 */
if (($_GET['accion'] ?? '') === 'eliminar') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM `$tabla` WHERE `$pk` = ?");
            $stmt->execute([$id]);
            flash($def['titulo'] . ' eliminado correctamente.');
        } catch (PDOException $ex) {
            flash('No se pudo eliminar: existen registros relacionados que dependen de este elemento.', 'error');
        }
    }
    redirect($destino);
}

/**
 * ACCION: ocultar / mostrar (toggle de activo, via GET)
 */
if (($_GET['accion'] ?? '') === 'toggle') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $act = $pdo->prepare("SELECT activo FROM `$tabla` WHERE `$pk` = ?");
            $act->execute([$id]);
            $actual = (int) $act->fetchColumn();
            $nuevo = $actual ? 0 : 1;
            $pdo->prepare("UPDATE `$tabla` SET `activo` = ? WHERE `$pk` = ?")->execute([$nuevo, $id]);
            flash(($nuevo ? 'Mostrado' : 'Ocultado') . ' en la web.');
        } catch (PDOException $ex) {
            flash('No se pudo cambiar la visibilidad.', 'error');
        }
    }
    redirect($destino);
}

/**
 * ACCION: crear / editar (via POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if (!in_array($accion, ['crear', 'editar'], true)) {
        redirect($destino);
    }

    $datos = [];
    $errores = [];

    // Archivos subidos desde el PC: se guardan en la carpeta correcta del sitio
    // (assets/images para fotos, pdfs para PDF) y su ruta pasa a ser el valor del campo.
    foreach ($def['campos'] as $campo => $c) {
        $nArchivo = 'archivo_' . $campo;
        if (empty($_FILES[$nArchivo])) {
            continue;
        }
        $subido = $_FILES[$nArchivo];
        if ((int) $subido['error'] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ((int) $subido['error'] !== UPLOAD_ERR_OK) {
            $errores[] = 'No se pudo subir el archivo del campo "' . $c['label'] . '" (código ' . (int) $subido['error'] . ').';
            continue;
        }
        $ruta = manejar_subida($subido, $campo, (int) ($_POST['peso_' . $campo] ?? 0));
        if ($ruta === false) {
            $errores[] = 'El archivo del campo "' . $c['label'] . '" no es válido (usa imágenes JPG/PNG/GIF/WebP, PDF, o video/audio MP4-WebM-MP3-M4A-OGG, máx. 15 MB).';
            continue;
        }
        $_POST[$campo] = $ruta;
    }

    foreach ($def['campos'] as $campo => $c) {
        if ($campo === 'password') {
            // Contrasena: hash SHA-256 equivalente a SHA2('...',256) del script SQL.
            if ($accion === 'crear') {
                $pass = (string) ($_POST['password'] ?? '');
                if ($pass === '') {
                    $errores[] = 'La contrasena es obligatoria.';
                    continue;
                }
                $datos['password_hash'] = hash('sha256', $pass);
            } else {
                $pass = (string) ($_POST['password'] ?? '');
                if ($pass !== '') {
                    $datos['password_hash'] = hash('sha256', $pass);
                }
            }
            continue;
        }

        if ($c['tipo'] === 'checkbox') {
            $datos[$campo] = isset($_POST[$campo]) ? 1 : 0;
            continue;
        }

        $valor = trim((string) ($_POST[$campo] ?? ''));

        if ($c['req'] && $valor === '') {
            $errores[] = 'El campo "' . $c['label'] . '" es obligatorio.';
            continue;
        }

        if ($valor === '') {
            $datos[$campo] = null;
        } elseif ($c['tipo'] === 'number') {
            $datos[$campo] = (int) $valor;
        } else {
            $datos[$campo] = $valor;
        }
    }

    if (!empty($errores)) {
        $_SESSION['form_errores'] = $errores;
        $_SESSION['form_old'] = $_POST;
        $qs = ($accion === 'editar' && $id > 0)
            ? '&view=form&id=' . $id
            : (($accion === 'editar') ? '&view=form&id=' . $id : '&view=form');
        redirect($destino . '?tabla_error=1' . $qs);
    }

    // El boton "Guardar borrador" fuerza el estado a borrador.
    if (!empty($_POST['guardar_borrador']) && array_key_exists('estado', $datos)) {
        $datos['estado'] = 'borrador';
    }

    try {
        if ($accion === 'crear') {
            $cols = array_keys($datos);
            $sql = "INSERT INTO `$tabla` (`" . implode('`,`', $cols) . '`) VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')';
            $pdo->prepare($sql)->execute(array_values($datos));
            flash($def['titulo'] . ' creado correctamente.');
        } else {
            if ($id <= 0) {
                flash('Registro no valido.', 'error');
                redirect($destino);
            }
            if (empty($datos)) {
                flash('No hay cambios que guardar.', 'error');
                redirect($destino);
            }
            $sets = [];
            foreach (array_keys($datos) as $col) {
                $sets[] = "`$col` = ?";
            }
            $sql = "UPDATE `$tabla` SET " . implode(', ', $sets) . " WHERE `$pk` = ?";
            $pdo->prepare($sql)->execute(array_merge(array_values($datos), [$id]));
            flash($def['titulo'] . ' actualizado correctamente.');
        }
    } catch (PDOException $ex) {
        flash('Error al guardar: ' . htmlspecialchars($ex->getMessage(), ENT_QUOTES, 'UTF-8'), 'error');
    }

    redirect($destino);
}

redirect($destino);