<?php
/**
 * EduTube — Sincronización automática por cron
 *
 * Sincroniza todos los canales que tengan auto_sync = 1.
 * Ejecutar desde CLI: php /path/to/cron-sync.php
 *
 * Crontab ejemplo (cada 6 horas):
 *   0 */6 * * * /usr/bin/php /path/to/edutube/cron-sync.php >> /var/log/edutube-sync.log 2>&1
 */

// Solo permitir ejecución desde CLI
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Acceso denegado. Solo ejecución por CLI.');
}

set_time_limit(600);
ini_set('memory_limit', '256M');

require_once __DIR__ . '/config.php';

$db = getDB();
$startTime = time();

logMsg("=== Inicio sincronización automática ===");

// Obtener canales con auto_sync activado
$canales = $db->query("SELECT * FROM canales WHERE auto_sync = 1 AND activo = 1")->fetchAll();

if (empty($canales)) {
    logMsg("No hay canales con auto_sync activado.");
    exit(0);
}

logMsg(count($canales) . " canal(es) para sincronizar.");

$totalVideos = 0;
$totalPlaylists = 0;

foreach ($canales as $canal) {
    $channelId = $canal['youtube_channel_id'];
    if (empty($channelId)) {
        logMsg("[{$canal['nombre']}] Sin YouTube Channel ID, omitido.");
        continue;
    }

    logMsg("[{$canal['nombre']}] Iniciando sync...");

    try {
        $result = syncChannelAll(
            $db,
            $channelId,
            $canal['id'],
            $canal['default_categoria_id'],
            'cron',
            50
        );

        $totalVideos += $result['imported'];
        $totalPlaylists += $result['playlists_imported'];

        // Registrar en sync_log
        $errores = !empty($result['errors']) ? implode('; ', $result['errors']) : null;
        $stmt = $db->prepare("INSERT INTO sync_log (canal_id, videos_importados, playlists_importadas, errores) VALUES (?, ?, ?, ?)");
        $stmt->execute([$canal['id'], $result['imported'], $result['playlists_imported'], $errores]);

        $status = "{$result['imported']} videos, {$result['playlists_imported']} playlists";
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
logMsg("");

function logMsg($msg) {
    fwrite(STDOUT, date('Y-m-d H:i:s') . " | $msg\n");
}
