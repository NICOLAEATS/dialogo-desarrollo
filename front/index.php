<?php require_once 'conexion.php';

// Reportaje destacado
$destacado = $pdo->query(
    "SELECT r.*, CONCAT(a.nombre,' ',a.apellidos) AS autor
     FROM reportajes r
     LEFT JOIN autores a ON a.autor_id = r.autor_id
     WHERE r.es_destacado = 1 AND r.activo = 1 AND r.estado = 'publicado'
     ORDER BY r.fecha_publicacion DESC LIMIT 1"
)->fetch();

// Si no hay destacado, tomar el mas reciente
if (!$destacado) {
    $destacado = $pdo->query(
        "SELECT r.*, CONCAT(a.nombre,' ',a.apellidos) AS autor
         FROM reportajes r
LEFT JOIN autores a ON a.autor_id = r.autor_id
     WHERE r.activo = 1 AND r.estado = 'publicado'
     ORDER BY r.fecha_publicacion DESC LIMIT 1"
    )->fetch();
}

// Foto principal del destacado
if ($destacado) {
    if (empty($destacado['foto_portada_url'])) {
        $fotoDest = $pdo->query(
            "SELECT url_foto FROM reportajes_fotos WHERE reportaje_id = {$destacado['reportaje_id']} AND es_principal = 1 AND activo = 1 LIMIT 1"
        )->fetch();
        $destacado['foto'] = $fotoDest ? $fotoDest['url_foto'] : 'assets/images/video.jpg';
    } else {
        $destacado['foto'] = $destacado['foto_portada_url'];
    }
} else {
    $destacado['foto'] = 'assets/images/video.jpg';
}

// Reportajes recientes (grid)
$reportajes = $pdo->query(
    "SELECT r.*, CONCAT(a.nombre,' ',a.apellidos) AS autor,
            COALESCE(NULLIF(r.foto_portada_url,''),
                     (SELECT url_foto FROM reportajes_fotos WHERE reportaje_id = r.reportaje_id AND es_principal = 1 AND activo = 1 LIMIT 1),
                     'assets/images/video.jpg') AS foto
     FROM reportajes r
     LEFT JOIN autores a ON a.autor_id = r.autor_id
     WHERE r.activo = 1 AND r.estado = 'publicado' AND r.es_destacado = 0
     ORDER BY r.fecha_publicacion DESC LIMIT 3"
)->fetchAll();

// Noticias recientes
$noticias = $pdo->query(
    "SELECT * FROM noticias WHERE activo = 1 AND estado = 'publicado' AND es_destacado = 0 ORDER BY fecha_publicacion DESC LIMIT 3"
)->fetchAll();

// Boletin mas reciente
$boletin = $pdo->query(
    "SELECT * FROM boletines WHERE activo = 1 AND estado = 'publicado' ORDER BY numero_boletin DESC LIMIT 1"
)->fetch();

// Podcasts
$podcasts = $pdo->query(
    "SELECT * FROM podcasts WHERE activo = 1 AND estado = 'publicado' ORDER BY fecha_publicacion DESC LIMIT 4"
)->fetchAll();

// Especiales (videos)
$especiales = $pdo->query(
    "SELECT * FROM videos WHERE activo = 1 AND estado = 'publicado' ORDER BY fecha_publicacion DESC LIMIT 4"
)->fetchAll();

// Destacados de portada: noticias y reportajes marcados como "Destacado" en el admin
$destacadosRep = $pdo->query(
    "SELECT r.*, CONCAT(a.nombre,' ',a.apellidos) AS autor,
            COALESCE(NULLIF(r.foto_portada_url,''),
                     (SELECT url_foto FROM reportajes_fotos WHERE reportaje_id = r.reportaje_id AND es_principal = 1 AND activo = 1 LIMIT 1),
                     'assets/images/video.jpg') AS foto
     FROM reportajes r
     LEFT JOIN autores a ON a.autor_id = r.autor_id
     WHERE r.es_destacado = 1 AND r.activo = 1 AND r.estado = 'publicado'
     ORDER BY r.fecha_publicacion DESC"
)->fetchAll();
$destacadosNot = $pdo->query(
    "SELECT * FROM noticias WHERE es_destacado = 1 AND activo = 1 AND estado = 'publicado' ORDER BY fecha_publicacion DESC"
)->fetchAll();
$destacados = [];
foreach ($destacadosRep as $r) {
    $destacados[] = ['tipo' => 'reportaje', 'id' => (int) $r['reportaje_id'], 'noticia_id' => 0, 'url' => 'reportaje.php?id=' . (int) $r['reportaje_id'], 'externo' => false, 'titulo' => $r['titulo'], 'resumen' => $r['resumen_corto'] ?? '', 'foto' => $r['foto'], 'fecha' => $r['fecha_publicacion']];
}
foreach ($destacadosNot as $n) {
    $destacados[] = ['tipo' => 'noticia', 'id' => 0, 'noticia_id' => (int) $n['noticia_id'], 'url' => $n['link_externo'] ?: '#', 'externo' => true, 'titulo' => $n['titulo'], 'resumen' => $n['resumen'] ?? '', 'foto' => $n['foto_url'] ?: 'assets/images/video.jpg', 'fecha' => $n['fecha_publicacion']];
}
usort($destacados, function ($a, $b) {
    return strcmp((string) $b['fecha'], (string) $a['fecha']);
});
$destacados = array_slice($destacados, 0, 8);
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>DDP Noticias - Diálogo y Desarrollo Perú</title>
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&subset=latin-ext,vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-starter.css">
  </head>
  <body>
