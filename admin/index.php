<?php
require_once 'config.php';
require_login();

$counts = [];
$tables = [
    'usuarios'         => 'Usuarios',
    'autores'          => 'Autores',
    'reportajes'       => 'Reportajes',
    'noticias'         => 'Noticias',
    'boletines'        => 'Boletines',
    'podcasts'         => 'Podcasts',
    'videos'           => 'Videos',
    'pdfs'             => 'Documentos PDF',
    'fotos'            => 'Fotos',
    'reportajes_fotos' => 'Fotos de Reportajes',
];
foreach ($tables as $tabla => $label) {
    try {
        $counts[$tabla] = ['label' => $label, 'n' => (int) $pdo->query("SELECT COUNT(*) FROM $tabla")->fetchColumn()];
    } catch (Exception $ex) {
        $counts[$tabla] = ['label' => $label, 'n' => 0];
    }
}

$ultimosReportajes = $pdo->query(
    "SELECT r.reportaje_id, r.titulo, r.fecha_publicacion, COALESCE(CONCAT(a.nombre, ' ', a.apellidos), 'Sin autor') AS autor, r.es_destacado
     FROM reportajes r
     LEFT JOIN autores a ON a.autor_id = r.autor_id
     ORDER BY r.created_at DESC
     LIMIT 5"
)->fetchAll();

$ultimasNoticias = $pdo->query(
    "SELECT noticia_id, titulo, fecha_publicacion FROM noticias ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

$page = 'dashboard';
$title = 'Dashboard';
include 'includes/layout_head.php';
include 'includes/sidebar.php';
include 'includes/header.php';
?>

<main class="relative overflow-hidden">
  <div class="p-5 sm:p-8 lg:py-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
      <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">
        Dashboard de Contenido
      </h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
        Resumen general de la base de datos <b>dialogoydesarrollo</b>. Bienvenido, <?= e($_SESSION['nombre_completo'] ?? '') ?>.
      </p>
    </div>

    <!-- Metricas -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-4">
      <?php foreach ($counts as $tabla => $c) : ?>
      <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/15">
          <svg class="fill-brand-500 dark:fill-brand-400" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.665 3.75618C11.8762 3.65061 12.1247 3.65061 12.3358 3.75618L18.7807 6.97853L12.3358 10.2009C12.1247 10.3064 11.8762 10.3064 11.665 10.2009L5.22014 6.97853L11.665 3.75618ZM4.29297 8.19199V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0365V11.6512C11.1631 11.6205 11.0777 11.5843 10.9942 11.5425L4.29297 8.19199ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19199L13.0066 11.5425C12.9229 11.5844 12.8372 11.6207 12.75 11.6515V20.037ZM13.0066 2.41453C12.3732 2.09783 11.6277 2.09783 10.9942 2.41453L4.03676 5.89316C3.27449 6.27429 2.79297 7.05339 2.79297 7.90563V16.0946C2.79297 16.9468 3.27448 17.7259 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.7259 21.2079 16.9468 21.2079 16.0946V7.90563C21.2079 7.05339 20.7264 6.27429 19.9641 5.89316L13.0066 2.41453Z" fill="" />
          </svg>
        </div>
        <div class="mt-5 flex items-end justify-between">
          <div>
            <span class="text-theme-sm text-gray-500 dark:text-gray-400"><?= e($c['label']) ?></span>
            <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90"><?= (int) $c['n'] ?></h4>
          </div>
          <a
            href="<?= e($tabla === 'reportajes_fotos' ? 'reportajes_fotos.php' : ($tabla === 'pdfs' ? 'pdfs.php' : $tabla . '.php')) ?>"
            class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-theme-xs font-medium text-brand-500 transition-colors hover:bg-brand-50 hover:text-brand-700 dark:hover:bg-brand-500/10 dark:hover:text-brand-400"
          >
            Ver
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Ultimos registros -->
    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
      <!-- Ultimos reportajes -->
      <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-start justify-between">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Ultimos Reportajes</h3>
          <a href="reportajes.php" class="text-theme-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Ver todos</a>
        </div>
        <?php if (empty($ultimosReportajes)) : ?>
          <p class="mt-6 text-theme-sm text-gray-500 dark:text-gray-400">Aun no hay reportajes registrados.</p>
        <?php else : ?>
        <ul class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
          <?php foreach ($ultimosReportajes as $r) : ?>
          <li class="flex items-center justify-between gap-3 py-3">
            <div class="min-w-0">
              <p class="truncate text-theme-sm font-medium text-gray-800 dark:text-white/90"><?= e($r['titulo']) ?></p>
              <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400"><?= e($r['autor']) ?> &middot; <?= e($r['fecha_publicacion']) ?></p>
            </div>
            <?php if ((int) $r['es_destacado'] === 1) : ?>
              <span class="shrink-0 rounded-full bg-warning-50 px-2.5 py-0.5 text-theme-xs font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-500">Destacado</span>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <!-- Ultimas noticias -->
      <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-start justify-between">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Ultimas Noticias</h3>
          <a href="noticias.php" class="text-theme-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Ver todas</a>
        </div>
        <?php if (empty($ultimasNoticias)) : ?>
          <p class="mt-6 text-theme-sm text-gray-500 dark:text-gray-400">Aun no hay noticias registradas.</p>
        <?php else : ?>
        <ul class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
          <?php foreach ($ultimasNoticias as $nx) : ?>
          <li class="flex items-center justify-between gap-3 py-3">
            <p class="truncate text-theme-sm font-medium text-gray-800 dark:text-white/90"><?= e($nx['titulo']) ?></p>
            <span class="shrink-0 text-theme-xs text-gray-500 dark:text-gray-400"><?= e($nx['fecha_publicacion']) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>