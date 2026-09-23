<?php require_once 'conexion.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT r.*, CONCAT(a.nombre,' ',a.apellidos) AS autor
     FROM reportajes r
     LEFT JOIN autores a ON a.autor_id = r.autor_id
     WHERE r.reportaje_id = ? AND r.activo = 1 AND r.estado = 'publicado'"
);
$stmt->execute([$id]);
$r = $stmt->fetch();

if (!$r) {
    header('Location: reportajes.php');
    exit;
}

$foto = $r['foto_portada_url'];
if (!$foto) {
    $foto = $pdo->prepare(
        "SELECT url_foto FROM reportajes_fotos WHERE reportaje_id = ? AND es_principal = 1 AND activo = 1 LIMIT 1"
    );
    $foto->execute([$r['reportaje_id']]);
    $foto = $foto->fetchColumn() ?: 'assets/images/video.jpg';
}

// Parrafos del desarrollo (con editor tipo Word puede ser HTML)
$desarrolloHtml = mostrar_html($r['desarrollo']);

// Ultimas noticias (sidebar)
$ultimas = $pdo->query(
    "SELECT reportaje_id, titulo, fecha_publicacion FROM reportajes WHERE activo = 1 AND estado = 'publicado' ORDER BY fecha_publicacion DESC, reportaje_id DESC LIMIT 4"
)->fetchAll();

// Archivos por mes
$archivos = $pdo->query(
    "SELECT DATE_FORMAT(fecha_publicacion, '%Y-%m') AS mes
     FROM reportajes
     WHERE activo = 1 AND estado = 'publicado'
     GROUP BY mes
     ORDER BY mes DESC
     LIMIT 6"
)->fetchAll();
$meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= e($r['titulo']) ?> - Diálogo y Desarrollo Perú</title>
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&subset=latin-ext,vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-starter.css">
  </head>
  <body>
<header id="site-header" class="fixed-top">
  <div class="container">
      <nav class="navbar navbar-expand-lg stroke">
      <a class="navbar-brand" href="index.php">
          <img src="assets/images/logo.png" alt="Your logo" title="Your logo" style="height:75px;" />
      </a>
          <button class="navbar-toggler collapsed bg-gradient" type="button" data-toggle="collapse"
              data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false"
              aria-label="Toggle navigation">
              <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
              <span class="navbar-toggler-icon fa icon-close fa-times"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
              <ul class="navbar-nav ml-auto">
                  <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                  <li class="nav-item active"><a class="nav-link" href="reportajes.php">Reportajes <span class="sr-only">(current)</span></a></li>
                  <li class="nav-item"><a class="nav-link" href="podcasts.php">Podcast</a></li>
                  <li class="nav-item"><a class="nav-link" href="boletines.php">Boletín NTEP</a></li>
                  <li class="nav-item"><a class="nav-link" href="contact.php">Sobre D&D</a></li>
                  <li class="ml-2"><a href="contact.php" class="btn btn-style btn-outline-secondary">Contacto</a></li>
              </ul>
          </div>
      </nav>
  </div>
</header>