<!-- header -->
<header id="site-header" class="fixed-top">
  <div class="container">
      <nav class="navbar navbar-expand-lg stroke">
      <a class="navbar-brand" href="index.html">
          <img src="assets/images/logo.png" alt="Your logo" title="Your logo" style="height:75px;" />
      </a>
          <button class="navbar-toggler  collapsed bg-gradient" type="button" data-toggle="collapse"
              data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false"
              aria-label="Toggle navigation">
              <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
              <span class="navbar-toggler-icon fa icon-close fa-times"></span>
              </span>
          </button>
          <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
              <ul class="navbar-nav ml-auto">
                  <li class="nav-item active">
                      <a class="nav-link" href="index.php">Inicio <span class="sr-only">(current)</span></a>
                  </li>
                  <li class="nav-item @@about__active">
                      <a class="nav-link" href="#actualidad">Actualidad</a>
                  </li>
                  <li class="nav-item @@about__active">
                      <a class="nav-link" href="reportajes.php">Reportajes</a>
                  </li>
                  <li class="nav-item @@about__active">
                      <a class="nav-link" href="podcasts.php">Podcast</a>
                  </li>
                  <li class="nav-item @@about__active">
                      <a class="nav-link" href="boletines.php">Boletín NTEP</a>
                  </li>
                  <li class="nav-item @@about__active">
                      <a class="nav-link" href="#ali">Alianzas</a>
                  </li>
                  <li class="nav-item @@contact__active">
                      <a class="nav-link" href="contact.php">Sobre D&D</a>
                  </li>
                  <li class="ml-2">
                      <a href="contact.php" class="btn btn-style btn-outline-secondary">Contacto</a>
                  </li>
              </ul>
          </div>
      </nav>
  </div>
</header>
<!-- //header -->

<?php if (!empty($destacados)) : ?>
<section class="grids-block-5 py-lg-4 py-2">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h2 class="title-big mb-0"><span class="fa fa-star text-warning"></span> Destacados</h2>
        </div>
        <div class="row">
            <?php foreach ($destacados as $lad) : ?>
            <?php if ($lad['tipo'] === 'reportaje' && !empty($destacado) && $lad['id'] === (int) $destacado['reportaje_id']) continue; ?>
            <div class="col-lg-3 col-md-6 grids5-info mt-4">
                <a href="<?= e($lad['url']) ?>" <?= $lad['externo'] ? 'target="_blank"' : '' ?> class="d-block position-relative">
                    <img src="<?= e($lad['foto']) ?>" alt="<?= e($lad['titulo']) ?>" class="img-fluid" style="height:170px;width:100%;object-fit:cover;" />
                    <span class="badge badge-pill" style="position:absolute;top:10px;right:10px;background:#fbbf24;color:#fff;">Destacado</span>
                </a>
                <div class="blog-info">
                    <h5><?= fecha_bonita($lad['fecha']) ?> · <span><?= $lad['tipo'] === 'noticia' ? 'Noticia' : 'Reportaje' ?></span></h5>
                    <h4><a href="<?= e($lad['url']) ?>" <?= $lad['externo'] ? 'target="_blank"' : '' ?> class="d-block"><?= e($lad['titulo']) ?></a></h4>
                    <a href="<?= e($lad['url']) ?>" <?= $lad['externo'] ? 'target="_blank"' : '' ?> class="btn mt-3 p-0">Leer <span class="fa fa-arrow-right"></span> </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Reportajes</h2>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="w3l-video w3l-homeblock3 " id="video">
    <div class="container-fluid">
        <div class="video-grids-info row">
            <div class="video-gd-right col-lg-6 p-0">
                <div class="position-relative">
                    <a href="reportaje.php?id=<?= (int)$destacado['reportaje_id'] ?>"><img src="<?= e($destacado['foto']) ?>" alt="" class="img-fluid"></a>
                </div>
            </div>
            <div class="video-gd-left col-lg-6 p-lg-5 p-4 align-self">
                <div class="p-xl-4 p-0 video-wrap">
                    <h5><?= fecha_bonita($destacado['fecha_publicacion']) ?></h5>
                    <h3 class="title-big text-left mb-4"><a href="reportaje.php?id=<?= (int)$destacado['reportaje_id'] ?>"><?= e($destacado['titulo']) ?></a></h3>
                    <p><?= e($destacado['resumen_corto']) ?></p>
                    <a href="reportaje.php?id=<?= (int)$destacado['reportaje_id'] ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span> </a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="grids-block-5 py-1">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php foreach ($reportajes as $r) : ?>
                <div class="col-lg-4 col-md-6 grids5-info mt-5">
                    <a href="reportaje.php?id=<?= (int)$r['reportaje_id'] ?>" class="d-block"><img src="<?= e($r['foto']) ?>" alt="" class="img-fluid" /></a>
                    <div class="blog-info">
                        <h5><?= fecha_bonita($r['fecha_publicacion']) ?></h5>
                        <h4><a href="reportaje.php?id=<?= (int)$r['reportaje_id'] ?>" class="d-block"><?= e($r['titulo']) ?></a></h4>
                        <a href="reportaje.php?id=<?= (int)$r['reportaje_id'] ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span> </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination">
                <ul>
                    <li><a href="reportajes.php">Ver todos</a></li>
                </ul>
            </div>
        </div>
