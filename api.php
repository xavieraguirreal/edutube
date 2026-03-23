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

// ── Canal profile ──
if ($action === 'canal') {
    $canalId = intval($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT c.*, cat.nombre AS categoria_nombre FROM canales c LEFT JOIN categorias cat ON c.default_categoria_id = cat.id WHERE c.id = ? AND c.activo = 1");
    $stmt->execute([$canalId]);
    $canal = $stmt->fetch();
    if (!$canal) {
        http_response_code(404);
        echo json_encode(['error' => 'Canal no encontrado']);
        exit;
    }
    // Remove sensitive fields
    unset($canal['nota_interna']);

    // EduTube stats
    $stmtV = $db->prepare("SELECT COUNT(*) FROM videos WHERE canal_id = ? AND activo = 1");
    $stmtV->execute([$canalId]);
    $edutube_videos = $stmtV->fetchColumn();

    $stmtP = $db->prepare("SELECT COUNT(*) FROM playlists WHERE canal_id = ? AND activa = 1");
    $stmtP->execute([$canalId]);
    $edutube_playlists = $stmtP->fetchColumn();

    // EduTube views (from registro_vistas)
    $stmtViews = $db->prepare("SELECT COUNT(*) FROM registro_vistas rv JOIN videos v ON rv.video_id = v.id WHERE v.canal_id = ?");
    $stmtViews->execute([$canalId]);
    $edutube_views = $stmtViews->fetchColumn();

    // Total YouTube views for this channel's videos in EduTube
    $stmtYtViews = $db->prepare("SELECT COALESCE(SUM(vistas_yt), 0) FROM videos WHERE canal_id = ? AND activo = 1");
    $stmtYtViews->execute([$canalId]);
    $total_yt_views = $stmtYtViews->fetchColumn();

    // Recent videos
    $stmtRecent = $db->prepare("
        SELECT v.youtube_id, v.titulo, v.duracion, v.vistas_yt, v.fecha_yt,
               c.nombre AS canal_nombre, c.codigo AS canal_codigo, c.color AS canal_color, c.id AS canal_id
        FROM videos v
        LEFT JOIN canales c ON v.canal_id = c.id
        WHERE v.canal_id = ? AND v.activo = 1
        ORDER BY v.fecha_yt DESC
    ");
    $stmtRecent->execute([$canalId]);
    $allVideos = $stmtRecent->fetchAll();

    // Playlists with preview videos
    $stmtPl = $db->prepare("
        SELECT p.id, p.nombre,
               (SELECT COUNT(*) FROM playlist_videos pv WHERE pv.playlist_id = p.id) AS total_videos
        FROM playlists p
        WHERE p.canal_id = ? AND p.activa = 1
        ORDER BY p.nombre
    ");
    $stmtPl->execute([$canalId]);
    $playlists = $stmtPl->fetchAll();

    echo json_encode([
        'canal' => $canal,
        'stats' => [
            'edutube_videos' => intval($edutube_videos),
            'edutube_playlists' => intval($edutube_playlists),
            'edutube_views' => intval($edutube_views),
            'total_yt_views' => intval($total_yt_views)
        ],
        'videos' => $allVideos,
        'playlists' => $playlists
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Contenido Internet Archive (cine) ──
if ($action === 'contenido_ia') {
    $stmt = $db->query("
        SELECT slug, ia_id, titulo, director, year, duracion, genero
        FROM contenido_ia
        WHERE activo = 1
        ORDER BY orden, titulo
    ");
    $items = [];
    foreach ($stmt->fetchAll() as $row) {
        $items[] = [
            'id' => 'ia:' . $row['slug'],
            'ia_id' => $row['ia_id'],
            'titulo' => $row['titulo'],
            'director' => $row['director'],
            'year' => intval($row['year']),
            'duracion' => $row['duracion'],
            'genero' => $row['genero'],
            'descargas' => 0,
            'section' => 'Cine'
        ];
    }
    echo json_encode($items, JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Search Internet Archive (admin proxy) ──
if ($action === 'search_ia') {
    $q = trim($_GET['q'] ?? '');
    $lang = trim($_GET['lang'] ?? 'Spanish');
    $rows = min(intval($_GET['rows'] ?? 30), 50);
    $page = max(intval($_GET['page'] ?? 0), 0);
    $start = $page * $rows;
    if (!$q) {
        echo json_encode(['error' => 'Falta parámetro q']);
        exit;
    }

    // Build IA advanced search query
    $collection = trim($_GET['collection'] ?? '');
    $iaQuery = '(' . $q . ') AND mediatype:movies';
    if ($collection) {
        $iaQuery .= ' AND collection:(' . $collection . ')';
    }
    if ($lang) {
        $iaQuery .= ' AND language:(' . $lang . ')';
    }

    $url = 'https://archive.org/advancedsearch.php?' . http_build_query([
        'q' => $iaQuery,
        'fl' => ['identifier', 'title', 'creator', 'year', 'date', 'description', 'runtime', 'subject', 'language'],
        'sort' => ['downloads desc'],
        'rows' => $rows,
        'start' => $start,
        'output' => 'json'
    ]);

    $ctx = stream_context_create(['http' => ['timeout' => 15]]);
    $json = @file_get_contents($url, false, $ctx);
    if (!$json) {
        echo json_encode(['error' => 'No se pudo conectar con Archive.org']);
        exit;
    }

    $data = json_decode($json, true);
    $docs = $data['response']['docs'] ?? [];

    // Check which ia_ids already exist in our DB
    $existingIds = [];
    if ($docs) {
        $placeholders = implode(',', array_fill(0, count($docs), '?'));
        $ids = array_map(function($d) { return $d['identifier']; }, $docs);
        $stmt = $db->prepare("SELECT ia_id FROM contenido_ia WHERE ia_id IN ($placeholders)");
        $stmt->execute($ids);
        $existingIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    $results = [];
    foreach ($docs as $doc) {
        $desc = $doc['description'] ?? '';
        if (is_array($desc)) $desc = implode(' ', $desc);
        $creator = $doc['creator'] ?? '';
        if (is_array($creator)) $creator = implode(', ', $creator);
        $subject = $doc['subject'] ?? '';
        if (is_array($subject)) $subject = implode(', ', $subject);
        $lang = $doc['language'] ?? '';
        if (is_array($lang)) $lang = implode(', ', $lang);
        $year = $doc['year'] ?? '';
        if (!$year && !empty($doc['date'])) $year = substr($doc['date'], 0, 4);

        $results[] = [
            'ia_id' => $doc['identifier'],
            'titulo' => $doc['title'] ?? $doc['identifier'],
            'director' => $creator,
            'year' => $year,
            'duracion' => $doc['runtime'] ?? '',
            'genero' => $subject,
            'descripcion' => mb_substr(strip_tags($desc), 0, 200),
            'idioma' => $lang,
            'ya_existe' => in_array($doc['identifier'], $existingIds)
        ];
    }

    echo json_encode(['results' => $results, 'total' => $data['response']['numFound'] ?? 0], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['error' => 'Acción no válida']);
