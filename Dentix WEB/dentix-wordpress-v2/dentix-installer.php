<?php
/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║           DENTIX — INSTALADOR AUTOMÁTICO v1.0              ║
 * ║      Dentix Productos Dentales, S.L.  ·  dentix.es          ║
 * ╚══════════════════════════════════════════════════════════════╝
 *
 * Sube este archivo a la raíz de tu dominio en Dinahosting.
 * Ábrelo en el navegador: https://www.dentix.es/dentix-installer.php
 * Sigue los 3 pasos. El instalador se borra solo al terminar.
 *
 * REQUISITOS: PHP 8.1+, MySQL 8.0+, ZipArchive, cURL, allow_url_fopen
 */

define('DENTIX_INSTALLER_VERSION', '1.0.0');
define('DENTIX_INSTALLER_KEY',    'dentix2026'); // Clave de seguridad
define('WP_DOWNLOAD_URL',         'https://es.wordpress.org/latest-es_ES.zip');

session_start();
@set_time_limit(300);
@ini_set('memory_limit', '512M');
@ini_set('max_execution_time', 300);

$step    = (int)($_GET['step'] ?? 1);
$action  = $_POST['action'] ?? '';
$errors  = [];
$success = [];
$base    = dirname(__FILE__) . '/';
$siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
         . '://' . $_SERVER['HTTP_HOST'];

// ══ SEGURIDAD BÁSICA ════════════════════════════════════════════
if (isset($_GET['key']) && $_GET['key'] !== DENTIX_INSTALLER_KEY) {
    die('Acceso denegado');
}

// ══ ACCIONES AJAX ═══════════════════════════════════════════════
if ($action === 'check_requirements') {
    header('Content-Type: application/json');
    $checks = dentix_check_requirements();
    echo json_encode($checks);
    exit;
}

if ($action === 'run_install') {
    header('Content-Type: application/json; charset=utf-8');
    $result = dentix_run_install($_POST);
    echo json_encode($result);
    exit;
}

