<?php
// ============================================================
// CONFIGURACION GLOBAL DEL PANEL ADMIN
// Base de datos: dialogoydesarrollo
// ============================================================

session_start();

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'dialogoydesarrollo');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('APP_NAME', 'Dialogo y Desarrollo');
define('APP_LABEL', 'Panel de Administracion');
define('APP_BASE', str_replace('\\', '/', dirname(__DIR__)));

error_reporting(E_ALL);
ini_set('display_errors', '1');

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
    die(
        'No se pudo conectar a la base de datos <b>' . DB_NAME . '</b>.<br>' .
        '1) Inicia MySQL (XAMPP/WAMP).<br>' .
        '2) Importa el archivo <b>dialogoydesarrollo.sql</b>.<br>' .
        'Detalle: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
    );
}

/**
 * Redirige a una URL.
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

/**
 * Bloquea el acceso si no hay sesion iniciada.
 */
function require_login()
{
    if (empty($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

/**
 * URL base del sitio publico (raiz del proyecto, no /admin).
 */
function app_base_url()
{
    static $base = null;
    if ($base === null) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $dir = APP_BASE;
        $root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
        if ($root !== '' && strpos($dir, $root) === 0) {
            $sub = rtrim(substr($dir, strlen($root)), '/');
        } else {
            $sub = '';
        }
        $base = $scheme . '://' . $host . $sub;
    }
    return $base;
}

/**
 * Resuelve una ruta de recurso (imagen/pdf grabada en BD como relativa al sitio).
 */
function app_asset($path)
{
    $path = (string) $path;
    if ($path === '') return '';
    if (preg_match('#^(https?:)?//#i', $path)) return $path;
    return app_base_url() . '/' . ltrim($path, '/');
}

/**
 * Detecta si un campo del formulario es de imagen, PDF o video/audio local
 * (para subida de archivos). Devuelve 'img', 'pdf', 'media' o null.
 */
function detectar_tipo_recurso($campo)
{
    $campo = (string) $campo;
    if ($campo === 'archivo_url') return 'media'; // podcast/video subido desde el PC
    if (preg_match('/pdf|archivo/i', $campo)) return 'pdf';
    if (preg_match('/(^|_)(foto|imagen|avatar|icono)(_|$)/i', $campo) || in_array($campo, ['url_foto', 'url_imagen', 'portada_url', 'url_portada'], true)) return 'img';
    return null;
}

/**
 * Comprime una imagen con GD intentando que su peso no supere $targetKb.
 * Escala de forma progresiva y baja la calidad hasta lograrlo.
 * Reescribe el archivo en la misma ruta. Devuelve true si tuvo exito.
 */
function comprimir_imagen($ruta, $targetKb = 0)
{
    if (!function_exists('imagecreatefromjpeg')) return true;
    if ($targetKb <= 0 || !is_file($ruta)) return true;

    $info = @getimagesize($ruta);
    if (!$info) return false;

    $mime = $info['mime'];
    $ancho = (int) $info[0];
    $alto  = (int) $info[1];
    $esPng  = $mime === 'image/png';
    $esGif  = $mime === 'image/gif';
    $esWebp = $mime === 'image/webp';

    switch ($mime) {
        case 'image/jpeg':
            $im = @imagecreatefromjpeg($ruta);
            break;
        case 'image/png':
            $im = @imagecreatefrompng($ruta);
            break;
        case 'image/gif':
            $im = @imagecreatefromgif($ruta);
            break;
        case 'image/webp':
            $im = @imagecreatefromwebp($ruta);
            break;
        default:
            return true;
    }
    if (!$im) return false;

    // Escala inicial: maximo 1600px en el lado mayor.
    $maxLado = 1600;
    if ($ancho > $maxLado || $alto > $maxLado) {
        $escala = min($maxLado / $ancho, $maxLado / $alto);
        $nw = (int) round($ancho * $escala);
        $nh = (int) round($alto * $escala);
        $nuevo = imagecreatetruecolor($nw, $nh);
        if ($esPng) {
            imagealphablending($nuevo, false);
            imagesavealpha($nuevo, true);
            $t = imagecolorallocatealpha($nuevo, 0, 0, 0, 127);
            imagefill($nuevo, 0, 0, $t);
        }
        imagecopyresampled($nuevo, $im, 0, 0, 0, 0, $nw, $nh, $ancho, $alto);
        imagedestroy($im);
        $im = $nuevo;
        $ancho = $nw;
        $alto = $nh;
    }

    $calidad = 85;
    $tmp = $ruta . '.compr';
    $intentos = 0;
    $ultimoDest = '';

    while ($intentos++ < 40) {
        $dest = $tmp . $intentos . (($esPng || $esGif) ? '.png' : '.jpg');
        if ($esPng) {
            $ok = imagepng($im, $dest);
        } elseif ($esWebp) {
            $ok = imagewebp($im, $dest, max(30, $calidad));
        } else {
            $ok = imagejpeg($im, $dest, max(30, $calidad));
        }
        if (!$ok) {
            @unlink($dest);
            break;
        }
        // Ultimo archivo realmente escrito (se usa si no se alcanza el objetivo).
        $ultimoDest = $dest;
        $bytes = (int) filesize($dest);
        if ($bytes <= $targetKb * 1024) {
            // Cumple el peso objetivo: se queda.
        } elseif ($esPng || $esGif) {
            // PNG/GIF no admiten calidad: se escala la imagen.
            if ($ancho <= 320) {
                // Ya no conviene reducir mas; se usa el ultimo intento.
            } else {
                @unlink($dest);
                $escala = 0.85;
                $ancho = max(320, (int) round($ancho * $escala));
                $alto  = max(320, (int) round($alto * $escala));
                $nuevo = imagecreatetruecolor($ancho, $alto);
                if ($esPng) {
                    imagealphablending($nuevo, false);
                    imagesavealpha($nuevo, true);
                    $t = imagecolorallocatealpha($nuevo, 0, 0, 0, 127);
                    imagefill($nuevo, 0, 0, $t);
                }
                imagecopyresampled($nuevo, $im, 0, 0, 0, 0, $ancho, $alto, imagesx($im), imagesy($im));
                imagedestroy($im);
                $im = $nuevo;
                continue;
            }
        } else {
            // JPEG/WebP: bajar calidad y, de no alcanzar, reducir dimensiones.
            if ($calidad <= 35) {
                if ($ancho <= 480) {
                    // Limite alcanzado: se usa el ultimo intento.
                } else {
                    @unlink($dest);
                    $escala = 0.8;
                    $ancho = max(480, (int) round($ancho * $escala));
                    $alto  = max(480, (int) round($alto * $escala));
                    $nuevo = imagecreatetruecolor($ancho, $alto);
                    if ($esPng) {
                        imagealphablending($nuevo, false);
                        imagesavealpha($nuevo, true);
                        $t = imagecolorallocatealpha($nuevo, 0, 0, 0, 127);
                        imagefill($nuevo, 0, 0, $t);
                    }
                    imagecopyresampled($nuevo, $im, 0, 0, 0, 0, $ancho, $alto, imagesx($im), imagesy($im));
                    imagedestroy($im);
                    $im = $nuevo;
                    $calidad = 80;
                    continue;
                }
            } else {
                @unlink($dest);
                $calidad -= 15;
                continue;
            }
        }

        if (is_file($dest) && @rename($dest, $ruta)) {
            imagedestroy($im);
            // Limpia temporales sobrantes.
            for ($i = 1; $i <= 40; $i++) {
                @unlink($tmp . $i . '.jpg');
                @unlink($tmp . $i . '.png');
            }
            return true;
        }
        @unlink($dest);
        break;
    }

    // Si no se alcanzo el peso objetivo, queda el ultimo intento escrito
    // (que es el mas comprimido) en lugar del archivo original.
    if ($ultimoDest !== '' && is_file($ultimoDest) && @rename($ultimoDest, $ruta)) {
        imagedestroy($im);
        for ($i = 1; $i <= 40; $i++) {
            @unlink($tmp . $i . '.jpg');
            @unlink($tmp . $i . '.png');
        }
        return is_file($ruta);
    }

    imagedestroy($im);
    return is_file($ruta);
}

/**
 * Guarda un archivo subido por formulario en la carpeta correcta del sitio
 * (assets/images para imagenes, assets/videos para video/audio local, pdfs para PDF)
 * y devuelve su ruta relativa lista para guardar en la BD.
 * Con $pesoKb > 0 las imagenes se comprimen para que pesen <= $pesoKb KB.
 * Devuelve false en caso de error.
 */
function manejar_subida($file, $campo, $pesoKb = 0)
{
    $tipo = detectar_tipo_recurso($campo);
    if (!$tipo || !is_array($file)) return false;

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > 15 * 1024 * 1024) return false;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($tipo === 'img') {
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) return false;
    } elseif ($tipo === 'media') {
        if (!in_array($ext, ['mp4', 'mov', 'webm', 'mp3', 'm4a', 'aac', 'ogg', 'wav'], true)) return false;
    } else {
        if ($ext !== 'pdf') return false;
    }

    $dir = ($tipo === 'img') ? 'assets/images' : (($tipo === 'media') ? 'assets/videos' : 'pdfs');
    $base = APP_BASE . '/' . $dir;
    if (!is_dir($base)) {
        if (!@mkdir($base, 0777, true)) return false;
    }

    $nombreBase = strtolower(preg_replace('/[^a-z0-9]+/i', '-', pathinfo($file['name'], PATHINFO_FILENAME)));
    $nombreBase = trim($nombreBase, '-') ?: 'archivo';
    $nombre = $nombreBase . '-' . date('Ymd-His') . '.' . $ext;
    $destino = $base . '/' . $nombre;

    if (!move_uploaded_file($file['tmp_name'], $destino)) return false;

    if ($tipo === 'img') {
        comprimir_imagen($destino, (int) $pesoKb);
    }

    return $dir . '/' . $nombre;
}

