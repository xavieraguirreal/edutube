<?php
// ═══════════════════════════════════════════
// EduTube — API interna (JSON)
// Sirve datos de la BD para el frontend
// ═══════════════════════════════════════════
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';
$db = getDB();

$action = $_GET['action'] ?? '';

// ── Videos list ──
if ($action === 'videos') {
    $stmt = $db->query("
        SELECT v.youtube_id, v.titulo, v.descripcion, v.duracion, v.vistas_yt, v.fecha_yt, v.tags,
               c.nombre AS canal_nombre, c.codigo AS canal_codigo, c.color AS canal_color, c.id AS canal_id,
               cat.nombre AS categoria_nombre
        FROM videos v
        LEFT JOIN canales c ON v.canal_id = c.id
        LEFT JOIN categorias cat ON v.categoria_id = cat.id
        WHERE v.activo = 1
        ORDER BY v.fecha_yt DESC
    ");
    $videos = $stmt->fetchAll();

    // Build channels list
    $canalesStmt = $db->query("SELECT id, nombre, codigo, color FROM canales WHERE activo = 1 ORDER BY nombre");
    $canales = $canalesStmt->fetchAll();

    echo json_encode(['videos' => $videos, 'canales' => $canales], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Single video ──
if ($action === 'video') {
    $ytId = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['id'] ?? '');
    $stmt = $db->prepare("
        SELECT v.youtube_id, v.titulo, v.descripcion, v.duracion, v.vistas_yt, v.fecha_yt, v.tags,
               c.nombre AS canal_nombre, c.codigo AS canal_codigo, c.color AS canal_color, c.id AS canal_id,
               cat.nombre AS categoria_nombre
        FROM videos v
        LEFT JOIN canales c ON v.canal_id = c.id
        LEFT JOIN categorias cat ON v.categoria_id = cat.id
        WHERE v.youtube_id = ? AND v.activo = 1
    ");
    $stmt->execute([$ytId]);
    $video = $stmt->fetch();

    if (!$video) {
        http_response_code(404);
        echo json_encode(['error' => 'Video no encontrado']);
        exit;
    }

    // Related: same channel first, then by tags
    $related = $db->prepare("
        SELECT v.youtube_id, v.titulo, v.duracion, v.vistas_yt, v.fecha_yt,
               c.nombre AS canal_nombre, c.codigo AS canal_codigo, c.color AS canal_color, c.id AS canal_id
        FROM videos v
        LEFT JOIN canales c ON v.canal_id = c.id
        WHERE v.youtube_id != ? AND v.activo = 1
        ORDER BY (v.canal_id = ?) DESC, v.fecha_yt DESC
        LIMIT 15
    ");
    $related->execute([$ytId, $video['canal_id']]);

    // Register view
    $ipHash = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . date('Y-m-d'));
    $stmt = $db->prepare("INSERT INTO registro_vistas (video_id, ip_hash) SELECT id, ? FROM videos WHERE youtube_id = ?");
    $stmt->execute([$ipHash, $ytId]);

    echo json_encode([
        'video' => $video,
        'related' => $related->fetchAll()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Search ──
if ($action === 'search') {
    $q = trim($_GET['q'] ?? '');
    if (empty($q)) {
        echo json_encode(['videos' => []]);
        exit;
    }

    // FULLTEXT search
    $stmt = $db->prepare("
        SELECT v.youtube_id, v.titulo, v.descripcion, v.duracion, v.vistas_yt, v.fecha_yt,
               c.nombre AS canal_nombre, c.codigo AS canal_codigo, c.color AS canal_color, c.id AS canal_id
        FROM videos v
        LEFT JOIN canales c ON v.canal_id = c.id
        WHERE v.activo = 1 AND MATCH(v.titulo, v.descripcion, v.tags) AGAINST(? IN BOOLEAN MODE)
        ORDER BY v.fecha_yt DESC
        LIMIT 50
    ");
    $stmt->execute([$q . '*']);
    $results = $stmt->fetchAll();

    // Fallback to LIKE if FULLTEXT returns nothing
    if (empty($results)) {
        $like = '%' . $q . '%';
        $stmt = $db->prepare("
            SELECT v.youtube_id, v.titulo, v.descripcion, v.duracion, v.vistas_yt, v.fecha_yt,
                   c.nombre AS canal_nombre, c.codigo AS canal_codigo, c.color AS canal_color, c.id AS canal_id
            FROM videos v
            LEFT JOIN canales c ON v.canal_id = c.id
            WHERE v.activo = 1 AND (v.titulo LIKE ? OR v.descripcion LIKE ? OR v.tags LIKE ? OR c.nombre LIKE ?)
            ORDER BY v.fecha_yt DESC
            LIMIT 50
        ");
        $stmt->execute([$like, $like, $like, $like]);
        $results = $stmt->fetchAll();
    }

    // Log search
    $db->prepare("INSERT INTO busquedas (termino, resultados) VALUES (?, ?)")->execute([$q, count($results)]);

    echo json_encode(['videos' => $results], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['error' => 'Acción no válida']);
