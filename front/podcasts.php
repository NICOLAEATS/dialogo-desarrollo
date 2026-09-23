<?php require_once 'conexion.php';

$podcasts = $pdo->query("SELECT * FROM podcasts WHERE activo = 1 AND estado = 'publicado' ORDER BY fecha_publicacion DESC")->fetchAll();
$videos = $pdo->query("SELECT * FROM videos WHERE activo = 1 AND estado = 'publicado' ORDER BY fecha_publicacion DESC")->fetchAll();
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Podcast - Diálogo y Desarrollo Perú</title>
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
                  <li class="nav-item"><a class="nav-link" href="reportajes.php">Reportajes</a></li>
                  <li class="nav-item active"><a class="nav-link" href="podcasts.php">Podcast <span class="sr-only">(current)</span></a></li>
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
        <div class="row"><div class="col-md-12">
            <div class="breadcrumb-contents"><h2 class="title-big">Podcast</h2></div>
        </div></div>
    </div>
</section>

<section class="w3l-homeblock3 py-5">
    <div class="container py-lg-5 py-md-4">
        <h3 class="title-big mb-5 text-center">Podcasts</h3>
        <div class="row">
            <?php foreach ($podcasts as $p) : ?>
            <div class="col-lg-4 col-md-6 grids5-info mt-5">
                <div class="area-box">
                    <h4><?= e($p['titulo']) ?></h4>
                    <p><?= e($p['descripcion']) ?></p>
                    <p><small><?= fecha_bonita($p['fecha_publicacion']) ?></small></p>
                    <?= mostrar_medio($p, false) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="w3l-homeblock3 py-5">
    <div class="container py-lg-5 py-md-4">
        <h3 class="title-big mb-5 text-center">Videos / Especiales</h3>
        <div class="row">
            <?php foreach ($videos as $v) : ?>
            <div class="col-lg-4 col-md-6 grids5-info mt-5">
                <div class="area-box">
                    <h4><?= e($v['titulo']) ?></h4>
                    <p><?= e($v['descripcion']) ?></p>
                    <p><small><?= fecha_bonita($v['fecha_publicacion']) ?></small></p>
                    <?= mostrar_medio($v, true) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="w3l-footer-29-main py-5" id="footer">
  <div class="footer-29 py-md-3">
    <div class="container">
      <div class="bottom-copies text-center">
        <p class="copy-footer-29">&copy; 2026 Diálogo y Desarrollo Perú. All rights reserved</p>
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
