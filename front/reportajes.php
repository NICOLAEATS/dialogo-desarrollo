<?php require_once 'conexion.php';

$porPagina = 9;
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$offset = ($pagina - 1) * $porPagina;

$total = (int) $pdo->query("SELECT COUNT(*) FROM reportajes WHERE activo = 1 AND estado = 'publicado'")->fetchColumn();
$paginas = max(1, (int) ceil($total / $porPagina));

$stmt = $pdo->prepare(
    "SELECT r.*, CONCAT(a.nombre,' ',a.apellidos) AS autor,
            COALESCE(NULLIF(r.foto_portada_url,''),
                     (SELECT url_foto FROM reportajes_fotos WHERE reportaje_id = r.reportaje_id AND es_principal = 1 AND activo = 1 LIMIT 1),
                     'assets/images/video.jpg') AS foto
     FROM reportajes r
     LEFT JOIN autores a ON a.autor_id = r.autor_id
     WHERE r.activo = 1 AND r.estado = 'publicado'
     ORDER BY r.fecha_publicacion DESC
     LIMIT ? OFFSET ?"
);
$stmt->execute([$porPagina, $offset]);
$reportajes = $stmt->fetchAll();
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reportajes - Diálogo y Desarrollo Perú</title>
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
        <div class="row"><div class="col-md-12">
            <div class="breadcrumb-contents"><h2 class="title-big">Reportajes</h2></div>
        </div></div>
    </div>
</section>

<section class="grids-block-5 py-5">
    <div class="container">
        <div class="row">
            <?php foreach ($reportajes as $r) : ?>
            <div class="col-lg-4 col-md-6 grids5-info mt-5">
                <a href="reportaje.php?id=<?= (int)$r['reportaje_id'] ?>" class="d-block"><img src="<?= e($r['foto']) ?>" alt="" class="img-fluid" /></a>
                <div class="blog-info">
                    <h5><?= fecha_bonita($r['fecha_publicacion']) ?></h5>
                    <h4><a href="reportaje.php?id=<?= (int)$r['reportaje_id'] ?>" class="d-block"><?= e($r['titulo']) ?></a></h4>
                    <p><?= e(substr($r['resumen_corto'], 0, 150)) ?>...</p>
                    <a href="reportaje.php?id=<?= (int)$r['reportaje_id'] ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if ($paginas > 1) : ?>
        <div class="pagination">
            <ul>
                <?php if ($pagina > 1) : ?>
                <li class="prev"><a href="reportajes.php?pagina=<?= $pagina - 1 ?>">Ant</a></li>
                <?php endif; ?>
                <?php for ($p = 1; $p <= $paginas; $p++) : ?>
                <li><a href="reportajes.php?pagina=<?= $p ?>" class="<?= $p === $pagina ? 'active' : '' ?>"><?= $p ?></a></li>
                <?php endfor; ?>
                <?php if ($pagina < $paginas) : ?>
                <li class="next"><a href="reportajes.php?pagina=<?= $pagina + 1 ?>">Sig</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>
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
