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
    // Portada: solo categorías marcadas. Con ?all=1 o filtro de canal/categoría: todos
    $filtroPortada = empty($_GET['all']) && empty($_GET['canal_id']) && empty($_GET['categoria']);
    $where = 'v.activo = 1';
    if ($filtroPortada) {
        $where .= ' AND (cat.mostrar_en_portada = 1 OR dcat.mostrar_en_portada = 1)';
    }
    if (!empty($_GET['canal_id'])) {
        $where .= ' AND c.id = ' . intval($_GET['canal_id']);
    }
    if (!empty($_GET['categoria'])) {
        $catFilter = $db->quote($_GET['categoria']);
        $where .= " AND (cat.nombre = $catFilter OR dcat.nombre = $catFilter)";
    }

    $stmt = $db->query("
        SELECT v.youtube_id, v.titulo, v.descripcion, v.duracion, v.vistas_yt, v.fecha_yt, v.tags,
               c.nombre AS canal_nombre, c.codigo AS canal_codigo, c.color AS canal_color, c.id AS canal_id,
               COALESCE(cat.nombre, dcat.nombre) AS categoria_nombre
        FROM videos v
        LEFT JOIN canales c ON v.canal_id = c.id
        LEFT JOIN categorias cat ON v.categoria_id = cat.id
        LEFT JOIN categorias dcat ON c.default_categoria_id = dcat.id
        WHERE $where
        ORDER BY v.fecha_yt DESC
    ");
    $videos = $stmt->fetchAll();

    // Total de videos (siempre el total real, no filtrado)
    $totalVideos = $db->query("SELECT COUNT(*) FROM videos WHERE activo = 1")->fetchColumn();

    // Build channels list (con categoría para agrupar en sidebar)
    $canalesStmt = $db->query("
        SELECT c.id, c.nombre, c.codigo, c.color, c.prioridad_portada, COALESCE(cat.nombre, '') AS categoria_nombre
        FROM canales c
        LEFT JOIN categorias cat ON c.default_categoria_id = cat.id
        WHERE c.activo = 1 ORDER BY c.nombre
    ");
    $canales = $canalesStmt->fetchAll();

    // Categorías para filtro del sidebar
    $categoriasStmt = $db->query("SELECT id, nombre, icono FROM categorias WHERE activa = 1 ORDER BY orden");
    $categoriasData = $categoriasStmt->fetchAll();

    // Playlists grouped by channel
    $playlistsStmt = $db->query("
        SELECT p.id, p.nombre, p.canal_id,
               (SELECT COUNT(*) FROM playlist_videos pv WHERE pv.playlist_id = p.id) AS total_videos
        FROM playlists p
        WHERE p.activa = 1
        ORDER BY p.nombre
    ");
    $playlists = $playlistsStmt->fetchAll();

    echo json_encode(['videos' => $videos, 'canales' => $canales, 'playlists' => $playlists, 'categorias' => $categoriasData, 'total_videos' => intval($totalVideos)], JSON_UNESCAPED_UNICODE);
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

// ── Playlists list ──
if ($action === 'playlists') {
    $stmt = $db->query("
        SELECT p.id, p.nombre, p.canal_id, c.nombre AS canal_nombre,
               (SELECT COUNT(*) FROM playlist_videos pv WHERE pv.playlist_id = p.id) AS total_videos
        FROM playlists p
        LEFT JOIN canales c ON p.canal_id = c.id
        WHERE p.activa = 1
        ORDER BY p.nombre
    ");
    echo json_encode(['playlists' => $stmt->fetchAll()], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Playlist videos ──
if ($action === 'playlist') {
    $plId = intval($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT nombre FROM playlists WHERE id = ? AND activa = 1");
    $stmt->execute([$plId]);
    $pl = $stmt->fetch();
    if (!$pl) {
        http_response_code(404);
        echo json_encode(['error' => 'Playlist no encontrada']);
        exit;
    }

    $stmt = $db->prepare("
        SELECT v.youtube_id, v.titulo, v.duracion, v.vistas_yt, v.fecha_yt,
               c.nombre AS canal_nombre, c.codigo AS canal_codigo, c.color AS canal_color, c.id AS canal_id
        FROM playlist_videos pv
        JOIN videos v ON pv.video_id = v.id
        LEFT JOIN canales c ON v.canal_id = c.id
        WHERE pv.playlist_id = ? AND v.activo = 1
        ORDER BY pv.orden
    ");
    $stmt->execute([$plId]);
    echo json_encode(['playlist' => $pl, 'videos' => $stmt->fetchAll()], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Video playlists (which playlists contain this video) ──
if ($action === 'video_playlists') {
    $ytId = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['id'] ?? '');
    $stmt = $db->prepare("
        SELECT p.id, p.nombre,
               (SELECT COUNT(*) FROM playlist_videos pv2 WHERE pv2.playlist_id = p.id) AS total_videos
        FROM playlists p
        JOIN playlist_videos pv ON pv.playlist_id = p.id
        JOIN videos v ON pv.video_id = v.id
        WHERE v.youtube_id = ? AND p.activa = 1
    ");
    $stmt->execute([$ytId]);

    // Get videos from the first playlist
    $playlists = $stmt->fetchAll();
    $playlistVideos = [];
    if (!empty($playlists)) {
        $firstPlId = $playlists[0]['id'];
        $stmt = $db->prepare("
            SELECT v.youtube_id, v.titulo, v.duracion, v.vistas_yt, v.fecha_yt,
                   c.nombre AS canal_nombre, c.codigo AS canal_codigo, c.color AS canal_color
            FROM playlist_videos pv
            JOIN videos v ON pv.video_id = v.id
            LEFT JOIN canales c ON v.canal_id = c.id
            WHERE pv.playlist_id = ? AND v.activo = 1
            ORDER BY pv.orden
        ");
        $stmt->execute([$firstPlId]);
        $playlistVideos = $stmt->fetchAll();
    }

    echo json_encode([
        'playlists' => $playlists,
        'playlist_videos' => $playlistVideos
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['error' => 'Acción no válida']);
