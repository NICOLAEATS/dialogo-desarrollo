<?php
/**
 * Renderiza el listado y el formulario (crear/editar) de una tabla.
 * Requiere haber definido $nombre_tabla antes de incluirlo.
 *
 * Uso: $nombre_tabla = 'usuarios'; include 'includes/render_tabla.php';
 */

if (!isset($nombre_tabla)) {
    exit('Falta la variable $nombre_tabla.');
}

$tablas = definiciones_tablas();
$def = $tablas[$nombre_tabla];
$pk = $def['pk'];
$page = $nombre_tabla;
$title = ucfirst($def['titulo']);

$titulosPagina = [
    'usuarios' => 'Gestion de Usuarios',
    'autores' => 'Gestion de Autores',
    'reportajes' => 'Gestion de Reportajes',
    'reportajes_fotos' => 'Fotos de Reportajes',
    'noticias' => 'Gestion de Noticias',
    'boletines' => 'Gestion de Boletines',
    'podcasts' => 'Gestion de Podcasts',
    'videos' => 'Gestion de Videos',
    'pdfs' => 'Gestion de Documentos PDF',
    'fotos' => 'Gestion de Fotos',
];
$pageTitle = $titulosPagina[$nombre_tabla] ?? $title;

// Errores de validacion pendientes (regresados por crud.php)
$formErrores = $_SESSION['form_errores'] ?? null;
$formOld = $_SESSION['form_old'] ?? null;

include 'includes/layout_head.php';
include 'includes/sidebar.php';
include 'includes/header.php';

$esForm = ($_GET['view'] ?? '') === 'form';
?>

