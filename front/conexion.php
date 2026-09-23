<?php
// Conexion local (XAMPP) + produccion (InfinityFree).
// En InfinityFree no hay variables de entorno: se autodetecta por HTTP_HOST.
$isProd = isset($_SERVER['HTTP_HOST']) && (
    strpos($_SERVER['HTTP_HOST'], 'wuaze.com') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'infinityfree') !== false
);
if ($isProd) {
    define('DB_HOST', 'sql309.infinityfree.com');
    define('DB_NAME', 'if0_42992639_dialogo');
    define('DB_USER', 'if0_42992639');
    define('DB_PASS', 'QG6CHMtdkqgeQZY');
} else {
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_NAME', getenv('DB_NAME') ?: 'dialogoydesarrollo');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
}

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ]
    );
} catch (PDOException $e) {
    die('Error de conexion: ' . $e->getMessage());
}

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function fecha_bonita($fecha) {
    $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $t = strtotime($fecha);
    return $meses[(int)date('m', $t)-1] . ' ' . date('d', $t) . ', ' . date('Y', $t);
}

function fecha_es($fecha) {
    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $t = strtotime($fecha);
    return date('d', $t) . ' de ' . $meses[(int)date('m', $t)-1] . ' de ' . date('Y', $t);
}

/**
 * Renderiza el desarrollo de un reportaje. Si el texto contiene HTML
 * (editor tipo Word) se muestra tal cual; si es texto plano (registros
 * antiguos) se convierte en parrafos. Elimina script e iframes por seguridad.
 */
function mostrar_html($texto)
{
    $texto = (string) $texto;
    if ($texto === '') return '';

    $limpio = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $texto);
    $limpio = preg_replace('#<(/?)(script|iframe|object|embed)\b[^>]*>#is', '', $limpio);
    $limpio = preg_replace('#\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $limpio);

    if (preg_match('/<(p|div|h[1-6]|ul|ol|li|blockquote|br|img|table)\b/i', $limpio)) {
        return $limpio; // ya es HTML del editor
    }

    // Texto plano antiguo -> parrafos
    $parrafos = array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', $limpio)), fn($p) => $p !== ''));
    if (!$parrafos) $parrafos = [trim($limpio)];
    $out = '';
    foreach ($parrafos as $p) {
        $out .= '<p align="justify" class="mb-4">' . nl2br(e($p)) . '</p>';
    }
    return $out;
}

/**
 * Reproduce un podcast/video embebido en la propia pagina:
 *  - archivo local (assets/videos)  -> <audio> o <video controls>
 *  - url_embed                      -> iframe (YouTube, etc.)
 */
function mostrar_medio($f, $esVideo = false)
{
    $esVideo = (bool) $esVideo;
    $archivo = (string) ($f['archivo_url'] ?? '');
    $embed   = (string) ($f['url_embed'] ?? '');

    if ($archivo !== '') {
        $archivo = (strpos($archivo, 'http') === 0 || strpos($archivo, '//') === 0) ? $archivo : ltrim($archivo, '/');
        $ext = strtolower(pathinfo(parse_url($archivo, PHP_URL_PATH) ?: $archivo, PATHINFO_EXTENSION));
        if (in_array($ext, ['mp3', 'm4a', 'aac', 'ogg', 'wav'], true) || !$esVideo) {
            return '<div class="mediabox audio mt-3"><audio controls preload="metadata" style="width:100%"><source src="' . e($archivo) . '"></audio></div>';
        }
        return '<div class="embed-responsive embed-responsive-16by9 mt-3"><video controls preload="metadata" class="embed-responsive-item" src="' . e($archivo) . '"></video></div>';
    }

    if ($embed !== '') {
        return '<div class="embed-responsive embed-responsive-16by9 mt-3"><iframe class="embed-responsive-item" src="' . e($embed) . '" allowfullscreen></iframe></div>';
    }
    return '';
}
