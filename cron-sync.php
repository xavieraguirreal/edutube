<?php
/**
 * EduTube — Sincronización automática por cron
 *
 * Sincroniza todos los canales que tengan auto_sync = 1.
 * Se puede ejecutar via CLI o via curl con token.
 *
 * Crontab:
 *   curl -A "Mozilla/5.0" --silent "https://edutube.universidadliberte.org/cron-sync.php?token=edutube-sync-2026"
 */

// Protección: solo permitir CLI o requests con token secreto
$cronToken = 'edutube-sync-2026';
if (php_sapi_name() !== 'cli' && ($_GET['token'] ?? '') !== $cronToken) {
    http_response_code(403);
    die('Acceso denegado.');
}

// Capturar TODOS los errores en archivo local
$logFile = __DIR__ . '/cron-sync.log';
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/cron-error.log');
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . " | PHP ERROR: $errstr en $errfile:$errline\n", FILE_APPEND);
});
set_exception_handler(function($e) {
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . " | EXCEPCION: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine() . "\n", FILE_APPEND);
});

set_time_limit(600);
ini_set('memory_limit', '256M');

// Evitar ejecuciones simultáneas
$lockFile = __DIR__ . '/cron-sync.lock';
if (file_exists($lockFile)) {
    $lockTime = filemtime($lockFile);
    if (time() - $lockTime < 300) { // 5 minutos
        die('Otra ejecución en curso. Esperá a que termine.');
    }
}
file_put_contents($lockFile, date('Y-m-d H:i:s'));
register_shutdown_function(function() use ($lockFile) { @unlink($lockFile); });

// Log: buffer + archivo local
$logBuffer = [];
function logMsg($msg) {
    global $logBuffer, $logFile;
    $line = date('Y-m-d H:i:s') . " | $msg";
    $logBuffer[] = $line;
    file_put_contents($logFile, $line . "\n", FILE_APPEND);
}

file_put_contents($logFile, date('Y-m-d H:i:s') . " | === Script iniciado ===\n", FILE_APPEND);

require_once __DIR__ . '/config.php';

$db = getDB();
$startTime = time();

logMsg("=== Inicio sincronización automática ===");

// Obtener canales con auto_sync activado
$canales = $db->query("SELECT * FROM canales WHERE auto_sync = 1 AND activo = 1")->fetchAll();

if (empty($canales)) {
    logMsg("No hay canales con auto_sync activado.");
    outputLog();
    exit(0);
}

logMsg(count($canales) . " canal(es) para sincronizar.");

$totalVideos = 0;
$totalPlaylists = 0;
$maxVideosTotal = 50; // Límite global por ejecución del cron
$videosRestantes = $maxVideosTotal;

foreach ($canales as $canal) {
    // Si se excedió la cuota, parar
    if (!empty($GLOBALS['youtube_quota_exceeded'])) {
        logMsg("CUOTA EXCEDIDA — se detiene la sincronización. Se resetea a las 4 AM Argentina.");
        break;
    }

    // Si ya importamos el máximo, parar
    if ($videosRestantes <= 0) {
        logMsg("Límite de $maxVideosTotal videos alcanzado. Ejecutá de nuevo para continuar.");
        break;
    }

    $channelId = $canal['youtube_channel_id'];
    if (empty($channelId)) {
        logMsg("[{$canal['nombre']}] Sin YouTube Channel ID, omitido.");
        continue;
    }

    $canalStart = time();
    logMsg("[{$canal['nombre']}] Iniciando sync (máx $videosRestantes videos)...");

    try {
        $result = syncChannelAll(
            $db,
            $channelId,
            $canal['id'],
            $canal['default_categoria_id'],
            'cron',
            $videosRestantes
        );

        $totalVideos += $result['imported'];
        $totalPlaylists += $result['playlists_imported'];
        $videosRestantes -= $result['imported'];

        // Registrar en sync_log
        $errores = !empty($result['errors']) ? implode('; ', $result['errors']) : null;
        $stmt = $db->prepare("INSERT INTO sync_log (canal_id, videos_importados, playlists_importadas, errores) VALUES (?, ?, ?, ?)");
        $stmt->execute([$canal['id'], $result['imported'], $result['playlists_imported'], $errores]);

        $canalElapsed = time() - $canalStart;
        $status = "{$result['imported']} videos, {$result['playlists_imported']} playlists ({$canalElapsed}s)";
        if ($result['hit_limit']) $status .= " (límite alcanzado)";
        if ($errores) $status .= " | ERRORES: $errores";
        logMsg("[{$canal['nombre']}] $status");

    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
        logMsg("[{$canal['nombre']}] ERROR: $errorMsg");

        // Registrar error en sync_log
        $stmt = $db->prepare("INSERT INTO sync_log (canal_id, videos_importados, playlists_importadas, errores) VALUES (?, 0, 0, ?)");
        $stmt->execute([$canal['id'], $errorMsg]);
    }
}

$elapsed = time() - $startTime;
logMsg("=== Fin: $totalVideos videos, $totalPlaylists playlists en {$elapsed}s ===");

// Resumen de llamadas API por key
$apiStats = youtubeApiGetStats();
if (!empty($apiStats)) {
    $totalCalls = array_sum($apiStats);
    $parts = [];
    foreach ($apiStats as $key => $count) {
        $parts[] = "...$key: $count calls";
    }
    logMsg("API CALLS: $totalCalls total (" . implode(' | ', $parts) . ")");
    logMsg("Cuota restante estimada: " . (30000 - $totalCalls) . " de 30,000/día (3 keys)");
}

outputLog();

function outputLog() {
    global $logBuffer;
    if (php_sapi_name() === 'cli') {
        foreach ($logBuffer as $line) fwrite(STDOUT, $line . "\n");
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo implode("\n", $logBuffer) . "\n";
    }
}
