<?php
/**
 * EduTube — Proxy de thumbnails con cache local
 *
 * Uso: thumb.php?id=VIDEO_ID&s=mq (mq=320px, hq=480px, sd=640px)
 *
 * Descarga la thumbnail de YouTube la primera vez y la sirve
 * desde disco en requests posteriores. Así el navegador del
 * usuario nunca contacta servidores de YouTube/Google directamente
 * (excepto por el embed en sí).
 */

$videoId = $_GET['id'] ?? '';
$size = $_GET['s'] ?? 'mq'; // mq, hq, sd, maxres

// Validar ID (11 chars alfanuméricos + guion/guion bajo)
if (!preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId)) {
    http_response_code(400);
    exit;
}

// Validar tamaño
$sizes = ['mq' => 'mqdefault', 'hq' => 'hqdefault', 'sd' => 'sddefault', 'maxres' => 'maxresdefault'];
$sizeKey = $sizes[$size] ?? 'mqdefault';

$cacheDir = __DIR__ . '/thumbs';
$cachePath = $cacheDir . '/' . $videoId . '_' . $size . '.jpg';

// Servir desde cache si existe y tiene menos de 30 días
if (file_exists($cachePath) && (time() - filemtime($cachePath)) < 2592000) {
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=2592000');
    header('X-Cache: HIT');
    readfile($cachePath);
    exit;
}

// Descargar de YouTube
$url = 'https://img.youtube.com/vi/' . $videoId . '/' . $sizeKey . '.jpg';
$ctx = stream_context_create(['http' => [
    'timeout' => 5,
    'user_agent' => 'EduTube/1.0',
]]);
$data = @file_get_contents($url, false, $ctx);

if ($data === false || strlen($data) < 1000) {
    // Fallback: intentar hqdefault si pidieron otro tamaño
    if ($sizeKey !== 'hqdefault') {
        $url = 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg';
        $data = @file_get_contents($url, false, $ctx);
    }
    if ($data === false || strlen($data) < 1000) {
        http_response_code(404);
        exit;
    }
}

// Guardar en cache
if (is_writable($cacheDir)) {
    file_put_contents($cachePath, $data);
}

header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=2592000');
header('X-Cache: MISS');
echo $data;