<main class="relative flex-1 overflow-y-auto overflow-x-hidden" style="min-height:0">
  <div class="p-5 sm:p-8 lg:py-6">

    <?php if ($esForm) : ?>
    <?php
        $id = (int) ($_GET['id'] ?? 0);
        $registro = null;
        $accionForm = 'crear';

        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT * FROM `$nombre_tabla` WHERE `$pk` = ?");
            $stmt->execute([$id]);
            $registro = $stmt->fetch();
            if ($registro) {
                $accionForm = 'editar';
            }
        }
    ?>
    <!-- ===== Formulario ===== -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">
          <?= $accionForm === 'editar' ? 'Editar ' . $def['titulo'] : 'Nuevo ' . $def['titulo'] ?>
        </h1>
        <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
          Tabla <b><?= e($nombre_tabla) ?></b> de la base de datos dialogoydesarrollo.
        </p>
      </div>
      <a href="<?= e($def['pagina']) ?>" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
        <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M9.84825 21.75C9.86825 21.75 9.88914 21.7481 9.90925 21.7443C10.4028 21.6567 10.7326 21.1875 10.6445 20.6939L9.02698 11.6631L5.61021 13.915C5.54206 13.9612 5.46331 13.9879 5.38199 13.9922C5.24396 14 5.10902 13.9507 5.00869 13.8561L4.1961 13.0903C3.3589 12.3034 3.39831 10.9581 4.27982 10.2203L11.8036 3.97732C12.8446 3.10342 14.4485 3.73372 14.6258 5.07893L15.9409 14.8057C16.0054 15.3006 15.6616 15.7543 15.1686 15.8224L14.291 15.9378C13.7757 15.9993 13.1825 15.7832 12.8953 15.366L10.9379 12.5637L12.2752 20.244C12.3628 20.7345 12.0337 21.2015 11.5432 21.2919L9.94736 21.614C9.91546 21.6201 9.88071 21.6231 9.84818 21.6231L9.84825 21.75Z" fill="" />
        </svg>
        Volver al listado
      </a>
    </div>

    <?php if ($accionForm === 'editar' && !$registro) : ?>
      <div class="rounded-2xl border border-error-200 bg-error-50 p-5 dark:border-error-500/30 dark:bg-error-500/10">
        <p class="text-theme-sm text-error-700 dark:text-error-400">El registro no existe o ya fue eliminado.</p>
      </div>
    <?php else : ?>

    <?php if (!empty($formErrores)) : ?>
      <div class="mb-5 rounded-xl border border-error-200 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/10">
        <p class="mb-2 text-theme-sm font-semibold text-error-700 dark:text-error-400">Corrige los siguientes campos:</p>
        <ul class="list-inside list-disc space-y-1">
          <?php foreach ($formErrores as $err) : ?>
            <li class="text-theme-sm text-error-700 dark:text-error-400"><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; unset($formErrores, $formOld, $_SESSION['form_errores'], $_SESSION['form_old']); ?>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-7">
      <form method="post" enctype="multipart/form-data" action="crud.php?tabla=<?= e($nombre_tabla) ?>&accion=<?= e($accionForm) ?>" data-autosave="1" data-tabla="<?= e($nombre_tabla) ?>" data-id="<?= (int) ($registro[$pk] ?? 0) ?>" data-pk="<?= e($pk) ?>">
        <input type="hidden" name="accion" value="<?= e($accionForm) ?>" />
        <?php if ($accionForm === 'editar') : ?>
          <input type="hidden" name="id" value="<?= (int) $registro[$pk] ?>" />
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <?php foreach ($def['campos'] as $campo => $c) : ?>
            <?php
                $valor = '';
                if ($campo === 'password' && $accionForm === 'editar') {
                    // no se muestra la contrasena actual
                } elseif (isset($_SESSION['form_old'][$campo])) {
                    $valor = $_SESSION['form_old'][$campo];
                } elseif ($registro) {
                    $valor = $registro[$campo] ?? '';
                }
            ?>

            <?php if ($c['tipo'] === 'checkbox') : ?>
            <div class="md:col-span-1">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                <?= e($c['label']) ?>
              </label>
              <div x-data="{ checkboxToggle: <?= ((int) $valor === 1) ? 'true' : 'false' ?> }">
                <label class="flex items-center text-sm font-normal text-gray-700 cursor-pointer select-none dark:text-gray-400">
                  <div class="relative">
                    <input type="checkbox" name="<?= e($campo) ?>" value="1" class="sr-only" @change="checkboxToggle = !checkboxToggle" <?= ((int) $valor === 1) ? 'checked' : '' ?> />
                    <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'" class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                      <span :class="checkboxToggle ? '' : 'opacity-0'">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                      </span>
                    </div>
                  </div>
                  <span><?= e($c['label']) ?></span>
                </label>
              </div>
            </div>
            <?php elseif ($c['tipo'] === 'select') : ?>
            <div class="md:col-span-1">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                <?= e($c['label']) ?><?= !empty($c['req']) ? '<span class="text-error-500">*</span>' : '' ?>
              </label>
              <select
                name="<?= e($campo) ?>" <?= !empty($c['req']) ? 'required' : '' ?>
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
              >
                <option value="">— Seleccionar —</option>
                <?php if (isset($c['fuente'])) : ?>
                  <?php foreach (opciones_select($c['fuente']) as $val => $txt) : ?>
                    <option value="<?= e($val) ?>" <?= ($valor !== '' && (string) $valor === (string) $val) ? 'selected' : '' ?>><?= e($txt) ?></option>
                  <?php endforeach; ?>
                <?php else : ?>
                  <?php foreach ($c['opciones'] as $val => $txt) : ?>
                    <option value="<?= e($val) ?>" <?= ($valor !== '' && (string) $valor === (string) $val) ? 'selected' : '' ?>><?= e($txt) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <?php elseif ($c['tipo'] === 'textarea') : ?>
            <div class="md:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                <?= e($c['label']) ?><?= !empty($c['req']) ? '<span class="text-error-500">*</span>' : '' ?>
              </label>
              <?php if ($campo === 'desarrollo') : ?>
                <!-- Editor de texto tipo Word (CKEditor) para el cuerpo del reportaje -->
                <textarea
                  name="desarrollo"
                  id="wysiwyg_hidden_desarrollo"
                  rows="14"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                ><?= $valor ?></textarea>
                <p class="mt-1 text-theme-xs text-gray-400">Editor tipo Word: negrita, cursiva, subrayado, tamaños/colores de fuente, listas, tablas, enlaces, imágenes y más. Todo se reproduce en la página principal.</p>
              <?php else : ?>
              <textarea
                name="<?= e($campo) ?>" <?= !empty($c['req']) ? 'required' : '' ?>
                rows="4"
                class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              ><?= e($valor) ?></textarea>
              <?php endif; ?>
            </div>
            <?php else : ?>
            <?php
                $esPreviewMedia = ($campo === 'archivo_url'); // video/audio local (podcasts/videos)
                $esPreviewImg = (bool) preg_match('/(^|_)(foto|imagen|avatar|icono)(_|$)/i', $campo) || in_array($campo, ['url_foto', 'url_imagen', 'portada_url', 'url_portada'], true);
                $esPreviewPdf = (!$esPreviewMedia && (bool) preg_match('/pdf|archivo/i', $campo));
            ?>
            <div class="md:col-span-1">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                <?= e($c['label']) ?><?= !empty($c['req']) ? '<span class="text-error-500">*</span>' : '' ?>
              </label>
              <input
                type="<?= e($c['tipo']) ?>"
                name="<?= e($campo) ?>"
                value="<?= e($valor) ?>"
                <?= !empty($c['req']) ? 'required' : '' ?>
                <?= ($c['tipo'] === 'password' && $accionForm === 'editar') ? 'placeholder="Deja en blanco para mantener la actual"' : '' ?>
                <?= ($c['tipo'] === 'date' && $valor === '') ? 'placeholder="aaaa-mm-dd"' : '' ?>
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              />
              <?php if ($esPreviewMedia) : ?>
              <div class="mt-2 flex items-center gap-3">
                <video
                  id="prev_<?= e($campo) ?>"
                  src="<?= e(app_asset($valor)) ?>"
                  controls
                  preload="none"
                  class="h-24 w-40 rounded-lg border border-gray-200 bg-gray-100 object-cover dark:border-gray-800 dark:bg-gray-800"
                  style="<?= $valor ? '' : 'display:none' ?>"
                ></video>
                <span
                  id="phprev_<?= e($campo) ?>"
                  class="flex h-24 w-40 items-center justify-center rounded-lg border border-dashed border-gray-300 text-theme-xs text-gray-400 dark:border-gray-700 dark:text-gray-500"
                  style="<?= $valor ? 'display:none' : 'flex' ?>"
                >Sin archivo</span>
              </div>
              <div class="mt-2">
                <label for="archivo_<?= e($campo) ?>" class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-gray-300 px-3 py-2 text-theme-xs font-medium text-gray-600 transition hover:border-brand-500 hover:text-brand-600 dark:border-gray-700 dark:text-gray-400 dark:hover:text-brand-400">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16L12 4M12 16L7 11M12 16L17 11M4 20H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" transform="rotate(180 12 12)"/></svg>
                  Subir video/audio desde mi PC
                  <input type="file" id="archivo_<?= e($campo) ?>" name="archivo_<?= e($campo) ?>" accept="video/*,audio/*" class="hidden" />
                </label>
                <p id="fileinfo_<?= e($campo) ?>" class="mt-1 text-theme-xs text-gray-400">Formato permitido: MP4-WebM (video) o MP3-M4A-OGG (audio), máx. 15 MB. Se reproduce embebido en la página principal.</p>
              </div>
              <script>
                (function () {
                  var base = '<?= e(app_base_url()) ?>';
                  var input = document.querySelector('[name="<?= e($campo) ?>"]');
                  var vid = document.getElementById('prev_<?= e($campo) ?>');
                  var ph = document.getElementById('phprev_<?= e($campo) ?>');
                  if (!input) return;
                  input.addEventListener('input', function () {
                    var v = input.value.trim();
                    vid.style.display = v ? '' : 'none';
                    ph.style.display = v ? 'none' : 'flex';
                    if (v) vid.src = (v.indexOf('http') === 0 || v.indexOf('//') === 0) ? v : base + '/' + v.replace(/^\/+/, '');
                  });
                  var fSel = document.querySelector('[name="archivo_<?= e($campo) ?>"]');
                  if (fSel) {
                    fSel.addEventListener('change', function () {
                      var info = document.getElementById('fileinfo_<?= e($campo) ?>');
                      if (fSel.files && fSel.files[0]) {
                        var f = fSel.files[0];
                        vid.src = URL.createObjectURL(f);
                        vid.style.display = '';
                        ph.style.display = 'none';
                        if (info) info.textContent = 'Archivo: ' + f.name + ' — se subirá a la carpeta de videos al guardar.';
                      } else if (info) {
                        info.textContent = 'Ningún archivo seleccionado.';
                      }
                    });
                  }
                })();
              </script>
              <?php elseif ($esPreviewImg) : ?>
              <div class="mt-2 flex items-center gap-3">
                <img
                  id="prev_<?= e($campo) ?>"
                  src="<?= e(app_asset($valor)) ?>"
                  alt=""
                  class="h-24 w-36 rounded-lg border border-gray-200 bg-gray-100 object-cover dark:border-gray-800 dark:bg-gray-800"
                  style="<?= $valor ? '' : 'display:none' ?>"
                  onerror="this.style.display='none';document.getElementById('phprev_<?= e($campo) ?>').style.display='flex'"
                />
                <span
                  id="phprev_<?= e($campo) ?>"
                  class="flex h-24 w-36 items-center justify-center rounded-lg border border-dashed border-gray-300 text-theme-xs text-gray-400 dark:border-gray-700 dark:text-gray-500"
                  style="<?= $valor ? 'display:none' : 'flex' ?>"
                >Sin foto</span>
              </div>
              <div class="mt-2">
                <label for="archivo_<?= e($campo) ?>" class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-gray-300 px-3 py-2 text-theme-xs font-medium text-gray-600 transition hover:border-brand-500 hover:text-brand-600 dark:border-gray-700 dark:text-gray-400 dark:hover:text-brand-400">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16L12 4M12 16L7 11M12 16L17 11M4 20H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" transform="rotate(180 12 12)"/></svg>
                  Elegir foto desde mi PC
                  <input type="file" id="archivo_<?= e($campo) ?>" name="archivo_<?= e($campo) ?>" accept="image/*" class="hidden" />
                </label>
                <p id="fileinfo_<?= e($campo) ?>" class="mt-1 text-theme-xs text-gray-400">Se subirá automáticamente a la carpeta de imágenes al guardar y reemplazará la foto actual.</p>
                <label for="peso_<?= e($campo) ?>" class="mt-3 block text-theme-xs font-medium text-gray-500 dark:text-gray-400">Peso de la imagen (compresor)</label>
                <select
                  id="peso_<?= e($campo) ?>"
                  name="peso_<?= e($campo) ?>"
                  class="dark:bg-dark-900 mt-1 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-theme-xs text-gray-700 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                  <option value="800">Original (máx. 800 KB por imagen)</option>
                  <option value="400" selected>Optimizada (máx. 400 KB) — recomendado</option>
                  <option value="200">Ligera (máx. 200 KB)</option>
                  <option value="100">Mini (máx. 100 KB)</option>
                  <option value="0">Sin comprimir (peso original)</option>
                </select>
                <p class="mt-1 text-theme-xs text-gray-400">El compresor reduce automáticamente el peso de la foto al subirla para que la página cargue más rápido.</p>
              </div>
              <script>
                (function () {
                  var base = '<?= e(app_base_url()) ?>';
                  var input = document.querySelector('[name="<?= e($campo) ?>"]');
                  var img = document.getElementById('prev_<?= e($campo) ?>');
                  var ph = document.getElementById('phprev_<?= e($campo) ?>');
                  if (!input) return;
                  input.addEventListener('input', function () {
                    var v = input.value.trim();
                    img.style.display = v ? '' : 'none';
                    ph.style.display = v ? 'none' : 'flex';
                    if (v) img.src = (v.indexOf('http') === 0 || v.indexOf('//') === 0) ? v : base + '/' + v.replace(/^\/+/, '');
                  });
                  var fSel = document.querySelector('[name="archivo_<?= e($campo) ?>"]');
                  if (fSel) {
                    fSel.addEventListener('change', function () {
                      var info = document.getElementById('fileinfo_<?= e($campo) ?>');
                      if (fSel.files && fSel.files[0]) {
                        var f = fSel.files[0];
                        img.src = URL.createObjectURL(f);
                        img.style.display = '';
                        ph.style.display = 'none';
                        if (info) info.textContent = 'Archivo: ' + f.name + ' — se guardará al pulsar Guardar.';
                      } else if (info) {
                        info.textContent = 'Ningún archivo seleccionado.';
                      }
                    });
                  }
                })();
              </script>
              <?php elseif ($esPreviewPdf) : ?>
              <div class="mt-2 flex items-center gap-3">
                <a
                  id="prev_<?= e($campo) ?>"
                  href="<?= e(app_asset($valor)) ?>"
                  target="_blank"
                  class="inline-flex items-center gap-1.5 rounded-lg bg-error-50 px-3 py-2 text-theme-xs font-medium text-error-600 transition hover:bg-error-100 dark:bg-error-500/10 dark:text-error-500"
                  style="<?= $valor ? '' : 'display:none' ?>"
                >
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16L12 4M12 16L7 11M12 16L17 11M4 20H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  Ver / Descargar PDF
                </a>
                <span id="phprev_<?= e($campo) ?>" class="text-theme-xs text-gray-400 dark:text-gray-500" style="<?= $valor ? 'display:none' : '' ?>">Sin PDF</span>
              </div>
              <div class="mt-2">
                <label for="archivo_<?= e($campo) ?>" class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-gray-300 px-3 py-2 text-theme-xs font-medium text-gray-600 transition hover:border-brand-500 hover:text-brand-600 dark:border-gray-700 dark:text-gray-400 dark:hover:text-brand-400">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16L12 4M12 16L7 11M12 16L17 11M4 20H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" transform="rotate(180 12 12)"/></svg>
                  Elegir PDF desde mi PC
                  <input type="file" id="archivo_<?= e($campo) ?>" name="archivo_<?= e($campo) ?>" accept=".pdf" class="hidden" />
                </label>
                <p id="fileinfo_<?= e($campo) ?>" class="mt-1 text-theme-xs text-gray-400">Se subirá automáticamente a la carpeta de PDFs al guardar y reemplazará el PDF actual.</p>
              </div>
              <script>
                (function () {
                  var base = '<?= e(app_base_url()) ?>';
                  var input = document.querySelector('[name="<?= e($campo) ?>"]');
                  var link = document.getElementById('prev_<?= e($campo) ?>');
                  var ph = document.getElementById('phprev_<?= e($campo) ?>');
                  if (!input) return;
                  input.addEventListener('input', function () {
                    var v = input.value.trim();
                    link.style.display = v ? '' : 'none';
                    ph.style.display = v ? 'none' : '';
                    if (v) link.href = (v.indexOf('http') === 0 || v.indexOf('//') === 0) ? v : base + '/' + v.replace(/^\/+/, '');
                  });
                  var fSel = document.querySelector('[name="archivo_<?= e($campo) ?>"]');
                  if (fSel) {
                    fSel.addEventListener('change', function () {
                      var info = document.getElementById('fileinfo_<?= e($campo) ?>');
                      if (fSel.files && fSel.files[0]) {
                        if (info) info.textContent = 'Archivo: ' + fSel.files[0].name + ' — se guardará al pulsar Guardar.';
                      } else if (info) {
                        info.textContent = 'Ningún archivo seleccionado.';
                      }
                    });
                  }
                })();
              </script>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
          <button
            type="submit"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs transition-colors hover:bg-brand-600"
          >
            <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 4.75C12.4142 4.75 12.75 5.08579 12.75 5.5V11.25H18.5C18.9142 11.25 19.25 11.5858 19.25 12C19.25 12.4142 18.9142 12.75 18.5 12.75H12.75V18.5C12.75 18.9142 12.4142 19.25 12 19.25C11.5858 19.25 11.25 18.9142 11.25 18.5V12.75H5.5C5.08579 12.75 4.75 12.4142 4.75 12C4.75 11.5858 5.08579 11.25 5.5 11.25H11.25V5.5C11.25 5.08579 11.5858 4.75 12 4.75Z" fill="" />
            </svg>
            <?= $accionForm === 'editar' ? 'Guardar cambios' : 'Crear registro' ?>
          </button>
          <?php if (isset($def['campos']['estado'])) : ?>
          <button
            type="submit"
            name="guardar_borrador"
            value="1"
            class="inline-flex items-center gap-2 rounded-lg border border-warning-300 bg-warning-50 px-5 py-2.5 text-theme-sm font-medium text-warning-700 shadow-theme-xs transition-colors hover:bg-warning-100 dark:border-warning-500/30 dark:bg-warning-500/10 dark:text-warning-400"
          >
            <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 14C10.8954 14 10 14.8954 10 16C10 17.1046 10.8954 18 12 18C13.1046 18 14 17.1046 14 16C14 14.8954 13.1046 14 12 14ZM3.073 14.39L4.877 8.975C5.534 7.299 6.751 5.978 8.175 5.594L6.092 3.511L7.506 2.097L12 6.591L16.494 2.097L17.908 3.511L15.825 5.594C17.249 5.978 18.466 7.299 19.123 8.975L20.927 14.39C22.004 16.687 21.375 19.443 18.97 20.622C17.524 21.553 15.639 21.632 14.216 20.576C13.096 19.784 12 19.784 10.884 20.576C9.462 21.632 7.577 21.553 6.131 20.622C3.725 19.443 3.096 16.687 4.173 14.39" fill="currentColor"/>
            </svg>
            Guardar borrador
          </button>
          <?php endif; ?>
          <a href="<?= e($def['pagina']) ?>" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">Cancelar</a>
          <span id="autosave_status" class="ml-auto text-theme-xs font-medium text-gray-400"></span>
        </div>

        <div id="autosave_banner" class="mt-4 hidden rounded-xl border border-warning-300 bg-warning-50 px-4 py-3 dark:border-warning-500/30 dark:bg-warning-500/10">
          <p class="text-theme-sm font-medium text-warning-700 dark:text-warning-400">
            Tienes un <b>borrador autoguardado</b> de esta edición.
            <button type="button" id="autosave_restaurar" class="ml-2 rounded-lg bg-warning-500 px-3 py-1.5 text-theme-xs font-medium text-white transition hover:bg-warning-600">Restaurar</button>
            <button type="button" id="autosave_descartar" class="ml-1 rounded-lg border border-warning-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-warning-700 transition hover:bg-gray-50 dark:border-warning-500/30 dark:bg-gray-800 dark:text-warning-400">Descartar</button>
          </p>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <?php else : ?>
    <!-- ===== Listado ===== -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90"><?= e($pageTitle) ?></h1>
        <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
          Registros de la tabla <b><?= e($nombre_tabla) ?></b>.
        </p>
      </div>
      <a href="<?= e($def['pagina']) ?>?view=form" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs transition-colors hover:bg-brand-600">
        <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 4.75C12.4142 4.75 12.75 5.08579 12.75 5.5V11.25H18.5C18.9142 11.25 19.25 11.5858 19.25 12C19.25 12.4142 18.9142 12.75 18.5 12.75H12.75V18.5C12.75 18.9142 12.4142 19.25 12 19.25C11.5858 19.25 11.25 18.9142 11.25 18.5V12.75H5.5C5.08579 12.75 4.75 12.4142 4.75 12C4.75 11.5858 5.08579 11.25 5.5 11.25H11.25V5.5C11.25 5.08579 11.5858 4.75 12 4.75Z" fill="" />
        </svg>
        Nuevo <?= $def['titulo'] ?>
      </a>
    </div>

    <?php
        // Filtro opcional (usado en reportajes_fotos)
        $filtro = $_GET['filtro_reportaje'] ?? '';
        $joinSql = '';
        $selectSql = 't.*';
        foreach ($def['campos'] as $campo => $c) {
            if ($c['tipo'] === 'select' && isset($c['fuente'])) {
                $alias = 'rel_' . $campo;
                $fdef = $tablas[$c['fuente']];
                $expr = columna_relacion($c['fuente']);
                $prefijo = (strpos($expr, '(') === false) ? $alias . '.' : '';
                $selectSql .= ', ' . $prefijo . $expr . ' AS lab_' . $campo;
                $joinSql .= " LEFT JOIN `{$c['fuente']}` $alias ON $alias.`{$fdef['pk']}` = t.`$campo`";
            }
        }
        $whereSql = '';
        if ($nombre_tabla === 'reportajes_fotos' && $filtro !== '') {
            $whereSql = ' WHERE t.`reportaje_id` = ' . (int) $filtro;
        }
        if ($nombre_tabla === 'reportajes') {
            $selectSql .= ", (SELECT rp.url_foto FROM reportajes_fotos rp WHERE rp.reportaje_id = t.reportaje_id AND rp.es_principal = 1 LIMIT 1) AS portada_url";
        }
        $sql = "SELECT $selectSql FROM `$nombre_tabla` t $joinSql $whereSql ORDER BY t.`$pk` DESC";
        $filas = $pdo->query($sql)->fetchAll();
    ?>

    <?php
    // ---------- Helpers de visibilidad (ocultar / mostrar sin borrar) ----------
    function pill_oculto($f)
    {
        if ((int) ($f['activo'] ?? 1) === 0) {
            echo '<span class="inline-flex items-center gap-1 rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 7C14.76 7 17 9.24 17 12C17 12.65 16.87 13.26 16.64 13.83L19.56 16.75C21.07 15.49 22.26 13.86 22.99 12C21.26 7.61 17 4.5 12 4.5C10.6 4.5 9.26 4.75 8.02 5.2L10.17 7.36C10.74 7.13 11.35 7 12 7ZM2 4.27L4.28 6.55L4.74 7.01C3.08 8.3 1.78 10.02 1 12C2.73 16.39 7 19.5 12 19.5C13.55 19.5 15.03 19.2 16.38 18.66L16.8 19.08L19.73 22L21 20.73L3.27 3L2 4.27ZM7.53 9.8L9.08 11.35C9.03 11.56 9 11.78 9 12C9 13.66 10.34 15 12 15C12.22 15 12.44 14.97 12.65 14.92L14.2 16.47C13.53 16.8 12.79 17 12 17C9.24 17 7 14.76 7 12C7 11.21 7.2 10.47 7.53 9.8ZM11.45 6.07C11.63 6.02 11.82 6 12 6C14.76 6 17 8.24 17 11C17 11.18 16.98 11.37 16.93 11.55L17 11.62L21.06 15.68C22.39 14.43 23.39 12.98 23.98 11.4C22.69 8.01 19.54 5.5 16 5.5C15.23 5.5 14.5 5.64 13.82 5.88L11.45 6.07Z" fill="currentColor"/></svg>Oculto</span>';
        }
    }

    function btn_visibilidad($tabla, $f, $pk, $boton = '')
    {
        $visible = (int) ($f['activo'] ?? 1) === 1;
        $titulo = $visible ? 'Ocultar de la web' : 'Mostrar en la web';
        $color = $visible
            ? 'text-gray-600 hover:bg-warning-500 hover:text-white dark:text-gray-300'
            : 'text-gray-400 hover:bg-success-500 hover:text-white dark:text-gray-500';
        $cls = $boton === ''
            ? 'rounded-lg bg-gray-100 p-2 ' . $color . ' transition dark:bg-gray-800'
            : $boton . ' ' . $color . ' transition';
        $ojoAbierto = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12C2.73 16.39 7 19.5 12 19.5C17 19.5 21.27 16.39 23 12C21.27 7.61 17 4.5 12 4.5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z" fill="currentColor"/>';
        $ojoCerrado = '<path d="M12 7C14.76 7 17 9.24 17 12C17 12.65 16.87 13.26 16.64 13.83L19.56 16.75C21.07 15.49 22.26 13.86 22.99 12C21.26 7.61 17 4.5 12 4.5C10.6 4.5 9.26 4.75 8.02 5.2L10.17 7.36C10.74 7.13 11.35 7 12 7ZM2 4.27L4.28 6.55L4.74 7.01C3.08 8.3 1.78 10.02 1 12C2.73 16.39 7 19.5 12 19.5C13.55 19.5 15.03 19.2 16.38 18.66L16.8 19.08L19.73 22L21 20.73L3.27 3L2 4.27ZM7.53 9.8L9.08 11.35C9.03 11.56 9 11.78 9 12C9 13.66 10.34 15 12 15C12.22 15 12.44 14.97 12.65 14.92L14.2 16.47C13.53 16.8 12.79 17 12 17C9.24 17 7 14.76 7 12C7 11.21 7.2 10.47 7.53 9.8ZM11.45 6.07C11.63 6.02 11.82 6 12 6C14.76 6 17 8.24 17 11C17 11.18 16.98 11.37 16.93 11.55L17 11.62L21.06 15.68C22.39 14.43 23.39 12.98 23.98 11.4C22.69 8.01 19.54 5.5 16 5.5C15.23 5.5 14.5 5.64 13.82 5.88L11.45 6.07Z" fill="currentColor"/>';
        $svg = $visible ? $ojoAbierto : $ojoCerrado;
        echo '<a href="crud.php?tabla=' . rawurlencode($tabla) . '&amp;accion=toggle&amp;id=' . (int) $f[$pk] . '" title="' . $titulo . '" class="' . $cls . '"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' . $svg . '</svg></a>';
    }

    function pill_estado($f)
    {
        $estado = $f['estado'] ?? 'publicado';
        if ($estado === 'borrador') {
            echo '<span class="inline-flex items-center gap-1 rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-500"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 14C10.8954 14 10 14.8954 10 16C10 17.1046 10.8954 18 12 18C13.1046 18 14 17.1046 14 16C14 14.8954 13.1046 14 12 14ZM3.073 14.39L4.877 8.975C5.534 7.299 6.751 5.978 8.175 5.594L6.092 3.511L7.506 2.097L12 6.591L16.494 2.097L17.908 3.511L15.825 5.594C17.249 5.978 18.466 7.299 19.123 8.975L20.927 14.39C22.004 16.687 21.375 19.443 18.97 20.622C17.524 21.553 15.639 21.632 14.216 20.576C13.096 19.784 12 19.784 10.884 20.576C9.462 21.632 7.577 21.553 6.131 20.622C3.725 19.443 3.096 16.687 4.173 14.39" fill="currentColor"/></svg>Borrador</span>';
        }
    }
    ?>

    <?php if ($nombre_tabla === 'reportajes_fotos') : ?>
    <div class="mb-5 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
      <form method="get" action="reportajes_fotos.php" class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="w-full sm:max-w-xs">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Filtrar por reportaje</label>
          <select name="filtro_reportaje" onchange="this.form.submit()" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">Todos los reportajes</option>
            <?php foreach (opciones_select('reportajes') as $val => $txt) : ?>
              <option value="<?= e($val) ?>" <?= ((string) $filtro === (string) $val) ? 'selected' : '' ?>><?= e($txt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
      <?php if (empty($filas)) : ?>
        <div class="py-14 text-center">
          <svg class="mx-auto mb-4 fill-current text-gray-300 dark:text-gray-600" width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 2C6.79086 2 5 3.79086 5 6V18C5 20.2091 6.79086 22 9 22H15C17.2091 22 19 20.2091 19 18V8L14 2H9ZM13.25 7.5C13.25 7.08579 12.9142 6.75 12.5 6.75H10.75V5.5C10.75 5.08579 10.4142 4.75 10 4.75C9.58579 4.75 9.25 5.08579 9.25 5.5V6.75H7.5C7.08579 6.75 6.75 7.08579 6.75 7.5C6.75 7.91421 7.08579 8.25 7.5 8.25H9.25V10C9.25 10.4142 9.58579 10.75 10 10.75C10.4142 10.75 10.75 10.4142 10.75 10V8.25H12.5C12.9142 8.25 13.25 7.91421 13.25 7.5ZM12.75 8.42V4.06L17.27 8.42H12.75Z" fill="" />
          </svg>
          <p class="text-theme-sm text-gray-500 dark:text-gray-400">No hay registros en <b><?= e($nombre_tabla) ?></b>.</p>
          <a href="<?= e($def['pagina']) ?>?view=form" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs transition-colors hover:bg-brand-600">
            Crear el primero
          </a>
        </div>
      <?php elseif ($nombre_tabla === 'fotos' || $nombre_tabla === 'reportajes_fotos') : ?>
        <!-- ===== Galería de fotos ===== -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
          <?php foreach ($filas as $f) : ?>
            <?php $img = app_asset($f['url_foto'] ?? ''); ?>
            <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
              <div class="relative h-44 w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                <?php if ($img) : ?>
                  <img src="<?= e($img) ?>" alt="<?= e($f['titulo'] ?? $f['descripcion'] ?? '') ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" onerror="this.closest('.group').querySelector('.ph').classList.remove('hidden');this.remove()" />
                <?php endif; ?>
                <div class="ph <?= $img ? 'hidden' : '' ?> flex h-full w-full items-center justify-center">
                  <span class="text-4xl font-semibold text-gray-300 dark:text-gray-600"><?= e(mb_strtoupper(mb_substr(($f['titulo'] ?? $f['descripcion'] ?? '#' . $f[$pk]) ?: '#' . $f[$pk], 0, 1))) ?></span>
                </div>
                <?php if (!empty($f['es_principal'])) : ?>
                  <span class="absolute left-3 top-3 rounded-full bg-brand-500 px-2.5 py-1 text-theme-xs font-medium text-white shadow-sm">Principal</span>
                <?php endif; ?>
                <?php if (!empty($f['descripcion'])) : ?>
                  <button class="absolute right-3 top-3 rounded-full bg-gray-900/60 p-1.5 text-white backdrop-blur-sm" title="Ver descripción" onclick="this.nextElementSibling.classList.remove('hidden')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2Z" stroke="currentColor" stroke-width="2"/><path d="M12 8.25C12.4142 8.25 12.75 8.58579 12.75 9V16.5C12.75 16.9142 12.4142 17.25 12 17.25C11.5858 17.25 11.25 16.9142 11.25 16.5V9C11.25 8.58579 11.5858 8.25 12 8.25Z" fill="currentColor"/></svg>
                  </button>
                  <div class="absolute inset-x-0 top-0 hidden max-h-full overflow-y-auto bg-gray-900/90 p-3 text-theme-xs text-white backdrop-blur-sm"><?= e($f['descripcion']) ?></div>
                <?php endif; ?>
              </div>
              <div class="flex items-center justify-between gap-2 p-3">
                <div class="min-w-0">
                  <p class="truncate text-theme-sm font-medium text-gray-800 dark:text-white/90"><?= e($f['titulo'] ?? '#' . $f[$pk]) ?></p>
                  <p class="text-theme-xs text-gray-500 dark:text-gray-400">#<?= (int) $f[$pk] ?><?= isset($f['lab_reportaje_id']) && $f['lab_reportaje_id'] ? ' · ' . e($f['lab_reportaje_id']) : '' ?></p>
                </div>
                <div class="flex shrink-0 items-center gap-1">
                  <?php pill_oculto($f); ?><?php pill_estado($f); ?>
                  <?php btn_visibilidad($nombre_tabla, $f, $pk); ?>
                  <?php if ($img) : ?>
                    <a href="<?= e($img) ?>" target="_blank" class="rounded-lg bg-gray-100 p-2 text-gray-600 transition hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-300" title="Ver en grande">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 3H21V9M9 21H3V15M13 11L21 3M3 21L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                  <?php endif; ?>
                  <a href="<?= e($def['pagina']) ?>?view=form&id=<?= (int) $f[$pk] ?>" class="rounded-lg bg-gray-100 p-2 text-gray-600 transition hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-300" title="Editar">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.731 2.26901C20.7062 1.24356 19.0392 1.24356 18.0137 2.26901L8.85954 11.4231C8.63215 11.6505 8.45803 11.9246 8.34857 12.2276L7.06601 15.7708C6.99431 15.9667 7.03255 16.1816 7.1695 16.3397C7.28551 16.4801 7.45546 16.5628 7.63656 16.5687C7.70527 16.5712 7.77431 16.5629 7.83951 16.5441L11.3858 15.2675C11.6907 15.1586 11.9664 14.9841 12.1954 14.756C20.1249 6.82616 14.1054 8.51083 19.7549 0.931707C20.204 0.369708 21.1997 0.353588 21.731 0.884877V2.26901Z" fill="currentColor"/></svg>
                  </a>
                  <a href="crud.php?tabla=<?= e($nombre_tabla) ?>&accion=eliminar&id=<?= (int) $f[$pk] ?>" onclick="return confirm('¿Seguro que deseas eliminar este registro?');" class="rounded-lg bg-error-50 p-2 text-error-600 transition hover:bg-error-500 hover:text-white dark:bg-error-500/10 dark:text-error-500" title="Eliminar">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 3.5C6.75 3.08579 7.08579 2.75 7.5 2.75H16.5C16.9142 2.75 17.25 3.08579 17.25 3.5C17.25 3.91421 16.9142 4.25 16.5 4.25H7.5C7.08579 4.25 6.75 3.91421 6.75 3.5ZM4.75 6C4.75 5.58579 5.08579 5.25 5.5 5.25H18.5C18.9142 5.25 19.25 5.58579 19.25 6C19.25 6.41421 18.9142 6.75 18.5 6.75H18.25L17.4409 19.0355C17.391 19.8574 16.7026 20.5 15.8798 20.5H8.12021C7.29744 20.5 6.60896 19.8574 6.55908 19.0355L5.75 6.75H5.5C5.08579 6.75 4.75 6.41421 4.75 6ZM7.25646 6.75L8.05123 19.0678C8.05881 19.2067 8.17458 19.3182 8.31362 19.3182H15.6864C15.8254 19.3182 15.9412 19.2067 15.9488 19.0678L16.7435 6.75H7.25646ZM10.75 9C10.75 8.58579 11.0858 8.25 11.5 8.25C11.9142 8.25 12.25 8.58579 12.25 9V17C12.25 17.4142 11.9142 17.75 11.5 17.75C11.0858 17.75 10.75 17.4142 10.75 17V9ZM14.25 9C14.25 8.58579 14.5858 8.25 15 8.25C15.4142 8.25 15.75 8.58579 15.75 9V17C15.75 17.4142 15.4142 17.75 15 17.75C14.5858 17.75 14.25 17.4142 14.25 17V9Z" fill="currentColor"/></svg>
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php elseif ($nombre_tabla === 'reportajes') : ?>
        <!-- ===== Tarjetas de reportajes ===== -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
          <?php foreach ($filas as $f) : ?>
            <?php $img = app_asset($f['foto_portada_url'] ?: ($f['portada_url'] ?? '')); ?>
            <div class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-theme-md dark:border-gray-800 dark:bg-white/[0.03]">
              <div class="relative h-44 w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                <?php if ($img) : ?>
                  <img src="<?= e($img) ?>" alt="<?= e($f['titulo']) ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" onerror="this.closest('.group').querySelector('.ph').classList.remove('hidden');this.remove()" />
                <?php endif; ?>
                <div class="ph <?= $img ? 'hidden' : '' ?> flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-500 to-brand-600">
                  <span class="text-5xl font-semibold text-white/80"><?= e(mb_strtoupper(mb_substr($f['titulo'], 0, 1))) ?></span>
                </div>
                <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-theme-xs font-medium text-gray-700 shadow-sm backdrop-blur-sm dark:bg-gray-900/90 dark:text-gray-300">
                  <?= e($f['fecha_publicacion'] ?? '—') ?>
                </span>
                <?php if ((int) $f['es_destacado'] === 1) : ?>
                  <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-warning-500 px-2.5 py-1 text-theme-xs font-medium text-white shadow-sm">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L14.4 8.8L21.6 9.2L16 13.6L18 20.8L12 17L6 20.8L8 13.6L2.4 9.2L9.6 8.8L12 2Z" fill="currentColor"/></svg>
                    Destacado
                  </span>
                <?php endif; ?>
              </div>
              <div class="flex flex-1 flex-col p-5">
                <h3 class="mb-1.5 line-clamp-2 text-base font-semibold leading-snug text-gray-800 dark:text-white/90"><?= e($f['titulo']) ?></h3>
                <?php if (!empty($f['resumen_corto'])) : ?>
                  <p class="mb-3 line-clamp-2 text-theme-sm text-gray-500 dark:text-gray-400"><?= e($f['resumen_corto']) ?></p>
                <?php endif; ?>
                <div class="mt-auto flex items-center justify-between gap-2 border-t border-gray-100 pt-3 dark:border-gray-800">
                  <div class="flex min-w-0 items-center gap-2">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-theme-xs font-semibold text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">
                      <?= e(mb_strtoupper(mb_substr(trim($f['lab_autor_id'] ?? 'A'), 0, 1))) ?>
                    </span>
                    <span class="truncate text-theme-xs text-gray-600 dark:text-gray-300"><?= e($f['lab_autor_id'] ?? 'Sin autor') ?></span>
                  </div>
                  <div class="flex shrink-0 items-center gap-1">
                    <?php pill_oculto($f); ?><?php pill_estado($f); ?>
                    <?php btn_visibilidad($nombre_tabla, $f, $pk); ?>
                    <a href="<?= e($def['pagina']) ?>?view=form&id=<?= (int) $f[$pk] ?>" class="rounded-lg bg-gray-100 p-2 text-gray-600 transition hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-300" title="Editar">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.731 2.26901C20.7062 1.24356 19.0392 1.24356 18.0137 2.26901L8.85954 11.4231C8.63215 11.6505 8.45803 11.9246 8.34857 12.2276L7.06601 15.7708C6.99431 15.9667 7.03255 16.1816 7.1695 16.3397C7.28551 16.4801 7.45546 16.5628 7.63656 16.5687C7.70527 16.5712 7.77431 16.5629 7.83951 16.5441L11.3858 15.2675C11.6907 15.1586 11.9664 14.9841 12.1954 14.756C20.1249 6.82616 14.1054 8.51083 19.7549 0.931707C20.204 0.369708 21.1997 0.353588 21.731 0.884877V2.26901Z" fill="currentColor"/></svg>
                    </a>
                    <a href="crud.php?tabla=<?= e($nombre_tabla) ?>&accion=eliminar&id=<?= (int) $f[$pk] ?>" onclick="return confirm('¿Seguro que deseas eliminar este registro?');" class="rounded-lg bg-error-50 p-2 text-error-600 transition hover:bg-error-500 hover:text-white dark:bg-error-500/10 dark:text-error-500" title="Eliminar">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 3.5C6.75 3.08579 7.08579 2.75 7.5 2.75H16.5C16.9142 2.75 17.25 3.08579 17.25 3.5C17.25 3.91421 16.9142 4.25 16.5 4.25H7.5C7.08579 4.25 6.75 3.91421 6.75 3.5ZM4.75 6C4.75 5.58579 5.08579 5.25 5.5 5.25H18.5C18.9142 5.25 19.25 5.58579 19.25 6C19.25 6.41421 18.9142 6.75 18.5 6.75H18.25L17.4409 19.0355C17.391 19.8574 16.7026 20.5 15.8798 20.5H8.12021C7.29744 20.5 6.60896 19.8574 6.55908 19.0355L5.75 6.75H5.5C5.08579 6.75 4.75 6.41421 4.75 6ZM7.25646 6.75L8.05123 19.0678C8.05881 19.2067 8.17458 19.3182 8.31362 19.3182H15.6864C15.8254 19.3182 15.9412 19.2067 15.9488 19.0678L16.7435 6.75H7.25646ZM10.75 9C10.75 8.58579 11.0858 8.25 11.5 8.25C11.9142 8.25 12.25 8.58579 12.25 9V17C12.25 17.4142 11.9142 17.75 11.5 17.75C11.0858 17.75 10.75 17.4142 10.75 17V9ZM14.25 9C14.25 8.58579 14.5858 8.25 15 8.25C15.4142 8.25 15.75 8.58579 15.75 9V17C15.75 17.4142 15.4142 17.75 15 17.75C14.5858 17.75 14.25 17.4142 14.25 17V9Z" fill="currentColor"/></svg>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php elseif ($nombre_tabla === 'noticias') : ?>
        <!-- ===== Tarjetas de noticias ===== -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
          <?php foreach ($filas as $f) : ?>
            <?php $img = app_asset($f['foto_url'] ?? ''); ?>
            <div class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-theme-md dark:border-gray-800 dark:bg-white/[0.03]">
              <a href="<?= e($f['link_externo'] ?? '#') ?>" target="_blank" class="relative block h-40 w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                <?php if ($img) : ?>
                  <img src="<?= e($img) ?>" alt="<?= e($f['titulo']) ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" onerror="this.closest('.group').querySelector('.ph').classList.remove('hidden');this.remove()" />
                <?php endif; ?>
                <div class="ph <?= $img ? 'hidden' : '' ?> flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                  <span class="text-5xl font-semibold text-white/30"><?= e(mb_strtoupper(mb_substr($f['titulo'], 0, 1))) ?></span>
                </div>
                <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-theme-xs font-medium text-gray-700 shadow-sm backdrop-blur-sm dark:bg-gray-900/90 dark:text-gray-300"><?= e($f['fecha_publicacion'] ?? '—') ?></span>
                <?php if ((int) $f['es_destacado'] === 1) : ?>
                <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-warning-500 px-2.5 py-1 text-theme-xs font-medium text-white shadow-sm">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L14.4 8.8L21.6 9.2L16 13.6L18 20.8L12 17L6 20.8L8 13.6L2.4 9.2L9.6 8.8L12 2Z" fill="currentColor"/></svg>
                  Destacado
                </span>
                <?php endif; ?>
              </a>
              <div class="flex flex-1 flex-col p-5">
                <h3 class="mb-1.5 line-clamp-2 text-base font-semibold leading-snug text-gray-800 dark:text-white/90"><?= e($f['titulo']) ?></h3>
                <?php if (!empty($f['resumen'])) : ?>
                  <p class="mb-3 line-clamp-2 text-theme-sm text-gray-500 dark:text-gray-400"><?= e($f['resumen']) ?></p>
                <?php endif; ?>
                <div class="mt-auto flex items-center justify-between gap-2 border-t border-gray-100 pt-3 dark:border-gray-800">
                  <a href="<?= e($f['link_externo'] ?? '#') ?>" target="_blank" class="inline-flex items-center gap-1.5 text-theme-xs font-medium text-brand-600 transition hover:text-brand-700 dark:text-brand-400">
                    Ver original
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 3H21V9M9 21H3V15M13 11L21 3M3 21L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </a>
                  <div class="flex shrink-0 items-center gap-1">
                    <?php pill_oculto($f); ?><?php pill_estado($f); ?>
                    <?php btn_visibilidad($nombre_tabla, $f, $pk); ?>
                    <a href="<?= e($def['pagina']) ?>?view=form&id=<?= (int) $f[$pk] ?>" title="Editar" class="rounded-lg bg-gray-100 p-2 text-gray-600 transition hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-300">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.731 2.26901C20.7062 1.24356 19.0392 1.24356 18.0137 2.26901L8.85954 11.4231C8.63215 11.6505 8.45803 11.9246 8.34857 12.2276L7.06601 15.7708C6.99431 15.9667 7.03255 16.1816 7.1695 16.3397C7.28551 16.4801 7.45546 16.5628 7.63656 16.5687C7.70527 16.5712 7.77431 16.5629 7.83951 16.5441L11.3858 15.2675C11.6907 15.1586 11.9664 14.9841 12.1954 14.756C20.1249 6.82616 14.1054 8.51083 19.7549 0.931707C20.204 0.369708 21.1997 0.353588 21.731 0.884877V2.26901Z" fill="currentColor"/></svg>
                    </a>
                    <a href="crud.php?tabla=<?= e($nombre_tabla) ?>&accion=eliminar&id=<?= (int) $f[$pk] ?>" onclick="return confirm('¿Seguro que deseas eliminar este registro?');" title="Eliminar" class="rounded-lg bg-error-50 p-2 text-error-600 transition hover:bg-error-500 hover:text-white dark:bg-error-500/10 dark:text-error-500">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 3.5C6.75 3.08579 7.08579 2.75 7.5 2.75H16.5C16.9142 2.75 17.25 3.08579 17.25 3.5C17.25 3.91421 16.9142 4.25 16.5 4.25H7.5C7.08579 4.25 6.75 3.91421 6.75 3.5ZM4.75 6C4.75 5.58579 5.08579 5.25 5.5 5.25H18.5C18.9142 5.25 19.25 5.58579 19.25 6C19.25 6.41421 18.9142 6.75 18.5 6.75H18.25L17.4409 19.0355C17.391 19.8574 16.7026 20.5 15.8798 20.5H8.12021C7.29744 20.5 6.60896 19.8574 6.55908 19.0355L5.75 6.75H5.5C5.08579 6.75 4.75 6.41421 4.75 6ZM7.25646 6.75L8.05123 19.0678C8.05881 19.2067 8.17458 19.3182 8.31362 19.3182H15.6864C15.8254 19.3182 15.9412 19.2067 15.9488 19.0678L16.7435 6.75H7.25646ZM10.75 9C10.75 8.58579 11.0858 8.25 11.5 8.25C11.9142 8.25 12.25 8.58579 12.25 9V17C12.25 17.4142 11.9142 17.75 11.5 17.75C11.0858 17.75 10.75 17.4142 10.75 17V9ZM14.25 9C14.25 8.58579 14.5858 8.25 15 8.25C15.4142 8.25 15.75 8.58579 15.75 9V17C15.75 17.4142 15.4142 17.75 15 17.75C14.5858 17.75 14.25 17.4142 14.25 17V9Z" fill="currentColor"/></svg>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php elseif ($nombre_tabla === 'boletines') : ?>
        <!-- ===== Tarjetas de boletines ===== -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
          <?php foreach ($filas as $f) : ?>
            <?php $img = app_asset($f['foto_portada_url'] ?? ''); ?>
            <div class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-theme-md dark:border-gray-800 dark:bg-white/[0.03]">
              <div class="relative h-40 w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                <?php if ($img) : ?>
                  <img src="<?= e($img) ?>" alt="<?= e($f['titulo'] ?? '') ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" onerror="this.closest('.group').querySelector('.ph').classList.remove('hidden');this.remove()" />
                <?php endif; ?>
                <div class="ph <?= $img ? 'hidden' : '' ?> flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-500 to-brand-600">
                  <span class="text-5xl font-semibold text-white/80"><?= e(mb_strtoupper(mb_substr($f['titulo'] ?? 'B', 0, 1))) ?></span>
                </div>
                <span class="absolute left-3 top-3 rounded-full bg-brand-500 px-2.5 py-1 text-theme-xs font-medium text-white shadow-sm">N.º <?= (int) $f['numero_boletin'] ?></span>
                <span class="absolute right-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-theme-xs font-medium text-gray-700 shadow-sm backdrop-blur-sm dark:bg-gray-900/90 dark:text-gray-300"><?= e($f['fecha_publicacion'] ?? '—') ?></span>
              </div>
              <div class="flex flex-1 flex-col p-5">
                <h3 class="mb-1.5 line-clamp-2 text-base font-semibold leading-snug text-gray-800 dark:text-white/90"><?= e($f['titulo'] ?? 'Boletín N.º ' . (int) $f['numero_boletin']) ?></h3>
                <?php if (!empty($f['resumen'])) : ?>
                  <p class="mb-3 line-clamp-2 text-theme-sm text-gray-500 dark:text-gray-400"><?= e($f['resumen']) ?></p>
                <?php endif; ?>
                <div class="mt-auto flex items-center justify-between gap-2 border-t border-gray-100 pt-3 dark:border-gray-800">
                  <?php if (!empty($f['archivo_pdf_url'])) : ?>
                    <a href="<?= e($f['archivo_pdf_url']) ?>" target="_blank" class="inline-flex items-center gap-1.5 text-theme-xs font-medium text-error-600 transition hover:text-error-700 dark:text-error-500">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16L12 4M12 16L7 11M12 16L17 11M4 20H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                      Descargar PDF
                    </a>
                  <?php else : ?>
                    <span class="text-theme-xs text-gray-400">Sin PDF</span>
                  <?php endif; ?>
                  <div class="flex shrink-0 items-center gap-1">
                    <?php pill_estado($f); ?>
                    <a href="<?= e($def['pagina']) ?>?view=form&id=<?= (int) $f[$pk] ?>" title="Editar" class="rounded-lg bg-gray-100 p-2 text-gray-600 transition hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-300">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.731 2.26901C20.7062 1.24356 19.0392 1.24356 18.0137 2.26901L8.85954 11.4231C8.63215 11.6505 8.45803 11.9246 8.34857 12.2276L7.06601 15.7708C6.99431 15.9667 7.03255 16.1816 7.1695 16.3397C7.28551 16.4801 7.45546 16.5628 7.63656 16.5687C7.70527 16.5712 7.77431 16.5629 7.83951 16.5441L11.3858 15.2675C11.6907 15.1586 11.9664 14.9841 12.1954 14.756C20.1249 6.82616 14.1054 8.51083 19.7549 0.931707C20.204 0.369708 21.1997 0.353588 21.731 0.884877V2.26901Z" fill="currentColor"/></svg>
                    </a>
                    <a href="crud.php?tabla=<?= e($nombre_tabla) ?>&accion=eliminar&id=<?= (int) $f[$pk] ?>" onclick="return confirm('¿Seguro que deseas eliminar este registro?');" title="Eliminar" class="rounded-lg bg-error-50 p-2 text-error-600 transition hover:bg-error-500 hover:text-white dark:bg-error-500/10 dark:text-error-500">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 3.5C6.75 3.08579 7.08579 2.75 7.5 2.75H16.5C16.9142 2.75 17.25 3.08579 17.25 3.5C17.25 3.91421 16.9142 4.25 16.5 4.25H7.5C7.08579 4.25 6.75 3.91421 6.75 3.5ZM4.75 6C4.75 5.58579 5.08579 5.25 5.5 5.25H18.5C18.9142 5.25 19.25 5.58579 19.25 6C19.25 6.41421 18.9142 6.75 18.5 6.75H18.25L17.4409 19.0355C17.391 19.8574 16.7026 20.5 15.8798 20.5H8.12021C7.29744 20.5 6.60896 19.8574 6.55908 19.0355L5.75 6.75H5.5C5.08579 6.75 4.75 6.41421 4.75 6ZM7.25646 6.75L8.05123 19.0678C8.05881 19.2067 8.17458 19.3182 8.31362 19.3182H15.6864C15.8254 19.3182 15.9412 19.2067 15.9488 19.0678L16.7435 6.75H7.25646ZM10.75 9C10.75 8.58579 11.0858 8.25 11.5 8.25C11.9142 8.25 12.25 8.58579 12.25 9V17C12.25 17.4142 11.9142 17.75 11.5 17.75C11.0858 17.75 10.75 17.4142 10.75 17V9ZM14.25 9C14.25 8.58579 14.5858 8.25 15 8.25C15.4142 8.25 15.75 8.58579 15.75 9V17C15.75 17.4142 15.4142 17.75 15 17.75C14.5858 17.75 14.25 17.4142 14.25 17V9Z" fill="currentColor"/></svg>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php elseif ($nombre_tabla === 'podcasts' || $nombre_tabla === 'videos') : ?>
        <!-- ===== Tarjetas de multimedia (podcasts / videos) ===== -->
        <?php $esVideo = $nombre_tabla === 'videos'; ?>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
          <?php foreach ($filas as $f) : ?>
            <?php $img = app_asset($f['imagen_url'] ?? ''); ?>
            <div class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-theme-md dark:border-gray-800 dark:bg-white/[0.03]">
              <div class="relative h-44 w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                <?php if ($img) : ?>
                  <img src="<?= e($img) ?>" alt="<?= e($f['titulo']) ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" onerror="this.closest('.group').querySelector('.ph').classList.remove('hidden');this.remove()" />
                <?php endif; ?>
                <div class="ph <?= $img ? 'hidden' : '' ?> flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                  <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/15 backdrop-blur-sm">
                    <svg class="ml-0.5 fill-current text-white" width="22" height="22" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 4.6C6 3.6 7.1 3 8.05 3.5L19.64 10.38C20.6 10.9 20.6 12.28 19.64 12.8L8.05 19.68C7.1 20.21 6 19.6 6 18.6V4.6Z" /></svg>
                  </span>
                </div>
                <span class="absolute right-3 top-3 rounded-full bg-gray-900/60 px-2.5 py-1 text-theme-xs font-medium text-white backdrop-blur-sm">
                  <?php if ((int) $f['duracion_segundos'] > 0) : ?>
                    <?= floor((int) $f['duracion_segundos'] / 60) ?>:<?= str_pad((string) ((int) $f['duracion_segundos'] % 60), 2, '0', STR_PAD_LEFT) ?> min
                  <?php else : ?>
                    <?= $esVideo ? 'Video' : 'Audio' ?>
                  <?php endif; ?>
                </span>
              </div>
              <div class="flex flex-1 flex-col p-5">
                <h3 class="mb-1.5 line-clamp-2 text-base font-semibold leading-snug text-gray-800 dark:text-white/90"><?= e($f['titulo']) ?></h3>
                <?php if (!empty($f['descripcion'])) : ?>
                  <p class="mb-3 line-clamp-2 text-theme-sm text-gray-500 dark:text-gray-400"><?= e($f['descripcion']) ?></p>
                <?php endif; ?>
                <div class="mt-auto flex items-center justify-between gap-2 border-t border-gray-100 pt-3 dark:border-gray-800">
                  <?php if (!empty($f['url_embed'])) : ?>
                    <a href="<?= e($f['url_embed']) ?>" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-theme-xs font-medium text-white transition hover:bg-brand-600">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 4.6C6 3.6 7.1 3 8.05 3.5L19.64 10.38C20.6 10.9 20.6 12.28 19.64 12.8L8.05 19.68C7.1 20.21 6 19.6 6 18.6V4.6Z" fill="currentColor"/></svg>
                      <?= $esVideo ? 'Ver video' : 'Escuchar' ?>
                    </a>
                  <?php else : ?>
                    <span class="text-theme-xs text-gray-400">Sin enlace</span>
                  <?php endif; ?>
                  <div class="flex shrink-0 items-center gap-1">
                    <?php pill_estado($f); ?>
                    <a href="<?= e($def['pagina']) ?>?view=form&id=<?= (int) $f[$pk] ?>" title="Editar" class="rounded-lg bg-gray-100 p-2 text-gray-600 transition hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-300">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.731 2.26901C20.7062 1.24356 19.0392 1.24356 18.0137 2.26901L8.85954 11.4231C8.63215 11.6505 8.45803 11.9246 8.34857 12.2276L7.06601 15.7708C6.99431 15.9667 7.03255 16.1816 7.1695 16.3397C7.28551 16.4801 7.45546 16.5628 7.63656 16.5687C7.70527 16.5712 7.77431 16.5629 7.83951 16.5441L11.3858 15.2675C11.6907 15.1586 11.9664 14.9841 12.1954 14.756C20.1249 6.82616 14.1054 8.51083 19.7549 0.931707C20.204 0.369708 21.1997 0.353588 21.731 0.884877V2.26901Z" fill="currentColor"/></svg>
                    </a>
                    <a href="crud.php?tabla=<?= e($nombre_tabla) ?>&accion=eliminar&id=<?= (int) $f[$pk] ?>" onclick="return confirm('¿Seguro que deseas eliminar este registro?');" title="Eliminar" class="rounded-lg bg-error-50 p-2 text-error-600 transition hover:bg-error-500 hover:text-white dark:bg-error-500/10 dark:text-error-500">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 3.5C6.75 3.08579 7.08579 2.75 7.5 2.75H16.5C16.9142 2.75 17.25 3.08579 17.25 3.5C17.25 3.91421 16.9142 4.25 16.5 4.25H7.5C7.08579 4.25 6.75 3.91421 6.75 3.5ZM4.75 6C4.75 5.58579 5.08579 5.25 5.5 5.25H18.5C18.9142 5.25 19.25 5.58579 19.25 6C19.25 6.41421 18.9142 6.75 18.5 6.75H18.25L17.4409 19.0355C17.391 19.8574 16.7026 20.5 15.8798 20.5H8.12021C7.29744 20.5 6.60896 19.8574 6.55908 19.0355L5.75 6.75H5.5C5.08579 6.75 4.75 6.41421 4.75 6ZM7.25646 6.75L8.05123 19.0678C8.05881 19.2067 8.17458 19.3182 8.31362 19.3182H15.6864C15.8254 19.3182 15.9412 19.2067 15.9488 19.0678L16.7435 6.75H7.25646ZM10.75 9C10.75 8.58579 11.0858 8.25 11.5 8.25C11.9142 8.25 12.25 8.58579 12.25 9V17C12.25 17.4142 11.9142 17.75 11.5 17.75C11.0858 17.75 10.75 17.4142 10.75 17V9ZM14.25 9C14.25 8.58579 14.5858 8.25 15 8.25C15.4142 8.25 15.75 8.58579 15.75 9V17C15.75 17.4142 15.4142 17.75 15 17.75C14.5858 17.75 14.25 17.4142 14.25 17V9Z" fill="currentColor"/></svg>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php elseif ($nombre_tabla === 'pdfs') : ?>
        <!-- ===== Tarjetas de documentos PDF ===== -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
          <?php foreach ($filas as $f) : ?>
            <div class="flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 transition hover:shadow-theme-md dark:border-gray-800 dark:bg-white/[0.03]">
              <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-error-50 dark:bg-error-500/15">
                <svg class="fill-current text-error-600 dark:text-error-500" width="26" height="26" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M11 2H7C5.89543 2 5 2.89543 5 4V20C5 21.1046 5.89543 22 7 22H17C18.1046 22 19 21.1046 19 20V8L14 2H11ZM5 6V19C5 20.1 5.9 21 7 21H17C18.1 21 19 20.1 19 19V17H15V21H9V17H5V6Z"/><path d="M14 2V8H20"/></svg>
              </div>
              <div class="min-w-0 flex-1">
                <h3 class="mb-0.5 truncate text-theme-sm font-semibold text-gray-800 dark:text-white/90"><?= e($f['titulo']) ?></h3>
                <?php if (!empty($f['descripcion'])) : ?>
                  <p class="mb-1 line-clamp-2 text-theme-xs text-gray-500 dark:text-gray-400"><?= e($f['descripcion']) ?></p>
                <?php endif; ?>
                <p class="mb-3 truncate text-theme-xs text-gray-400 dark:text-gray-500">#<?= (int) $f[$pk] ?> · <?= e($f['url_archivo']) ?></p>
                <div class="flex items-center gap-2">
                  <?php pill_oculto($f); ?><?php pill_estado($f); ?>
                  <?php btn_visibilidad($nombre_tabla, $f, $pk); ?>
                  <a href="<?= e($f['url_archivo']) ?>" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-error-500 px-3 py-1.5 text-theme-xs font-medium text-white transition hover:bg-error-600">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16L12 4M12 16L7 11M12 16L17 11M4 20H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Ver / Descargar PDF
                  </a>
                  <a href="<?= e($def['pagina']) ?>?view=form&id=<?= (int) $f[$pk] ?>" title="Editar" class="rounded-lg bg-gray-100 p-2 text-gray-600 transition hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-300">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.731 2.26901C20.7062 1.24356 19.0392 1.24356 18.0137 2.26901L8.85954 11.4231C8.63215 11.6505 8.45803 11.9246 8.34857 12.2276L7.06601 15.7708C6.99431 15.9667 7.03255 16.1816 7.1695 16.3397C7.28551 16.4801 7.45546 16.5628 7.63656 16.5687C7.70527 16.5712 7.77431 16.5629 7.83951 16.5441L11.3858 15.2675C11.6907 15.1586 11.9664 14.9841 12.1954 14.756C20.1249 6.82616 14.1054 8.51083 19.7549 0.931707C20.204 0.369708 21.1997 0.353588 21.731 0.884877V2.26901Z" fill="currentColor"/></svg>
                  </a>
                  <a href="crud.php?tabla=<?= e($nombre_tabla) ?>&accion=eliminar&id=<?= (int) $f[$pk] ?>" onclick="return confirm('¿Seguro que deseas eliminar este registro?');" title="Eliminar" class="rounded-lg bg-error-50 p-2 text-error-600 transition hover:bg-error-500 hover:text-white dark:bg-error-500/10 dark:text-error-500">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 3.5C6.75 3.08579 7.08579 2.75 7.5 2.75H16.5C16.9142 2.75 17.25 3.08579 17.25 3.5C17.25 3.91421 16.9142 4.25 16.5 4.25H7.5C7.08579 4.25 6.75 3.91421 6.75 3.5ZM4.75 6C4.75 5.58579 5.08579 5.25 5.5 5.25H18.5C18.9142 5.25 19.25 5.58579 19.25 6C19.25 6.41421 18.9142 6.75 18.5 6.75H18.25L17.4409 19.0355C17.391 19.8574 16.7026 20.5 15.8798 20.5H8.12021C7.29744 20.5 6.60896 19.8574 6.55908 19.0355L5.75 6.75H5.5C5.08579 6.75 4.75 6.41421 4.75 6ZM7.25646 6.75L8.05123 19.0678C8.05881 19.2067 8.17458 19.3182 8.31362 19.3182H15.6864C15.8254 19.3182 15.9412 19.2067 15.9488 19.0678L16.7435 6.75H7.25646ZM10.75 9C10.75 8.58579 11.0858 8.25 11.5 8.25C11.9142 8.25 12.25 8.58579 12.25 9V17C12.25 17.4142 11.9142 17.75 11.5 17.75C11.0858 17.75 10.75 17.4142 10.75 17V9ZM14.25 9C14.25 8.58579 14.5858 8.25 15 8.25C15.4142 8.25 15.75 8.58579 15.75 9V17C15.75 17.4142 15.4142 17.75 15 17.75C14.5858 17.75 14.25 17.4142 14.25 17V9Z" fill="currentColor"/></svg>
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php elseif ($nombre_tabla === 'autores') : ?>
        <!-- ===== Tarjetas de autores ===== -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
          <?php foreach ($filas as $f) : ?>
            <?php $img = app_asset($f['foto_url'] ?? ''); ?>
            <div class="flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 transition hover:shadow-theme-md dark:border-gray-800 dark:bg-white/[0.03]">
              <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800">
                <?php if ($img) : ?>
                  <img src="<?= e($img) ?>" alt="<?= e(trim($f['nombre'] . ' ' . ($f['apellidos'] ?? ''))) ?>" class="h-full w-full object-cover" onerror="this.closest('div').querySelector('.ph').classList.remove('hidden');this.remove()" />
                <?php endif; ?>
                <div class="ph <?= $img ? 'hidden' : '' ?> flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-500 to-brand-600">
                  <span class="text-xl font-semibold text-white/80"><?= e(mb_strtoupper(mb_substr($f['nombre'], 0, 1))) ?></span>
                </div>
              </div>
              <div class="min-w-0 flex-1">
                <div class="mb-0.5 flex items-center justify-between gap-2">
                  <h3 class="truncate text-theme-sm font-semibold text-gray-800 dark:text-white/90"><?= e(trim($f['nombre'] . ' ' . ($f['apellidos'] ?? ''))) ?></h3>
                  <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-theme-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">#<?= (int) $f[$pk] ?></span>
                </div>
                <?php if (!empty($f['biografia'])) : ?>
                  <p class="mb-3 line-clamp-3 text-theme-xs leading-relaxed text-gray-500 dark:text-gray-400"><?= e($f['biografia']) ?></p>
                <?php endif; ?>
                <div class="flex items-center gap-2">
                  <?php pill_oculto($f); ?><?php pill_estado($f); ?>
                  <?php btn_visibilidad($nombre_tabla, $f, $pk); ?>
                  <?php if (!empty($f['lab_user_id'])) : ?>
                    <span class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2 py-0.5 text-theme-xs font-medium text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 2 12 2C14.2091 2 16 4.79086 16 7ZM12 13C8.68629 13 6 15.6863 6 19V22H18V19C18 15.6863 15.3137 13 12 13Z" fill="currentColor"/></svg>
                      <?= e($f['lab_user_id']) ?>
                    </span>
                  <?php endif; ?>
                  <a href="<?= e($def['pagina']) ?>?view=form&id=<?= (int) $f[$pk] ?>" title="Editar" class="ml-auto rounded-lg bg-gray-100 p-2 text-gray-600 transition hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-300">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.731 2.26901C20.7062 1.24356 19.0392 1.24356 18.0137 2.26901L8.85954 11.4231C8.63215 11.6505 8.45803 11.9246 8.34857 12.2276L7.06601 15.7708C6.99431 15.9667 7.03255 16.1816 7.1695 16.3397C7.28551 16.4801 7.45546 16.5628 7.63656 16.5687C7.70527 16.5712 7.77431 16.5629 7.83951 16.5441L11.3858 15.2675C11.6907 15.1586 11.9664 14.9841 12.1954 14.756C20.1249 6.82616 14.1054 8.51083 19.7549 0.931707C20.204 0.369708 21.1997 0.353588 21.731 0.884877V2.26901Z" fill="currentColor"/></svg>
                  </a>
                  <a href="crud.php?tabla=<?= e($nombre_tabla) ?>&accion=eliminar&id=<?= (int) $f[$pk] ?>" onclick="return confirm('¿Seguro que deseas eliminar este registro?');" title="Eliminar" class="rounded-lg bg-error-50 p-2 text-error-600 transition hover:bg-error-500 hover:text-white dark:bg-error-500/10 dark:text-error-500">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.75 3.5C6.75 3.08579 7.08579 2.75 7.5 2.75H16.5C16.9142 2.75 17.25 3.08579 17.25 3.5C17.25 3.91421 16.9142 4.25 16.5 4.25H7.5C7.08579 4.25 6.75 3.91421 6.75 3.5ZM4.75 6C4.75 5.58579 5.08579 5.25 5.5 5.25H18.5C18.9142 5.25 19.25 5.58579 19.25 6C19.25 6.41421 18.9142 6.75 18.5 6.75H18.25L17.4409 19.0355C17.391 19.8574 16.7026 20.5 15.8798 20.5H8.12021C7.29744 20.5 6.60896 19.8574 6.55908 19.0355L5.75 6.75H5.5C5.08579 6.75 4.75 6.41421 4.75 6ZM7.25646 6.75L8.05123 19.0678C8.05881 19.2067 8.17458 19.3182 8.31362 19.3182H15.6864C15.8254 19.3182 15.9412 19.2067 15.9488 19.0678L16.7435 6.75H7.25646ZM10.75 9C10.75 8.58579 11.0858 8.25 11.5 8.25C11.9142 8.25 12.25 8.58579 12.25 9V17C12.25 17.4142 11.9142 17.75 11.5 17.75C11.0858 17.75 10.75 17.4142 10.75 17V9ZM14.25 9C14.25 8.58579 14.5858 8.25 15 8.25C15.4142 8.25 15.75 8.58579 15.75 9V17C15.75 17.4142 15.4142 17.75 15 17.75C14.5858 17.75 14.25 17.4142 14.25 17V9Z" fill="currentColor"/></svg>
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php else : ?>
        <!-- ===== Tabla (usuarios) ===== -->
        <div class="overflow-x-auto custom-scrollbar">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-5 py-3 text-left text-theme-xs font-medium uppercase text-gray-500 dark:text-gray-400">ID</th>
                <?php foreach ($def['campos'] as $campo => $c) : ?>
                  <?php if ($c['tipo'] === 'textarea' || $c['tipo'] === 'password') continue; ?>
                  <th class="px-5 py-3 text-left text-theme-xs font-medium uppercase text-gray-500 dark:text-gray-400"><?= e($c['label']) ?></th>
                <?php endforeach; ?>
                <th class="px-5 py-3 text-right text-theme-xs font-medium uppercase text-gray-500 dark:text-gray-400">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              <?php foreach ($filas as $f) : ?>
              <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                <td class="px-5 py-4">
                  <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-theme-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">#<?= (int) $f[$pk] ?></span>
                </td>
                <?php foreach ($def['campos'] as $campo => $c) : ?>
                  <?php if ($c['tipo'] === 'textarea' || $c['tipo'] === 'password') continue; ?>
                  <td class="px-5 py-4">
                    <?php if ($campo === 'avatar_url') : ?>
                      <span class="flex items-center gap-2">
                        <img src="<?= e($f['avatar_url'] ?: 'assets/images/user/user-01.jpg') ?>" alt="" class="h-8 w-8 rounded-full object-cover" onerror="this.remove()" />
                        <span class="max-w-[220px] truncate text-theme-sm text-gray-700 dark:text-gray-300"><?= e($f['avatar_url'] ?? '') ?></span>
                      </span>
                    <?php elseif ($c['tipo'] === 'checkbox') : ?>
                      <?php if ((int) $f[$campo] === 1) : ?>
                        <span class="inline-flex rounded-full bg-success-50 px-2.5 py-0.5 text-theme-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500">Si</span>
                      <?php else : ?>
                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-theme-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">No</span>
                      <?php endif; ?>
                    <?php elseif ($c['tipo'] === 'select' && isset($c['fuente'])) : ?>
                      <span class="text-theme-sm text-gray-700 dark:text-gray-300"><?= e($f['lab_' . $campo] ?? 'Sin asignar') ?></span>
                    <?php else : ?>
                      <span class="block max-w-[240px] truncate text-theme-sm text-gray-700 dark:text-gray-300"><?= e($f[$campo] ?? '') ?></span>
                    <?php endif; ?>
                  </td>
                <?php endforeach; ?>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <?php pill_oculto($f); ?><?php pill_estado($f); ?>
                    <?php btn_visibilidad($nombre_tabla, $f, $pk, 'inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-2.5 py-1.5 text-theme-xs font-medium dark:border-gray-800'); ?>
                    <a href="<?= e($def['pagina']) ?>?view=form&id=<?= (int) $f[$pk] ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-white/5">Editar</a>
                    <a href="crud.php?tabla=<?= e($nombre_tabla) ?>&accion=eliminar&id=<?= (int) $f[$pk] ?>" onclick="return confirm('¿Seguro que deseas eliminar este registro?');" class="inline-flex items-center gap-1.5 rounded-lg border border-error-200 px-3 py-1.5 text-theme-xs font-medium text-error-700 transition hover:bg-error-50 dark:border-error-500/30 dark:text-error-500 dark:hover:bg-error-500/10">Eliminar</a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>