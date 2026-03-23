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

// ── Total count (all sections) ──
if ($action === 'total_titulos') {
    $totalVideos = intval($db->query("SELECT COUNT(*) FROM videos WHERE activo = 1")->fetchColumn());
    $totalCine = intval($db->query("SELECT COUNT(*) FROM contenido_ia WHERE activo = 1 AND bloqueado = 0 AND seccion = 'cine'")->fetchColumn());
    $totalAudio = intval($db->query("SELECT COUNT(*) FROM contenido_ia WHERE activo = 1 AND bloqueado = 0 AND seccion = 'audiolibros'")->fetchColumn());
    $totalLibros = intval($db->query("SELECT COUNT(*) FROM contenido_ia WHERE activo = 1 AND bloqueado = 0 AND seccion = 'libros'")->fetchColumn());
    echo json_encode([
        'total' => $totalVideos + $totalCine + $totalAudio + $totalLibros,
        'videos' => $totalVideos,
        'cine' => $totalCine,
        'audiolibros' => $totalAudio,
        'libros' => $totalLibros
    ]);
    exit;
}

// ── Contenido Internet Archive ──
if ($action === 'contenido_ia') {
    $seccion = $_GET['seccion'] ?? '';
    $where = 'activo = 1 AND bloqueado = 0';
    if ($seccion && in_array($seccion, ['cine', 'audiolibros', 'libros'])) {
        $where .= " AND seccion = '$seccion'";
    }
    $stmt = $db->query("
        SELECT slug, ia_id, titulo, director, year, duracion, genero, seccion, url_portada, fuente
        FROM contenido_ia
        WHERE $where
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
            'genero' => $row['genero'] ?: 'Sin género',
            'descargas' => 0,
            'url_portada' => $row['url_portada'] ?? '',
            'fuente' => $row['fuente'] ?? 'archive.org',
            'section' => $row['seccion'] === 'audiolibros' ? 'Audiolibro' : ($row['seccion'] === 'libros' ? 'Libro' : 'Cine')
        ];
    }
    echo json_encode($items, JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Bulk import IA content (AJAX, no page reload) ──
if ($action === 'import_ia_batch') {
    // Verify admin session
    session_start();
    require_once __DIR__ . '/config.php';
    if (empty($_SESSION[ADMIN_SESSION_NAME])) {
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $items = $input['items'] ?? [];
    $genero = trim($input['genero'] ?? '');

    // Genre detection
    $GENEROS_VALIDOS = ['Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo'];
    $GENERO_MAP = [
        'drama'=>'Drama','dramatic'=>'Drama','tragedy'=>'Drama',
        'comedy'=>'Comedia','comedia'=>'Comedia','comic'=>'Comedia','humor'=>'Comedia',
        'horror'=>'Terror','terror'=>'Terror','scary'=>'Terror',
        'sci-fi'=>'Ciencia ficción','science fiction'=>'Ciencia ficción','scifi'=>'Ciencia ficción',
        'adventure'=>'Aventura','aventura'=>'Aventura',
        'action'=>'Acción',
        'thriller'=>'Suspenso','suspense'=>'Suspenso','mystery'=>'Suspenso',
        'noir'=>'Film Noir','film noir'=>'Film Noir',
        'animation'=>'Animación','animated'=>'Animación','cartoon'=>'Animación',
        'documentary'=>'Documental','documental'=>'Documental',
        'history'=>'Historia','historia'=>'Historia','historical'=>'Historia',
        'musical'=>'Musical',
        'romance'=>'Romance','romantic'=>'Romance',
        'western'=>'Western',
        'war'=>'Bélico','military'=>'Bélico','guerra'=>'Bélico',
        'silent'=>'Cine mudo','silent film'=>'Cine mudo'
    ];
    function detectGenero($text, $map) {
        $lower = mb_strtolower($text);
        // Longest keys first
        $keys = array_keys($map);
        usort($keys, function($a,$b) { return strlen($b) - strlen($a); });
        foreach ($keys as $k) {
            if (strpos($lower, $k) !== false) return $map[$k];
        }
        return '';
    }
    $generoDefault = '';
    foreach ($GENEROS_VALIDOS as $v) {
        if (mb_strtolower($genero) === mb_strtolower($v)) { $generoDefault = $v; break; }
    }

    $imported = 0;
    $skipped = 0;
    $errors = 0;
    $lastError = '';
    $activo = !empty($input['activo']) ? 1 : 0;
    $seccion = in_array($input['seccion'] ?? '', ['cine', 'audiolibros']) ? $input['seccion'] : 'cine';
    $stmt = $db->prepare("INSERT INTO contenido_ia (slug, ia_id, titulo, director, year, duracion, genero, descripcion, agregado_por, activo, seccion, url_portada, url_contenido, fuente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $dupCheck = $db->prepare("SELECT id FROM contenido_ia WHERE ia_id = ?");
    $slugCheck = $db->prepare("SELECT id FROM contenido_ia WHERE slug = ?");

    foreach ($items as $item) {
        $ia_id = trim($item['ia_id'] ?? '');
        if (!$ia_id) continue;
        $dupCheck->execute([$ia_id]);
        if ($dupCheck->fetch()) { $skipped++; continue; }
        // Generate slug: clean ia_id, truncate to 90 chars
        $slug = preg_replace('/[^a-zA-Z0-9_-]/', '', $ia_id);
        if (strlen($slug) < 2) $slug = 'ia_' . md5($ia_id);
        $baseSlug = substr($slug, 0, 90);
        $slug = $baseSlug;
        $n = 1;
        $slugCheck->execute([$slug]);
        while ($slugCheck->fetch()) {
            $slug = substr($baseSlug, 0, 85) . '_' . $n++;
            $slugCheck->execute([$slug]);
        }
        $titulo = mb_substr(trim($item['titulo'] ?? $ia_id), 0, 490);
        $director = mb_substr(trim($item['director'] ?? ''), 0, 195);
        $year = intval($item['year'] ?? 0) ?: null;
        $duracion = substr(trim($item['duracion'] ?? ''), 0, 14);
        $descripcion = trim($item['descripcion'] ?? '');
        // Auto-detect genre from subject/title, fallback to global default
        $subject = trim($item['subject'] ?? '');
        $itemGenero = detectGenero($subject, $GENERO_MAP) ?: detectGenero($titulo, $GENERO_MAP) ?: $generoDefault;
        try {
            $urlPortada = trim($item['url_portada'] ?? '');
            $urlContenido = trim($item['url_contenido'] ?? '');
            $fuente = trim($item['fuente'] ?? 'archive.org');
            $stmt->execute([$slug, $ia_id, $titulo, $director, $year, $duracion, $itemGenero, $descripcion, $_SESSION['admin_nombre'] ?? 'admin', $activo, $seccion, $urlPortada, $urlContenido, $fuente]);
            $imported++;
        } catch (Exception $e) {
            $errors++;
            $lastError = $e->getMessage();
        }
    }

    echo json_encode(['imported' => $imported, 'skipped' => $skipped, 'errors' => $errors, 'lastError' => $lastError], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Search Internet Archive (admin proxy) ──
if ($action === 'search_ia') {
    $q = trim($_GET['q'] ?? '');
    $lang = trim($_GET['lang'] ?? 'Spanish');
    $rows = min(intval($_GET['rows'] ?? 30), 200);
    $page = max(intval($_GET['page'] ?? 0), 0);
    $iaPage = $page + 1; // IA uses 1-based pages
    if (!$q) {
        echo json_encode(['error' => 'Falta parámetro q']);
        exit;
    }

    // Build IA advanced search query
    $collection = trim($_GET['collection'] ?? '');
    $mediatype = trim($_GET['mediatype'] ?? 'movies');
    if (!in_array($mediatype, ['movies', 'audio'])) $mediatype = 'movies';
    $iaQuery = '(' . $q . ') AND mediatype:' . $mediatype;
    if ($collection) {
        $iaQuery .= ' AND collection:(' . $collection . ')';
    }
    if ($lang) {
        $iaQuery .= ' AND language:(' . $lang . ')';
    }

    $url = 'https://archive.org/advancedsearch.php?'
        . 'q=' . urlencode($iaQuery)
        . '&fl=identifier,title,creator,year,date,description,runtime,subject,language,collection'
        . '&sort=downloads+desc'
        . '&rows=' . $rows
        . '&page=' . $iaPage
        . '&output=json';

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
        $checkStmt = $db->prepare("SELECT ia_id FROM contenido_ia WHERE ia_id = ? LIMIT 1");
        foreach ($docs as $doc) {
            $checkStmt->execute([$doc['identifier']]);
            $found = $checkStmt->fetchColumn();
            if ($found !== false) {
                $existingIds[] = $doc['identifier'];
            }
        }
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

        $cols = $doc['collection'] ?? [];
        if (is_string($cols)) $cols = [$cols];
        $CURATED = ['feature_films','Film_Noir','silent_films','classic_cartoons','anime','ephemera','short_films','classic_tv','moviesandfilms','librivoxaudio','audio_bookspoetry','audio_foreign'];
        $isCurated = false;
        $colLabel = 'Comunidad';
        foreach ($cols as $c) {
            if (in_array($c, $CURATED)) {
                $isCurated = true;
                $colLabels = ['feature_films'=>'Largometrajes','Film_Noir'=>'Film Noir','silent_films'=>'Cine mudo','classic_cartoons'=>'Dibujos animados','anime'=>'Anime','ephemera'=>'Films educativos','short_films'=>'Cortometrajes','classic_tv'=>'TV clásica','moviesandfilms'=>'Movies','librivoxaudio'=>'LibriVox','audio_bookspoetry'=>'Libros y poesía','audio_foreign'=>'Audio no-inglés'];
                $colLabel = $colLabels[$c] ?? $c;
                break;
            }
        }

        $results[] = [
            'ia_id' => $doc['identifier'],
            'titulo' => $doc['title'] ?? $doc['identifier'],
            'director' => $creator,
            'year' => $year,
            'duracion' => $doc['runtime'] ?? '',
            'genero' => $subject,
            'descripcion' => mb_substr(strip_tags($desc), 0, 200),
            'idioma' => $lang,
            'coleccion' => $colLabel,
            'curada' => $isCurated,
            'ya_existe' => in_array($doc['identifier'], $existingIds)
        ];
    }

    echo json_encode(['results' => $results, 'total' => $data['response']['numFound'] ?? 0], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Proxy Gutenberg book content ──
if ($action === 'proxy_gutenberg') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    // Try plain text first (lighter), then HTML
    $textUrl = 'https://www.gutenberg.org/ebooks/' . $id . '.txt.utf-8';
    $ctx = stream_context_create(['http' => ['timeout' => 20, 'follow_location' => true]]);
    $content = @file_get_contents($textUrl, false, $ctx);

    if ($content) {
        // Convert plain text to HTML paragraphs
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        // Split into paragraphs (double newline)
        $paragraphs = preg_split('/\n\s*\n/', $content);
        $html = '';
        foreach ($paragraphs as $p) {
            $p = trim($p);
            if ($p) $html .= '<p>' . nl2br($p) . '</p>';
        }
        echo json_encode(['html' => $html], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['error' => 'No se pudo cargar el libro']);
    }
    exit;
}

// ── Search Gutenberg (admin proxy) ──
if ($action === 'search_gutenberg') {
    $q = trim($_GET['q'] ?? '');
    $page = max(intval($_GET['page'] ?? 1), 1);

    $url = 'https://gutendex.com/books/?languages=es&page=' . $page;
    if ($q) $url .= '&search=' . urlencode($q);

    $ctx = stream_context_create(['http' => ['timeout' => 15]]);
    $json = @file_get_contents($url, false, $ctx);
    if (!$json) {
        echo json_encode(['error' => 'No se pudo conectar con Gutenberg']);
        exit;
    }

    $data = json_decode($json, true);
    $total = $data['count'] ?? 0;
    $books = $data['results'] ?? [];

    // Check existing
    $checkStmt = $db->prepare("SELECT ia_id FROM contenido_ia WHERE ia_id = ? LIMIT 1");
    $existingIds = [];
    foreach ($books as $b) {
        $checkStmt->execute(['gutenberg_' . $b['id']]);
        if ($checkStmt->fetchColumn() !== false) $existingIds[] = 'gutenberg_' . $b['id'];
    }

    $results = [];
    foreach ($books as $b) {
        $authors = [];
        foreach ($b['authors'] ?? [] as $a) { $authors[] = $a['name']; }
        $subjects = [];
        foreach ($b['subjects'] ?? [] as $s) { $subjects[] = $s; }

        // Get cover and HTML URLs
        $formats = $b['formats'] ?? [];
        $cover = '';
        $htmlUrl = '';
        foreach ($formats as $mime => $url) {
            if (strpos($mime, 'image/jpeg') !== false && !$cover) $cover = $url;
            if ($mime === 'text/html' && !$htmlUrl) $htmlUrl = $url;
            if ($mime === 'text/html; charset=utf-8' && !$htmlUrl) $htmlUrl = $url;
        }

        $results[] = [
            'gutenberg_id' => $b['id'],
            'ia_id' => 'gutenberg_' . $b['id'],
            'titulo' => $b['title'] ?? '',
            'director' => implode('; ', $authors),
            'year' => '',
            'genero' => implode(', ', array_slice($subjects, 0, 3)),
            'url_portada' => $cover,
            'url_contenido' => $htmlUrl,
            'descargas' => $b['download_count'] ?? 0,
            'ya_existe' => in_array('gutenberg_' . $b['id'], $existingIds)
        ];
    }

    echo json_encode(['results' => $results, 'total' => $total], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['error' => 'Acción no válida']);