/**
 * Escapa una cadena para HTML segura.
 */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Establece / recupera un mensaje flash (una sola vez).
 */
function flash($message = null, $type = 'success')
{
    if ($message !== null) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    } else {
        if (isset($_SESSION['flash_message'])) {
            $f = ['msg' => $_SESSION['flash_message'], 'type' => $_SESSION['flash_type']];
            unset($_SESSION['flash_message'], $_SESSION['flash_type']);
            return $f;
        }
        return null;
    }
}

/**
 * Lista de usuarios para selects (user_id => nombre + rol).
 */
function lista_usuarios()
{
    global $pdo;
    $stmt = $pdo->query('SELECT user_id, nombre_completo, rol, activo FROM usuarios ORDER BY nombre_completo');
    return $stmt->fetchAll();
}

/**
 * Lista de autores para selects (autor_id => nombre apellidos).
 */
function lista_autores()
{
    global $pdo;
    $stmt = $pdo->query('SELECT autor_id, nombre, apellidos FROM autores ORDER BY nombre');
    return $stmt->fetchAll();
}

/**
 * Lista de reportajes para selects (reportaje_id => titulo).
 */
function lista_reportajes()
{
    global $pdo;
    $stmt = $pdo->query('SELECT reportaje_id, titulo FROM reportajes ORDER BY titulo');
    return $stmt->fetchAll();
}