</div>

<section class="breadcrumb-area py-sm-5 py-1">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Noticias Recientes</h2><a class="anchor" id="actualidad"></a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php foreach ($noticias as $n) : ?>
                <div class="col-lg-4 col-md-6 grids5-info">
                    <a target="_blank" href="<?= e($n['link_externo'] ?: '#') ?>" class="d-block"><img src="<?= e($n['foto_url'] ?: 'assets/images/video.jpg') ?>" alt="" class="img-fluid" /></a>
                    <div class="blog-info">
                        <h5><?= fecha_bonita($n['fecha_publicacion']) ?></h5>
                        <h4><a target="_blank" href="<?= e($n['link_externo'] ?: '#') ?>" class="d-block"><?= e($n['titulo']) ?></a></h4>
                        <a target="_blank" href="<?= e($n['link_externo'] ?: '#') ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span> </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination">
                <ul>
                    <li><a href="https://www.facebook.com/DialogoyDesarrolloPeru">Ver todos</a></li>
                </ul>
            </div>
        </div>
</div>

<?php if ($boletin) : ?>
<section class="w3l-homeblock5 py-0">
    <div class="container py-lg-5 py-4">
        <div class="row">
            <div class="col-lg-8 align-self">
                <h3 class="title-big mb-4"> Boletin NTEP Año 2025 </h3>
                <p class=""><?= e($boletin['resumen']) ?></p>
                <div class="row mt-sm-4 mt-2 px-3">
                    <div class="col-6 p-0">
                        <span>Nº <?= (int)$boletin['numero_boletin'] ?></span>
                        <h4><?= fecha_bonita($boletin['fecha_publicacion']) ?></h4>
                    </div>
                    <div class="col-6 p-0">
                        <span><a target="_blank" href="<?= e($boletin['archivo_pdf_url']) ?>" class="facebook"><span class="fa fa-download"></span></a></span>
                        <h4>Ver Boletin</h4>
                    </div>
                    <center><a href="boletines.php" class="btn btn-style btn-primary mt-md-5 mt-4">Ver todos</a></center>
                </div>
            </div>
            <div class="col-lg-4 mt-lg-0 mt-4">
                <img src="<?= e($boletin['foto_portada_url'] ?: 'assets/images/boletin-ntep-45.png') ?>" class="img-fluid radius-image" alt="">
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="w3l-homeblock3 py-5">
    <div class="container py-lg-5 py-md-4">
        <h3 class="title-big mb-5 text-center">Podcast</h3>
        <div class="row">
            <?php foreach ($podcasts as $p) : ?>
            <div class="col-lg-3 col-sm-6">
                <div class="area-box">
                    <h4 class="mb-2"><?= e($p['titulo']) ?></h4>
                    <p><?= e($p['descripcion']) ?></p>
                    <?= mostrar_medio($p, false) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <center><a href="podcasts.php" class="btn btn-style btn-primary mt-md-5 mt-4">Ver todos</a></center>
    </div>
</section>