// ══ COMPROBACIONES ══════════════════════════════════════════════
function dentix_check_requirements() {
    return [
        'php'        => version_compare(PHP_VERSION, '8.1', '>="),
        'php_version'=> PHP_VERSION,
        'zip'        => class_exists('ZipArchive'),
        'curl'       => function_exists('curl_init'),
        'pdo_mysql'  => extension_loaded('pdo_mysql') || function_exists('mysqli_connect'),
        'writable'   => is_writable(dirname(__FILE__)),
        'memory'     => intval(ini_get('memory_limit')) >= 128 || ini_get('memory_limit') === '-1',
    ];
}

// ══ INSTALACIÓN PRINCIPAL ═══════════════════════════════════════
function dentix_run_install($post) {
    global $base, $siteUrl;

    $dbHost  = trim($post['db_host']  ?? 'localhost');
    $dbName  = trim($post['db_name']  ?? '');
    $dbUser  = trim($post['db_user']  ?? '');
    $dbPass  = $post['db_pass']  ?? '';
    $dbPfx   = trim($post['db_prefix'] ?? 'dtx_');
    $wpUser  = trim($post['wp_user']  ?? 'admin');
    $wpPass  = $post['wp_pass']  ?? '';
    $wpEmail = trim($post['wp_email'] ?? '');
    $wpTitle = trim($post['wp_title'] ?? 'Dentix Productos Dentales');
    $wpUrl   = rtrim(trim($post['wp_url'] ?? $siteUrl), '/');
    $log     = [];

    // Validar
    if (!$dbName || !$dbUser || !$wpPass || !$wpEmail) {
        return ['ok' => false, 'error' => 'Faltan campos obligatorios'];
    }

    // 1. Comprobar conexión DB
    try {
        $pdo = new PDO("mysql:host={$dbHost};charset=utf8", $dbUser, $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbName}`");
        $log[] = '✅ Conexión a base de datos establecida';
    } catch (Exception $e) {
        return ['ok' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()];
    }

    // 2. Descargar WordPress
    $wpZip = sys_get_temp_dir() . '/wordpress-es.zip';
    if (!file_exists($wpZip) || filesize($wpZip) < 1000000) {
        $log[] = '⏳ Descargando WordPress en español…';
        $ch = curl_init(WP_DOWNLOAD_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $wpData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (!$wpData || $httpCode !== 200) {
            return ['ok' => false, 'error' => "No se pudo descargar WordPress (HTTP $httpCode). Comprueba la conexión del servidor."];
        }
        file_put_contents($wpZip, $wpData);
        $log[] = '✅ WordPress descargado (' . round(filesize($wpZip)/1024/1024, 1) . 'MB)';
    } else {
        $log[] = '✅ WordPress ya descargado (usando caché)';
    }

    // 3. Extraer WordPress
    $zip = new ZipArchive();
    if ($zip->open($wpZip) !== true) {
        return ['ok' => false, 'error' => 'No se pudo abrir el ZIP de WordPress'];
    }
    // Extraer a un directorio temporal
    $tmpDir = sys_get_temp_dir() . '/dentix-wp-extract/';
    if (is_dir($tmpDir)) { dentix_rmdir($tmpDir); }
    mkdir($tmpDir, 0755, true);
    $zip->extractTo($tmpDir);
    $zip->close();

    // Copiar archivos de WP al directorio raíz
    $wpExtracted = $tmpDir . 'wordpress/';
    if (!is_dir($wpExtracted)) {
        return ['ok' => false, 'error' => 'Estructura de WordPress inesperada en el ZIP'];
    }
    dentix_copy_dir($wpExtracted, $base);
    $log[] = '✅ WordPress extraído al servidor';

    // 4. Crear wp-config.php
    $secretKeys = dentix_get_secret_keys();
    $wpConfig = dentix_make_wp_config($dbHost, $dbName, $dbUser, $dbPass, $dbPfx, $secretKeys);
    file_put_contents($base . 'wp-config.php', $wpConfig);
    $log[] = '✅ wp-config.php creado';

    // 5. Crear .htaccess
    $htaccess = '# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress

# Seguridad
<Files wp-config.php>
  Order allow,deny
  Deny from all
</Files>
Options -Indexes';
    file_put_contents($base . '.htaccess', $htaccess);
    $log[] = '✅ .htaccess creado';

    // 6. Instalar WordPress via bootstrap
    define('ABSPATH', $base);
    define('WPINC', 'wp-includes');
    define('DB_NAME',     $dbName);
    define('DB_USER',     $dbUser);
    define('DB_PASSWORD', $dbPass);
    define('DB_HOST',     $dbHost);
    define('DB_CHARSET',  'utf8mb4');
    define('DB_COLLATE',  '');

    try {
        // Cargar WP mínimo para instalar
        require_once $base . 'wp-load.php';
        require_once $base . 'wp-admin/includes/upgrade.php';
        require_once $base . 'wp-includes/wp-db.php';

        // Instalar WP
        $result_wp = wp_install($wpTitle, $wpUser, $wpEmail, false, '', $wpPass, 'es_ES');
        $log[] = '✅ WordPress instalado (usuario: ' . $wpUser . ')';

        // Ajustes básicos
        update_option('siteurl',  $wpUrl);
        update_option('home',     $wpUrl);
        update_option('blogname', $wpTitle);
        update_option('blogdescription', 'Instrumental odontológico profesional B2B');
        update_option('timezone_string',  'Europe/Madrid');
        update_option('date_format',      'd/m/Y');
        update_option('permalink_structure', '/%postname%/');
        update_option('default_comment_status', 'closed');
        update_option('WPLANG', 'es_ES');
        flush_rewrite_rules();

        $log[] = '✅ Ajustes de WordPress configurados';

    } catch (Exception $e) {
        return ['ok' => false, 'error' => 'Error instalando WordPress: ' . $e->getMessage(), 'log' => $log];
    }

    // 7. Instalar tema Dentix
    $themeB64 = DENTIX_THEME_B64;
    $themeZip = sys_get_temp_dir() . '/dentix-theme.zip';
    file_put_contents($themeZip, base64_decode($themeB64));
    $zip = new ZipArchive();
    if ($zip->open($themeZip) === true) {
        $themesDir = $base . 'wp-content/themes/';
        if (!is_dir($themesDir . 'dentix-theme')) {
            $zip->extractTo($themesDir);
        }
        $zip->close();
        $log[] = '✅ Tema Dentix instalado';
    }

    // Activar tema
    try {
        switch_theme('dentix-theme');
        $log[] = '✅ Tema Dentix activado';
    } catch(Exception $e) {
        $log[] = '⚠️ Activar tema manualmente en wp-admin → Apariencia → Temas';
    }

    // 8. Instalar plugin setup
    $setupB64 = DENTIX_SETUP_B64;
    $setupZip = sys_get_temp_dir() . '/dentix-setup.zip';
    file_put_contents($setupZip, base64_decode($setupB64));
    $zip = new ZipArchive();
    if ($zip->open($setupZip) === true) {
        $pluginsDir = $base . 'wp-content/plugins/';
        $zip->extractTo($pluginsDir);
        $zip->close();
        $log[] = '✅ Plugin Dentix Setup instalado';
    }

    // Activar plugin
    try {
        activate_plugin('dentix-setup/dentix-setup.php');
        $log[] = '✅ Plugin Dentix Setup activado';
    } catch(Exception $e) {
        $log[] = '⚠️ Activar plugin manualmente en wp-admin → Plugins';
    }

    // 9. Instalar WooCommerce
    require_once $base . 'wp-admin/includes/plugin-install.php';
    require_once $base . 'wp-admin/includes/class-wp-upgrader.php';
    require_once $base . 'wp-admin/includes/file.php';

    try {
        $api = plugins_api('plugin_information', ['slug' => 'woocommerce', 'fields' => ['downloadlink' => true]]);
        if (!is_wp_error($api)) {
            $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
            $upgrader->install($api->download_link);
            activate_plugin('woocommerce/woocommerce.php');
            $log[] = '✅ WooCommerce instalado y activado';
        }
    } catch(Exception $e) {
        $log[] = '⚠️ Instalar WooCommerce manualmente: wp-admin → Plugins → Añadir WooCommerce';
    }

    // 10. Borrar instalador por seguridad
    @unlink(__FILE__);
    @unlink($wpZip);

    return [
        'ok'      => true,
        'log'     => $log,
        'wp_url'  => $wpUrl . '/wp-admin/',
        'wp_user' => $wpUser,
        'message' => '✅ Instalación completada. Accede a tu wp-admin para finalizar.',
    ];
}

// ══ HELPERS ═════════════════════════════════════════════════════
function dentix_copy_dir($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..') continue;
        $s = $src . $file;
        $d = $dst . $file;
        if (is_dir($s)) {
            dentix_copy_dir($s . '/', $d . '/');
        } else {
            // No sobreescribir wp-config.php si ya existe
            if (basename($d) === 'wp-config.php' && file_exists($d)) continue;
            copy($s, $d);
        }
    }
    closedir($dir);
}

function dentix_rmdir($dir) {
    if (!is_dir($dir)) return;
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        is_dir("$dir/$file") ? dentix_rmdir("$dir/$file") : unlink("$dir/$file");
    }
    rmdir($dir);
}

function dentix_get_secret_keys() {
    $ch = curl_init('https://api.wordpress.org/secret-key/1.1/salt/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $keys = curl_exec($ch);
    curl_close($ch);
    return $keys ?: 'define(\'AUTH_KEY\',         \'' . bin2hex(random_bytes(32)) . '\');
define(\'SECURE_AUTH_KEY\',  \'' . bin2hex(random_bytes(32)) . '\');
define(\'LOGGED_IN_KEY\',    \'' . bin2hex(random_bytes(32)) . '\');
define(\'NONCE_KEY\',        \'' . bin2hex(random_bytes(32)) . '\');
define(\'AUTH_SALT\',        \'' . bin2hex(random_bytes(32)) . '\');
define(\'SECURE_AUTH_SALT\', \'' . bin2hex(random_bytes(32)) . '\');
define(\'LOGGED_IN_SALT\',   \'' . bin2hex(random_bytes(32)) . '\');
define(\'NONCE_SALT\',       \'' . bin2hex(random_bytes(32)) . '\');';
}

function dentix_make_wp_config($host, $name, $user, $pass, $pfx, $keys) {
    return '<?php
/** Dentix — wp-config.php — Generado automáticamente */

// ** Base de datos ** //
define( 'DB_NAME',     \'' . $name . '\' );
define( 'DB_USER',     \'' . $user . '\' );
define( 'DB_PASSWORD', \'' . $pass . '\' );
define( 'DB_HOST',     \'' . $host . '\' );
define( 'DB_CHARSET',  \'utf8mb4\' );
define( 'DB_COLLATE',  \'\' );

// ** Claves de seguridad ** //
' . $keys . '

// ** Tabla prefix ** //
$table_prefix = \'' . $pfx . '\';

// ** Idioma ** //
define( 'WPLANG', \'es_ES\' );

// ** Depuración (desactivada en producción) ** //
define( 'WP_DEBUG',         false );
define( 'WP_DEBUG_LOG',     false );
define( 'WP_DEBUG_DISPLAY', false );

// ** Rendimiento ** //
define( 'WP_MEMORY_LIMIT',       \'512M\' );
define( 'WP_MAX_MEMORY_LIMIT',   \'512M\' );
define( 'COMPRESS_CSS',          true );
define( 'COMPRESS_SCRIPTS',      true );
define( 'CONCATENATE_SCRIPTS',   false );

// ** Seguridad ** //
define( 'DISALLOW_FILE_EDIT',  true );
define( 'FORCE_SSL_ADMIN',     true );
define( 'WP_AUTO_UPDATE_CORE', \'minor\' );

// ** HPOS WooCommerce ** //
define( 'WOOCOMMERCE_FEATURE_CUSTOM_ORDER_TABLES_ENABLED', true );

/* ¡Eso es todo! */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . \'/\' );
}
require_once ABSPATH . 'wp-settings.php';
';
}

// ══ DATOS EMBEBIDOS ═════════════════════════════════════════════
define('DENTIX_THEME_B64', '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');
define('DENTIX_SETUP_B64', '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');

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dentix — Instalador WordPress</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'DM Sans',system-ui,sans-serif;background:#F0EEED;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap');
  .card{background:white;border-radius:20px;box-shadow:0 8px 40px rgba(0,0,0,.10);width:100%;max-width:620px;overflow:hidden}
  .card-head{background:#1A1A1A;padding:32px 40px;position:relative;overflow:hidden}
  .card-head::before{content:'';position:absolute;top:-40px;right:-40px;width:180px;height:180px;background:rgba(192,57,43,.15);border-radius:50%}
  .logo{display:flex;align-items:center;gap:14px;margin-bottom:16px}
  .logo-icon{width:44px;height:44px;position:relative;flex-shrink:0}
  .logo-name{color:white;font-family:'Playfair Display',serif;font-size:22px;letter-spacing:1px}
  .logo-sub{color:#9A9898;font-size:10px;letter-spacing:3px;text-transform:uppercase;margin-top:2px}
  .card-head h1{color:white;font-family:'Playfair Display',serif;font-size:24px;font-weight:700}
  .card-head p{color:#9A9898;font-size:13px;margin-top:6px}
  .card-body{padding:36px 40px}
  .steps{display:flex;gap:0;margin-bottom:32px}
  .step-dot{flex:1;text-align:center;position:relative}
  .step-dot::after{content:'';position:absolute;top:14px;left:50%;width:100%;height:2px;background:#E8E4DC;z-index:0}
  .step-dot:last-child::after{display:none}
  .step-num{width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;position:relative;z-index:1;background:#E8E4DC;color:#6B6868;transition:all .3s}
  .step-num.active{background:#C0392B;color:white}
  .step-num.done{background:#2E7D32;color:white}
  .step-label{font-size:10px;color:#9A9898;margin-top:6px;letter-spacing:.5px;text-transform:uppercase}
  .field{margin-bottom:18px}
  .field label{display:block;font-size:11px;font-weight:700;letter-spacing:.5px;color:#2D2D2D;margin-bottom:7px;text-transform:uppercase}
  .field input{width:100%;padding:11px 14px;border:1.5px solid #E8E4DC;border-radius:9px;font-size:14px;font-family:inherit;color:#1A1A1A;background:#FAFAF9;transition:border-color .2s}
  .field input:focus{outline:none;border-color:#1A1A1A;background:white}
  .field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  .field-hint{font-size:11px;color:#9A9898;margin-top:5px}
  .btn-install{width:100%;padding:15px;background:#C0392B;color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;transition:background .2s;margin-top:8px;display:flex;align-items:center;justify-content:center;gap:10px}
  .btn-install:hover{background:#D44333}
  .btn-install:disabled{background:#9A9898;cursor:not-allowed}
  .notice{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start}
  .notice-ok{background:#E8F5E9;color:#1B5E20;border:1px solid #A5D6A7}
  .notice-err{background:#FFEBEE;color:#B71C1C;border:1px solid #EF9A9A}
  .notice-info{background:#E3F2FD;color:#0D47A1;border:1px solid #90CAF9}
  .notice-warn{background:#FFF8E1;color:#E65100;border:1px solid #FFE082}
  .req-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:24px}
  .req-item{padding:10px 14px;border-radius:8px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:8px}
  .req-ok{background:#E8F5E9;color:#2E7D32}
  .req-fail{background:#FFEBEE;color:#C62828}
  .log-box{background:#1A1A1A;border-radius:10px;padding:20px;margin-top:16px;max-height:260px;overflow-y:auto}
  .log-line{color:#C8C6C4;font-size:12.5px;font-family:monospace;padding:3px 0;line-height:1.6}
  .log-line.ok{color:#69F0AE}
  .log-line.warn{color:#FFD740}
  .log-line.err{color:#FF5252}
  .progress-bar{width:100%;height:6px;background:#E8E4DC;border-radius:3px;overflow:hidden;margin:16px 0}
  .progress-fill{height:100%;background:#C0392B;border-radius:3px;transition:width .5s;width:0%}
  .section-title{font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9A9898;margin:24px 0 14px;padding-bottom:8px;border-bottom:1px solid #E8E4DC}
  .success-icon{font-size:56px;text-align:center;margin-bottom:16px}
  .final-url{background:#F0EEED;border-radius:10px;padding:16px;text-align:center;margin:16px 0}
  .final-url a{color:#C0392B;font-weight:700;font-size:15px;word-break:break-all}
  .hidden{display:none}
  .spinner{width:18px;height:18px;border:2.5px solid rgba(255,255,255,.3);border-top-color:white;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0}
  @keyframes spin{to{transform:rotate(360deg)}}
</style>
</head>
<body>

<div class="card">
  <div class="card-head">
    <div class="logo">
      <svg class="logo-icon" viewBox="0 0 44 44" fill="none">
        <circle cx="20" cy="22" r="18" fill="#1A1A1A" stroke="#444" stroke-width="1"/>
        <circle cx="26" cy="16" r="12" fill="#2D2D2D"/>
        <circle cx="9"  cy="32" r="7"  fill="#C0392B"/>
      </svg>
      <div>
        <div class="logo-name">dentix</div>
        <div class="logo-sub">Productos dentales, S.L.</div>
      </div>
    </div>
    <h1>Instalador de WordPress</h1>
    <p>Instala Dentix · WooCommerce · Tema · Configuración B2B en un clic</p>
  </div>

  <div class="card-body">

    <!-- Indicador de pasos -->
    <div class="steps">
      <div class="step-dot">
        <div class="step-num active" id="dot1">1</div>
        <div class="step-label">Requisitos</div>
      </div>
      <div class="step-dot">
        <div class="step-num" id="dot2">2</div>
        <div class="step-label">Configurar</div>
      </div>
      <div class="step-dot">
        <div class="step-num" id="dot3">3</div>
        <div class="step-label">Instalar</div>
      </div>
    </div>

    <!-- Paso 1: Requisitos -->
    <div id="step1">
      <div class="notice notice-info">
        <span>ℹ️</span>
        <div>Este instalador descargará WordPress 6.x en español desde wordpress.org,
        instalará el tema Dentix y configurará WooCommerce para venta B2B en España.</div>
      </div>
      <div id="reqChecks">
        <div style="text-align:center;padding:20px;color:#9A9898;font-size:13px">
          <div class="spinner" style="margin:0 auto 10px;border-color:rgba(0,0,0,.15);border-top-color:#C0392B"></div>
          Comprobando requisitos del servidor…
        </div>
      </div>
      <button class="btn-install hidden" id="btnStep1" onclick="goStep2()">
        Continuar → Configurar base de datos
      </button>
    </div>

    <!-- Paso 2: Formulario -->
    <div id="step2" class="hidden">
      <div class="section-title">Base de datos MySQL</div>
      <div class="notice notice-warn" style="margin-bottom:16px">
        <span>⚠️</span>
        <div>Crea primero la base de datos en el panel de Dinahosting: <strong>Base de datos → MySQL → Nueva base de datos</strong></div>
      </div>
      <div class="field-row">
        <div class="field">
          <label>Host de la base de datos</label>
          <input type="text" id="db_host" value="localhost" placeholder="localhost">
          <div class="field-hint">Normalmente "localhost" en Dinahosting</div>
        </div>
        <div class="field">
          <label>Nombre de la base de datos *</label>
          <input type="text" id="db_name" placeholder="dentix_db" required>
        </div>
      </div>
      <div class="field-row">
        <div class="field">
          <label>Usuario MySQL *</label>
          <input type="text" id="db_user" placeholder="dentix_user" required>
        </div>
        <div class="field">
          <label>Contraseña MySQL *</label>
          <input type="password" id="db_pass" placeholder="••••••••" required>
        </div>
      </div>
      <div class="field" style="max-width:180px">
        <label>Prefijo de tablas</label>
        <input type="text" id="db_prefix" value="dtx_" placeholder="dtx_">
        <div class="field-hint">Recomendado: no usar "wp_"</div>
      </div>

      <div class="section-title">Configuración del sitio</div>
      <div class="field">
        <label>URL del sitio *</label>
        <input type="url" id="wp_url" value="<?php echo esc_attr($siteUrl); ?>" placeholder="https://www.dentix.es" required>
      </div>
      <div class="field">
        <label>Nombre del sitio</label>
        <input type="text" id="wp_title" value="Dentix Productos Dentales" placeholder="Dentix Productos Dentales">
      </div>

      <div class="section-title">Cuenta de administrador</div>
      <div class="field-row">
        <div class="field">
          <label>Usuario admin *</label>
          <input type="text" id="wp_user" placeholder="dentixadmin" required>
          <div class="field-hint">No uses "admin" (es inseguro)</div>
        </div>
        <div class="field">
          <label>Contraseña admin *</label>
          <input type="password" id="wp_pass" placeholder="Mínimo 12 caracteres" required>
        </div>
      </div>
      <div class="field">
        <label>Email del administrador *</label>
        <input type="email" id="wp_email" placeholder="admin@dentix.es" required>
      </div>

      <button class="btn-install" id="btnStep2" onclick="runInstall()">
        🚀 Instalar Dentix WordPress completo
      </button>
    </div>

    <!-- Paso 3: Progreso e resultado -->
    <div id="step3" class="hidden">
      <div id="installing">
        <div class="notice notice-info">
          <div class="spinner"></div>
          <div id="statusMsg">Iniciando instalación… Esto puede tardar 30–90 segundos.</div>
        </div>
        <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>
        <div class="log-box" id="logBox">
          <div class="log-line">⏳ Conectando con el servidor…</div>
        </div>
      </div>
      <div id="installDone" class="hidden">
        <div class="success-icon">🎉</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:24px;text-align:center;color:#1A1A1A;margin-bottom:8px">
          ¡Instalación completada!
        </h2>
        <p style="text-align:center;color:#6B6868;font-size:14px;margin-bottom:16px">
          WordPress, el tema Dentix y WooCommerce están instalados y configurados.
        </p>
        <div class="final-url">
          <div style="font-size:11px;color:#9A9898;margin-bottom:6px;letter-spacing:1px;text-transform:uppercase">Accede a tu panel</div>
          <a href="" id="wpAdminUrl" target="_blank"></a>
        </div>
        <div class="notice notice-warn" style="margin-top:12px">
          <span>⚠️</span>
          <div><strong>Próximos pasos:</strong> Entra al panel → Herramientas → Dentix Setup → Ejecutar configuración → Instalar WooCommerce → Configurar GetNet y SAGE 50.</div>
        </div>
        <div class="log-box" id="logBoxDone" style="margin-top:16px"></div>
      </div>
      <div id="installError" class="hidden">
        <div class="notice notice-err">
          <span>❌</span>
          <div id="errorMsg">Error durante la instalación.</div>
        </div>
        <div class="log-box" id="logBoxErr"></div>
        <button class="btn-install" style="margin-top:16px;background:#1A1A1A" onclick="location.href=location.pathname">
          ← Volver e intentar de nuevo
        </button>
      </div>
    </div>

  </div><!-- /card-body -->
</div><!-- /card -->

<script>
const self = location.href.split('?')[0];

// ── Paso 1: Comprobar requisitos ─────────────────────────────
async function checkReqs() {
  try {
    const r = await fetch(self + '?action=check_requirements', {method:'POST'});
    const d = await r.json();
    const checks = [
      {key:'php',       label:`PHP 8.1+ (tienes ${d.php_version})`},
      {key:'zip',       label:'Extensión ZipArchive'},
      {key:'curl',      label:'Extensión cURL'},
      {key:'pdo_mysql', label:'MySQL / PDO MySQL'},
      {key:'writable',  label:'Carpeta raíz escribible'},
      {key:'memory',    label:'Memoria PHP ≥128MB'},
    ];
    let html = '<div class="req-grid">';
    let allOk = true;
    checks.forEach(c => {
      const ok = d[c.key];
      if (!ok) allOk = false;
      html += `<div class="req-item ${ok ? 'req-ok' : 'req-fail'}">${ok ? '✅' : '❌'} ${c.label}</div>`;
    });
    html += '</div>';
    document.getElementById('reqChecks').innerHTML = html;
    const btn = document.getElementById('btnStep1');
    btn.classList.remove('hidden');
    if (!allOk) {
      btn.textContent = 'Continuar igualmente (puede fallar)';
      btn.style.background = '#666';
    }
  } catch(e) {
    document.getElementById('reqChecks').innerHTML = '<div class="notice notice-err">❌ Error comprobando requisitos: ' + e.message + '</div>';
  }
}

function goStep2() {
  document.getElementById('step1').classList.add('hidden');
  document.getElementById('step2').classList.remove('hidden');
  document.getElementById('dot1').classList.remove('active'); document.getElementById('dot1').classList.add('done');
  document.getElementById('dot2').classList.add('active');
}

async function runInstall() {
  // Validar
  const required = ['db_name','db_user','db_pass','wp_url','wp_user','wp_pass','wp_email'];
  for (const id of required) {
    if (!document.getElementById(id).value.trim()) {
      alert('Por favor rellena todos los campos obligatorios.');
      document.getElementById(id).focus();
      return;
    }
  }

  // Mostrar paso 3
  document.getElementById('step2').classList.add('hidden');
  document.getElementById('step3').classList.remove('hidden');
  document.getElementById('dot2').classList.remove('active'); document.getElementById('dot2').classList.add('done');
  document.getElementById('dot3').classList.add('active');

  const body = new FormData();
  body.append('action',    'run_install');
  body.append('db_host',   document.getElementById('db_host').value);
  body.append('db_name',   document.getElementById('db_name').value);
  body.append('db_user',   document.getElementById('db_user').value);
  body.append('db_pass',   document.getElementById('db_pass').value);
  body.append('db_prefix', document.getElementById('db_prefix').value);
  body.append('wp_url',    document.getElementById('wp_url').value);
  body.append('wp_title',  document.getElementById('wp_title').value);
  body.append('wp_user',   document.getElementById('wp_user').value);
  body.append('wp_pass',   document.getElementById('wp_pass').value);
  body.append('wp_email',  document.getElementById('wp_email').value);

  // Animar la barra de progreso
  let pct = 0;
  const pbar = document.getElementById('progressFill');
  const interval = setInterval(() => {
    pct = Math.min(pct + (pct < 70 ? 2 : 0.3), 90);
    pbar.style.width = pct + '%';
  }, 600);

  const steps = [
    'Verificando base de datos…',
    'Descargando WordPress en español…',
    'Extrayendo WordPress…',
    'Creando wp-config.php…',
    'Instalando WordPress…',
    'Instalando tema Dentix…',
    'Instalando WooCommerce…',
    'Configurando tienda B2B…',
  ];
  let si = 0;
  const msgInterval = setInterval(() => {
    if (si < steps.length) {
      document.getElementById('statusMsg').textContent = steps[si++];
      addLog('⏳ ' + steps[si-1]);
    }
  }, 8000);

  try {
    const r = await fetch(self, {method:'POST', body});
    const d = await r.json();

    clearInterval(interval);
    clearInterval(msgInterval);
    pbar.style.width = '100%';
    pbar.style.background = d.ok ? '#2E7D32' : '#C62828';

    if (d.ok) {
      document.getElementById('installing').classList.add('hidden');
      document.getElementById('installDone').classList.remove('hidden');
      const url = d.wp_url;
      const el = document.getElementById('wpAdminUrl');
      el.href = url;
      el.textContent = url;
      if (d.log) {
        const box = document.getElementById('logBoxDone');
        d.log.forEach(l => { const div = document.createElement('div'); div.className='log-line ok'; div.textContent=l; box.appendChild(div); });
      }
      document.getElementById('dot3').classList.remove('active'); document.getElementById('dot3').classList.add('done');
    } else {
      document.getElementById('installing').classList.add('hidden');
      document.getElementById('installError').classList.remove('hidden');
      document.getElementById('errorMsg').textContent = d.error || 'Error desconocido';
      if (d.log) {
        const box = document.getElementById('logBoxErr');
        d.log.forEach(l => { const div = document.createElement('div'); div.className='log-line'; div.textContent=l; box.appendChild(div); });
      }
    }
  } catch(e) {
    clearInterval(interval);
    clearInterval(msgInterval);
    document.getElementById('installing').classList.add('hidden');
    document.getElementById('installError').classList.remove('hidden');
    document.getElementById('errorMsg').textContent = 'Error de red: ' + e.message;
  }
}

function addLog(msg) {
  const box = document.getElementById('logBox');
  const div = document.createElement('div');
  div.className = 'log-line' + (msg.includes('✅') ? ' ok' : msg.includes('⚠') ? ' warn' : '');
  div.textContent = msg;
  box.appendChild(div);
  box.scrollTop = box.scrollHeight;
}

// Arrancar comprobación de requisitos
checkReqs();
</script>
</body>
</html>