<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big"><?= e($r['titulo']) ?></h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="index.php">Inicio</a></li>
                            <li><a href="reportajes.php">Reportajes</a></li>
                            <li class="active"><?= e(mb_substr($r['titulo'], 0, 40)) ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="w3l-blog mt-lg-5">
    <div class="text-element-9 py-5 mt-lg-5">
        <div class="container py-lg-3">
            <div class="row grid-text-9">
                <div class="col-lg-8">
                    <div class="blog-single-post">
                        <div class="post-content">
                            <h2 class="title-single mb-3"><?= e($r['titulo']) ?></h2>
                            <h5 class="mb-4"><?= fecha_bonita($r['fecha_publicacion']) ?><?= $r['autor'] ? ' · Por ' . e($r['autor']) : '' ?></h5>
                        </div>
                        <div class="single-post-image mb-4 text-center">
                            <?php if ($r['archivo_pdf_url']) : ?>
                                <a target="_blank" href="<?= e($r['archivo_pdf_url']) ?>"><img src="<?= e($foto) ?>" class="img-fluid w-100 radius-image" alt="blog-post-image" /><br />Clic en la imagen para ver la infografía completa</a>
                            <?php else : ?>
                                <img src="<?= e($foto) ?>" class="img-fluid w-100 radius-image" alt="blog-post-image" />
                            <?php endif; ?>
                        </div>
                        <div class="single-post-content">
                            <?php if (!empty($r['resumen_corto'])) : ?>
                            <blockquote class="blockquote my-5">
                                <q class="mb-3 d-block"><?= nl2br(e($r['resumen_corto'])) ?>
                            </blockquote>
                            <?php endif; ?>
                            <div class="desarrollo-contenido"><?= $desarrolloHtml ?></div>
                        </div>
                        <nav class="post-navigation row mb-5 py-4">
                            <div class="post-prev col-md-6 pr-sm-5">
                                <span class="nav-title"><span class="fa fa-arrow-left mr-2"></span> <a href="reportajes.php">Reportajes</a></span>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-4 left-text-9 mt-lg-0 mt-5 pl-lg-4">
                    <div class="left-top-9 mt-5 pt-sm-3">
                        <h6 class="heading-small-text-9 mb-3">Últimas noticias</h6>
                        <?php foreach ($ultimas as $n) : ?>
                        <a href="reportaje.php?id=<?= (int)$n['reportaje_id'] ?>" class="p-post d-block py-2">
                            <h6 class="text-left-inner-9"><?= e($n['titulo']) ?></h6>
                            <span class="sub-inner-text-9"><?= fecha_bonita($n['fecha_publicacion']) ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="categories mt-5 pt-sm-3">
                        <h6 class="heading-small-text-9">Archivos</h6>
                        <ul>
                            <?php foreach ($archivos as $a) : ?>
                            <li><a href="reportajes.php" class=""><?= $meses[substr($a['mes'], 5, 2)] ?? '—' ?> <?= substr($a['mes'], 0, 4) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="w3l-footer-29-main py-5" id="footer">
  <div class="footer-29 py-md-3">
    <div class="container">
      <div class="row footer-top-29">
        <div class="col-lg-6 col-md-6 footer-list-29 footer-1">
          <h6 class="footer-title-29">Quiénes Somos</h6>
          <p>Somos un espacio de periodismo independiente que busca visibilizar las acciones de diálogo en el país desde una mirada constructiva.</p>
          <div class="main-social-footer-29">
            <a target="_blank" href="https://www.facebook.com/DialogoyDesarrolloPeru" class="facebook"><span class="fa fa-facebook"></span></a>
            <a target="_blank" href="https://www.tiktok.com/@dialogo.y.desarrollo" class="twitter"><img src="assets/images/tiktokp.png"></a>
            <a target="_blank" href="https://www.instagram.com/dialogo.y.desarrollo/" class="instagram"><span class="fa fa-instagram"></span></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 footer-list-29 footer-2 mt-md-0 mt-5">
          <ul>
            <h6 class="footer-title-29">Contenido</h6>
            <li><a href="reportajes.php">Reportajes</a></li>
            <li><a href="boletines.php">Boletines</a></li>
            <li><a href="podcasts.php">Podcast</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-6 mt-lg-0 mt-5 footer-list-29 footer-3">
          <div class="properties">
            <h6 class="footer-title-29">Contacto</h6>
            <ul>
              <li><a href="mailto:info@dialogoydesarrollo.com.pe">info@dialogoydesarrollo.com.pe</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="bottom-copies text-center">
        <p class="copy-footer-29">&copy; 2026 Diálogo y Desarrollo Perú. All rights reserved | Designed by <a target="_blank" href="https://www.wsperu.info">WebSolutions</a>
        <br>
        <a href="admin/login.php" class="text-muted" style="font-size:12px;">Panel de Administración</a></p>
      </div>
    </div>
  </div>
</section>

<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
  $(window).on("scroll", function () {
    if ($(window).scrollTop() >= 80) { $("#site-header").addClass("nav-fixed"); } else { $("#site-header").removeClass("nav-fixed"); }
  });
</script>
</body>
</html>