<section class="w3l-team" id="team">
    <div class="teams1 py-5 mb-3">
        <div class="container py-lg-3 pb-lg-5 pb-4">
            <div class="teams1-content">
                <h3 class="title-big text-center mb-5">Especiales</h3>
                <div class="owl-carousel owl-theme text-center">
                    <?php foreach ($especiales as $esp) : ?>
                    <div class="item">
                        <div class="d-grid team-info">
                            <div class="column position-relative">
                                <?= mostrar_medio($esp, true) ?>
                            </div>
                            <div class="column">
                                <p><?= e($esp['titulo']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="w3l-banner py-0" id="work">
    <div class="midd-w3 py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mt-lg-0 mt-lg-5 about-right-faq align-self">
                    <h5 class="title-small mb-2">DDP Noticias</h5>
                    <h3 class="title-banner">Diálogo y Desarrollo Perú</h3>
                    <p class="mt-4">Somos un espacio de periodismo independiente que busca visibilizar las acciones de diálogo en el país desde una mirada constructiva.</p>
                        <a href="contact.php" class="btn btn-style btn-primary mt-md-5 mt-4">Nosotros</a>
                 </div>
                <div class="col-md-6 left-wthree-img mt-lg-0 mt-4">
                    <div class="position-relative">
                        <img src="assets/images/bannerimg.jpg" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="middle py-5">
    <div class="container py-xl-5 py-lg-3">
        <div class="welcome-left text-center py-md-5 py-3">
            <h3 class="title-big">Síguenos en nuestras Redes Sociales</h3>
            <div class="main-social-footer-29">
            <a target="_blank" href="https://www.facebook.com/DialogoyDesarrolloPeru" class="facebook"><span class="fa fa-facebook-square fa-2x"></span></a>
            <a target="_blank" href="https://www.tiktok.com/@dialogo.y.desarrollo" class="twitter"><img src="assets/images/tiktokg.png"></a>
            <a target="_blank" href="https://www.instagram.com/dialogo.y.desarrollo/" class="instagram"><span class="fa fa-instagram fa-2x"></span></a>
          </div>
        </div>
    </div>
</div>

<section class="w3l-footer-29-main py-5" id="footer">
  <div class="footer-29 py-md-3">
    <div class="container">
      <div class="row footer-top-29">
        <div class="col-lg-6 col-md-6 footer-list-29 footer-1">
          <h6 class="footer-title-29">Quiénes Somos</h6>
          <p>Somos un espacio de periodismo independiente que busca visibilizar las acciones de diálogo en el país desde una mirada constructiva.</p>
          <div class="main-social-footer-29">
            <a target="_blank" href="https://www.facebook.com/DialogoyDesarrolloPeru" class="facebook"><span class="fa fa-facebook-square"></span></a>
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
  <button onclick="topFunction()" id="movetop" title="Go to top">
    <span class="fa fa-angle-up"></span>
  </button>
  <script>
    window.onscroll = function () { scrollFunction() };
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("movetop").style.display = "block";
      } else {
        document.getElementById("movetop").style.display = "none";
      }
    }
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
  </script>
</section>

<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/theme-change.js"></script>
<script src="assets/js/easyResponsiveTabs.js"></script>
<script src="assets/js/owl.carousel.js"></script>
<script>
  $(document).ready(function () {
    $('.owl-logos').owlCarousel({
      loop: true, margin: 0, nav: false, responsiveClass: true,
      autoplay: true, autoplayTimeout: 5000, autoplaySpeed: 1000, autoplayHoverPause: false,
      responsive: { 0: { items: 2 }, 480: { items: 2 }, 568: { items: 3 }, 1000: { items: 5 } }
    })
  })
</script>
<script>
  $(document).ready(function () {
    $('.owl-carousel').owlCarousel({
      loop: true, margin: 0, responsiveClass: true,
      responsive: { 0: { items: 1, nav: true }, 400: { items: 2, nav: true, margin: 20 }, 768: { items: 3, nav: true, margin: 20 }, 1000: { items: 4, nav: true, loop: true, margin: 25 } }
    })
  })
</script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script>
  $(document).ready(function () {
    $('.popup-with-zoom-anim').magnificPopup({
      type: 'inline', fixedContentPos: false, fixedBgPos: true, overflowY: 'auto',
      closeBtnInside: true, preloader: false, midClick: true, removalDelay: 300, mainClass: 'my-mfp-zoom-in'
    });
  });
</script>
<script>
  $(function () {
    $('.navbar-toggler').click(function () { $('body').toggleClass('noscroll'); })
  });
</script>
<script>
  $(window).on("scroll", function () {
    var scroll = $(window).scrollTop();
    if (scroll >= 80) { $("#site-header").addClass("nav-fixed"); } else { $("#site-header").removeClass("nav-fixed"); }
  });
  $(".navbar-toggler").on("click", function () { $("header").toggleClass("active"); });
  $(document).on("ready", function () {
    if ($(window).width() > 991) { $("header").removeClass("active"); }
    $(window).on("resize", function () { if ($(window).width() > 991) { $("header").removeClass("active"); } });
  });
</script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
