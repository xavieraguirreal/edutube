<?php
set_time_limit(300); // 5 minutes for imports
ini_set('memory_limit', '256M');
session_start();
require_once __DIR__ . '/config.php';

$db = getDB();

// ═══════════════════════════════════════════
// Rate limiting
// ═══════════════════════════════════════════
function checkRateLimit($db, $ip) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM login_intentos WHERE ip = ? AND fecha > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $stmt->execute([$ip]);
    return $stmt->fetchColumn() < 5;
}

function logLoginAttempt($db, $ip) {
    $stmt = $db->prepare("INSERT INTO login_intentos (ip) VALUES (?)");
    $stmt->execute([$ip]);
}

// ═══════════════════════════════════════════
// Auth
// ═══════════════════════════════════════════
$isLoggedIn = !empty($_SESSION[ADMIN_SESSION_NAME]);

if (!$isLoggedIn && isset($_POST['action']) && $_POST['action'] === 'login') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    if (!checkRateLimit($db, $ip)) {
        $loginError = 'Demasiados intentos. Esperá 15 minutos.';
    } else {
        $user = $_POST['usuario'] ?? '';
        $pass = $_POST['password'] ?? '';
        $stmt = $db->prepare("SELECT * FROM admins WHERE usuario = ?");
        $stmt->execute([$user]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($pass, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION[ADMIN_SESSION_NAME] = $admin['id'];
            $_SESSION['admin_nombre'] = $admin['nombre'];
            $isLoggedIn = true;
        } else {
            logLoginAttempt($db, $ip);
            $loginError = 'Usuario o contraseña incorrectos.';
        }
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// ═══════════════════════════════════════════
// Login page
// ═══════════════════════════════════════════
if (!$isLoggedIn) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTube — Admin</title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#f5f5f5; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .login-card { background:#fff; border-radius:16px; padding:2.5rem; width:100%; max-width:400px; box-shadow:0 2px 12px rgba(0,0,0,0.08); }
        .login-logo { display:flex; align-items:center; gap:0.5rem; justify-content:center; margin-bottom:2rem; }
        .login-logo img { width:32px; }
        .login-logo span { font-size:1.3rem; font-weight:700; color:#2e8b47; }
        .login-card h2 { text-align:center; font-size:1.1rem; margin-bottom:1.5rem; color:#333; }
        .form-group { margin-bottom:1rem; }
        .form-group label { display:block; font-size:0.85rem; font-weight:500; margin-bottom:0.3rem; color:#555; }
        .form-group input { width:100%; padding:0.6rem 0.8rem; border:1px solid #ddd; border-radius:8px; font-size:0.9rem; font-family:inherit; }
        .form-group input:focus { outline:none; border-color:#2e8b47; }
        .btn-login { width:100%; padding:0.7rem; background:#2e8b47; color:#fff; border:none; border-radius:8px; font-size:0.95rem; font-weight:600; cursor:pointer; font-family:inherit; }
        .btn-login:hover { background:#38a555; }
        .error { background:#fee; color:#c00; padding:0.6rem; border-radius:8px; font-size:0.85rem; margin-bottom:1rem; text-align:center; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <img src="loguito-edutube.png" alt="EduTube">
        <span>EduTube</span>
    </div>
    <h2>Panel de Administración</h2>
    <?php if (!empty($loginError)): ?>
        <div class="error"><?= e($loginError) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
            <label>Usuario</label>
            <input type="text" name="usuario" required autofocus>
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn-login">Ingresar</button>
    </form>
</div>
</body>
</html>
<?php
    exit;
}

// ═══════════════════════════════════════════
// Admin panel (authenticated)
// ═══════════════════════════════════════════
$csrf = generateCSRF();
$msg = '';
$msgType = '';

$GENEROS_VALIDOS = ['Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo'];
function validarGenero($g) {
    global $GENEROS_VALIDOS;
    $g = trim($g);
    // Exact match
    if (in_array($g, $GENEROS_VALIDOS)) return $g;
    // Case-insensitive match
    foreach ($GENEROS_VALIDOS as $v) {
        if (mb_strtolower($g) === mb_strtolower($v)) return $v;
    }
    return '';
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] !== 'login') {
    if (!validateCSRF($_POST['csrf'] ?? '')) {
        $msg = 'Token de seguridad inválido.';
        $msgType = 'error';
    } else {
        $action = $_POST['action'];

        // ── Add video ──
        if ($action === 'add_video') {
            $ytId = extractYouTubeId($_POST['youtube_url'] ?? '');
            if (!$ytId) {
                $msg = 'URL de YouTube inválida.'; $msgType = 'error';
            } else {
                // Check duplicate
                $stmt = $db->prepare("SELECT id FROM videos WHERE youtube_id = ?");
                $stmt->execute([$ytId]);
                if ($stmt->fetch()) {
                    $msg = 'Este video ya está indexado.'; $msgType = 'error';
                } else {
                    // Get metadata via YouTube API
                    $apiDetails = getVideoDetailsAPI([$ytId]);
                    $meta = $apiDetails[$ytId] ?? null;
                    if (!$meta && empty($_POST['titulo'])) {
                        $msg = 'El video no está disponible (puede ser privado o eliminado).'; $msgType = 'error';
                    } else {
                    $titulo = $_POST['titulo'] ?: ($meta ? $meta['titulo'] : 'Sin título');
                    $desc = $_POST['descripcion'] ?: ($meta ? $meta['descripcion'] : '');
                    $duracion = $meta ? $meta['duracion'] : '';
                    $vistas = $meta ? $meta['vistas'] : 0;
                    $fecha = $meta ? $meta['fecha'] : date('Y-m-d');
                    $canalId = $_POST['canal_id'] ?: null;
                    $catId = $_POST['categoria_id'] ?: null;
                    $tags = $_POST['tags'] ?: ($meta ? $meta['tags'] : '');

                    // Embedding se genera después en background
                    $embedding = null;

                    $stmt = $db->prepare("INSERT INTO videos (youtube_id, titulo, descripcion, canal_id, categoria_id, duracion, vistas_yt, fecha_yt, tags, embedding, agregado_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $ytId, $titulo, $desc, $canalId, $catId, $duracion, $vistas, $fecha,
                        $tags, $embedding ? json_encode($embedding) : null,
                        $_SESSION['admin_nombre'] ?? 'admin'
                    ]);

                    $msg = 'Video "' . e($titulo) . '" agregado correctamente.';
                    $msgType = 'success';
                    }
                }
            }
        }

        // ── Import channel ──
        if ($action === 'import_channel') {
            $url = $_POST['channel_url'] ?? '';
            $channelId = extractChannelId($url);
            if (!$channelId) {
                $msg = 'No se pudo obtener el Channel ID.'; $msgType = 'error';
            } else {
                // Get channel info from YouTube API
                $chInfo = getChannelInfo($channelId);
                if (!$chInfo) {
                    $msg = 'No se pudo obtener información del canal.'; $msgType = 'error';
                } else {
                    // Check if channel exists in DB
                    $stmt = $db->prepare("SELECT id FROM canales WHERE youtube_channel_id = ?");
                    $stmt->execute([$channelId]);
                    $canal = $stmt->fetch();

                    if (!$canal) {
                        $nombre = $_POST['canal_nombre'] ?: $chInfo['nombre'];
                        $palabras = preg_split('/\s+/', $nombre);
                        $autoCodigo = '';
                        foreach ($palabras as $p) { $autoCodigo .= mb_strtoupper(mb_substr($p, 0, 1)); }
                        $autoCodigo = mb_substr($autoCodigo, 0, 4) ?: 'CH';
                        $colores = ['#2e8b47','#e63946','#9b5de5','#f77f00','#00b4d8','#e76f51','#6a994e','#bc4749'];

                        $defaultCatId = $_POST['categoria_id'] ?: null;
                        $stmt = $db->prepare("INSERT INTO canales (nombre, youtube_channel_id, codigo, color, default_categoria_id) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$nombre, $channelId, $autoCodigo, $colores[array_rand($colores)], $defaultCatId]);
                        $canalDbId = $db->lastInsertId();
                    } else {
                        $canalDbId = $canal['id'];
                    }

                    $catId = $_POST['categoria_id'] ?: null;

                    // Fallback to channel's default category
                    if (!$catId) {
                        $stmtDefCat = $db->prepare("SELECT default_categoria_id FROM canales WHERE id = ?");
                        $stmtDefCat->execute([$canalDbId]);
                        $defCat = $stmtDefCat->fetchColumn();
                        if ($defCat) $catId = $defCat;
                    }

                    $imported = 0;
                    $playlistsImported = 0;
                    $totalNewVideos = 0;
                    $msgType = 'success';

                    if (isset($_POST['sync_all'])) {
                        // Use shared sync function
                        $syncResult = syncChannelAll($db, $channelId, $canalDbId, $catId, $_SESSION['admin_nombre'] ?? 'admin');
                        $imported = $syncResult['imported'];
                        $playlistsImported = $syncResult['playlists_imported'];
                        $totalNewVideos = $syncResult['total_new'];

                        $parts = [];
                        if ($imported > 0) $parts[] = "$imported videos nuevos";
                        if ($playlistsImported > 0) $parts[] = "$playlistsImported playlists nuevas";
                        if (!empty($parts)) {
                            $msg = "Importación completada: " . implode(', ', $parts) . ".";
                            if ($syncResult['hit_limit']) {
                                $msg .= " Se alcanzó el límite de 50 videos por ejecución. Ejecutá de nuevo para continuar.";
                            }
                        } else {
                            $msg = "Todo al día. No hay contenido nuevo para importar.";
                        }
                    } else {
                        // Manual selection of playlists / loose videos
                        $selectedPlaylists = $_POST['playlists'] ?? [];
                        $maxVideosPerRun = 50;

                        // Import loose videos if selected
                        if (isset($_POST['import_latest'])) {
                            $limit = intval($_POST['limit'] ?? 15);
                            $uploadsPlaylist = $chInfo['uploads_playlist'] ?? '';

                            if ($uploadsPlaylist) {
                                $videoIds = getPlaylistVideoIds($uploadsPlaylist, $limit);

                                $newIds = [];
                                foreach ($videoIds as $vid) {
                                    $stmt = $db->prepare("SELECT id FROM videos WHERE youtube_id = ?");
                                    $stmt->execute([$vid]);
                                    if (!$stmt->fetch()) $newIds[] = $vid;
                                }

                                if (!empty($newIds)) {
                                    $apiDetails = getVideoDetailsAPI($newIds);

                                    usort($newIds, function($a, $b) use ($apiDetails) {
                                        $fa = isset($apiDetails[$a]) ? $apiDetails[$a]['fecha'] : '';
                                        $fb = isset($apiDetails[$b]) ? $apiDetails[$b]['fecha'] : '';
                                        return strcmp($fb, $fa);
                                    });

                                    foreach ($newIds as $ytId) {
                                        $meta = $apiDetails[$ytId] ?? null;
                                        if (!$meta) continue;
                                        $stmt = $db->prepare("INSERT INTO videos (youtube_id, titulo, descripcion, canal_id, categoria_id, duracion, vistas_yt, fecha_yt, tags, agregado_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                        $stmt->execute([
                                            $ytId, $meta['titulo'], $meta['descripcion'],
                                            $canalDbId, $catId, $meta['duracion'],
                                            $meta['vistas'], $meta['fecha'], $meta['tags'],
                                            $_SESSION['admin_nombre'] ?? 'admin'
                                        ]);
                                        $imported++;
                                        $totalNewVideos++;
                                    }

                                    // Create auto-playlist for loose videos
                                    $canalNombre = $_POST['canal_nombre'] ?: $chInfo['nombre'];
                                    $autoPlName = $canalNombre . ' — Sin lista';
                                    $stmt = $db->prepare("SELECT id FROM playlists WHERE nombre = ? AND canal_id = ?");
                                    $stmt->execute([$autoPlName, $canalDbId]);
                                    $autoPlRow = $stmt->fetch();

                                    if ($autoPlRow) {
                                        $autoPlId = $autoPlRow['id'];
                                    } else {
                                        $stmt = $db->prepare("INSERT INTO playlists (nombre, descripcion, canal_id) VALUES (?, ?, ?)");
                                        $stmt->execute([$autoPlName, 'Videos del canal que no pertenecen a una playlist específica.', $canalDbId]);
                                        $autoPlId = $db->lastInsertId();
                                        $playlistsImported++;
                                    }

                                    $stmt = $db->prepare("SELECT COALESCE(MAX(orden), -1) FROM playlist_videos WHERE playlist_id = ?");
                                    $stmt->execute([$autoPlId]);
                                    $maxOrden = intval($stmt->fetchColumn());

                                    foreach ($videoIds as $vid) {
                                        $stmt = $db->prepare("SELECT id FROM videos WHERE youtube_id = ?");
                                        $stmt->execute([$vid]);
                                        $vRow = $stmt->fetch();
                                        if ($vRow) {
                                            $stmt2 = $db->prepare("SELECT COUNT(*) FROM playlist_videos WHERE video_id = ? AND playlist_id != ?");
                                            $stmt2->execute([$vRow['id'], $autoPlId]);
                                            if (intval($stmt2->fetchColumn()) === 0) {
                                                $maxOrden++;
                                                $stmt3 = $db->prepare("INSERT IGNORE INTO playlist_videos (playlist_id, video_id, orden) VALUES (?, ?, ?)");
                                                $stmt3->execute([$autoPlId, $vRow['id'], $maxOrden]);
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        // Import selected playlists
                        foreach ($selectedPlaylists as $plYtId) {
                            if ($totalNewVideos >= $maxVideosPerRun) break;

                            $plData = youtubeApiGet('playlists', ['part' => 'snippet,contentDetails', 'id' => $plYtId]);
                            if (!$plData || empty($plData['items'])) continue;
                            $plInfo = $plData['items'][0];

                            $stmt = $db->prepare("SELECT id FROM playlists WHERE youtube_playlist_id = ?");
                            $stmt->execute([$plYtId]);
                            $existingPl = $stmt->fetch();

                            if ($existingPl) {
                                $plDbId = $existingPl['id'];
                            } else {
                                $stmt = $db->prepare("INSERT INTO playlists (nombre, descripcion, canal_id, youtube_playlist_id) VALUES (?, ?, ?, ?)");
                                $stmt->execute([$plInfo['snippet']['title'] ?? 'Playlist', $plInfo['snippet']['description'] ?? '', $canalDbId, $plYtId]);
                                $plDbId = $db->lastInsertId();
                                $playlistsImported++;
                            }

                            $plVideoIds = getPlaylistVideoIds($plYtId);
                            if (empty($plVideoIds)) continue;

                            $newPlVideoIds = [];
                            foreach ($plVideoIds as $vid) {
                                if ($totalNewVideos + count($newPlVideoIds) >= $maxVideosPerRun) break;
                                $stmt = $db->prepare("SELECT id FROM videos WHERE youtube_id = ?");
                                $stmt->execute([$vid]);
                                if (!$stmt->fetch()) $newPlVideoIds[] = $vid;
                            }

                            if (!empty($newPlVideoIds)) {
                                $apiDetails = getVideoDetailsAPI($newPlVideoIds);

                                usort($newPlVideoIds, function($a, $b) use ($apiDetails) {
                                    $fa = isset($apiDetails[$a]) ? $apiDetails[$a]['fecha'] : '';
                                    $fb = isset($apiDetails[$b]) ? $apiDetails[$b]['fecha'] : '';
                                    return strcmp($fb, $fa);
                                });

                                foreach ($newPlVideoIds as $vid) {
                                    if ($totalNewVideos >= $maxVideosPerRun) break;
                                    $meta = $apiDetails[$vid] ?? null;
                                    if (!$meta) continue;
                                    $stmt = $db->prepare("INSERT INTO videos (youtube_id, titulo, descripcion, canal_id, categoria_id, duracion, vistas_yt, fecha_yt, tags, agregado_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                    $stmt->execute([
                                        $vid, $meta['titulo'], $meta['descripcion'],
                                        $canalDbId, $catId, $meta['duracion'],
                                        $meta['vistas'], $meta['fecha'], $meta['tags'],
                                        $_SESSION['admin_nombre'] ?? 'admin'
                                    ]);
                                    $imported++;
                                    $totalNewVideos++;
                                }
                            }

                            $orden = 0;
                            foreach ($plVideoIds as $vid) {
                                $stmt = $db->prepare("SELECT id FROM videos WHERE youtube_id = ?");
                                $stmt->execute([$vid]);
                                $videoRow = $stmt->fetch();
                                if ($videoRow) {
                                    $stmt = $db->prepare("INSERT IGNORE INTO playlist_videos (playlist_id, video_id, orden) VALUES (?, ?, ?)");
                                    $stmt->execute([$plDbId, $videoRow['id'], $orden]);
                                }
                                $orden++;
                            }
                        }

                        $parts = [];
                        if ($imported > 0) $parts[] = "$imported videos nuevos";
                        if ($playlistsImported > 0) $parts[] = "$playlistsImported playlists nuevas";
                        $selectedCount = count($selectedPlaylists);
                        if ($selectedCount > 0 && $playlistsImported === 0) $parts[] = "$selectedCount playlists actualizadas";
                        if (!empty($parts)) {
                            $msg = "Importación completada: " . implode(', ', $parts) . ".";
                            if ($totalNewVideos >= $maxVideosPerRun) {
                                $msg .= " Se alcanzó el límite de $maxVideosPerRun videos por ejecución. Ejecutá de nuevo para continuar.";
                            }
                        } else if (empty($msg)) {
                            $msg = "Todo al día. No hay contenido nuevo para importar.";
                        }
                    }
                }
            }
        }

        // ── Add channel ──
        if ($action === 'add_channel') {
            $stmt = $db->prepare("INSERT INTO canales (nombre, youtube_channel_id, codigo, color, descripcion, prioridad_portada, auto_sync, default_categoria_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nombre'], $_POST['youtube_channel_id'] ?? '',
                $_POST['codigo'], $_POST['color'] ?? '#2e8b47',
                $_POST['descripcion'] ?? '',
                intval($_POST['prioridad_portada'] ?? 0),
                isset($_POST['auto_sync']) ? 1 : 0,
                $_POST['default_categoria_id'] ?: null
            ]);
            $msg = 'Canal creado correctamente.'; $msgType = 'success';
        }

        // ── Edit channel ──
        if ($action === 'edit_channel') {
            $stmt = $db->prepare("UPDATE canales SET nombre=?, youtube_channel_id=?, codigo=?, color=?, descripcion=?, prioridad_portada=?, auto_sync=?, default_categoria_id=?, nota_interna=? WHERE id=?");
            $stmt->execute([
                $_POST['nombre'], $_POST['youtube_channel_id'] ?? '',
                $_POST['codigo'], $_POST['color'] ?? '#2e8b47',
                $_POST['descripcion'] ?? '',
                intval($_POST['prioridad_portada'] ?? 0),
                isset($_POST['auto_sync']) ? 1 : 0,
                $_POST['default_categoria_id'] ?: null,
                $_POST['nota_interna'] ?? '',
                $_POST['canal_id']
            ]);
            $msg = 'Canal actualizado.'; $msgType = 'success';
        }

        // ── Toggle auto_sync ──
        if ($action === 'toggle_auto_sync') {
            $canalId = intval($_POST['canal_id']);
            $db->prepare("UPDATE canales SET auto_sync = NOT auto_sync WHERE id = ?")->execute([$canalId]);
            $msg = 'Auto-sync actualizado.'; $msgType = 'success';
        }

        // ── Toggle portada ──
        if ($action === 'toggle_portada') {
            $catId = intval($_POST['categoria_id']);
            $db->prepare("UPDATE categorias SET mostrar_en_portada = NOT mostrar_en_portada WHERE id = ?")->execute([$catId]);
            $msg = 'Portada actualizada.'; $msgType = 'success';
        }

        // ── Sync channel metadata from YouTube ──
        if ($action === 'sync_channel_metadata') {
            $canalId = intval($_POST['canal_id']);
            $stmtCh = $db->prepare("SELECT youtube_channel_id FROM canales WHERE id = ?");
            $stmtCh->execute([$canalId]);
            $ch = $stmtCh->fetch();
            if ($ch && $ch['youtube_channel_id']) {
                $info = syncChannelMetadata($db, $ch['youtube_channel_id'], $canalId);
                if ($info) {
                    $msg = 'Metadatos sincronizados: thumbnail, banner, stats actualizados.'; $msgType = 'success';
                } else {
                    $msg = 'Error al obtener datos de YouTube.'; $msgType = 'error';
                }
            } else {
                $msg = 'El canal no tiene YouTube Channel ID.'; $msgType = 'error';
            }
        }

        // ── Reorder channel priority ──
        if ($action === 'move_channel') {
            $canalId = intval($_POST['canal_id']);
            $direction = $_POST['direction']; // 'up' or 'down'

            // Obtener canales con portada ordenados por prioridad desc
            $allCh = $db->query("
                SELECT c.id, c.prioridad_portada FROM canales c
                JOIN categorias cat ON c.default_categoria_id = cat.id
                WHERE c.activo = 1 AND cat.mostrar_en_portada = 1
                ORDER BY c.prioridad_portada DESC, c.nombre
            ")->fetchAll();

            // Reasignar prioridades secuenciales para evitar duplicados
            $total = count($allCh);
            foreach ($allCh as $i => $ch) {
                $db->prepare("UPDATE canales SET prioridad_portada = ? WHERE id = ?")->execute([$total - $i, $ch['id']]);
                $allCh[$i]['prioridad_portada'] = $total - $i;
            }

            // Encontrar posición actual
            $pos = -1;
            foreach ($allCh as $i => $ch) {
                if ($ch['id'] == $canalId) { $pos = $i; break; }
            }

            if ($pos >= 0) {
                $swapPos = ($direction === 'up') ? $pos - 1 : $pos + 1;
                if ($swapPos >= 0 && $swapPos < $total) {
                    // Intercambiar prioridades
                    $pA = $allCh[$pos]['prioridad_portada'];
                    $pB = $allCh[$swapPos]['prioridad_portada'];
                    $db->prepare("UPDATE canales SET prioridad_portada = ? WHERE id = ?")->execute([$pB, $canalId]);
                    $db->prepare("UPDATE canales SET prioridad_portada = ? WHERE id = ?")->execute([$pA, $allCh[$swapPos]['id']]);
                    $msg = 'Prioridad actualizada.'; $msgType = 'success';
                }
            }
        }

        // ── Add category ──
        if ($action === 'add_categoria') {
            $stmt = $db->prepare("INSERT INTO categorias (nombre, icono, orden, mostrar_en_portada) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['nombre'], $_POST['icono'] ?? '📚', intval($_POST['orden'] ?? 0), isset($_POST['mostrar_en_portada']) ? 1 : 0]);
            $msg = 'Categoría creada correctamente.'; $msgType = 'success';
        }

        // ── Edit category ──
        if ($action === 'edit_categoria') {
            $stmt = $db->prepare("UPDATE categorias SET nombre=?, icono=?, orden=?, mostrar_en_portada=? WHERE id=?");
            $stmt->execute([
                $_POST['nombre'], $_POST['icono'] ?? '📚',
                intval($_POST['orden'] ?? 0),
                isset($_POST['mostrar_en_portada']) ? 1 : 0,
                $_POST['categoria_id']
            ]);
            $msg = 'Categoría actualizada.'; $msgType = 'success';
        }

        // ── Toggle video ──
        if ($action === 'toggle_video') {
            $stmt = $db->prepare("UPDATE videos SET activo = NOT activo WHERE id = ?");
            $stmt->execute([$_POST['video_id']]);
            $msg = 'Estado del video actualizado.'; $msgType = 'success';
        }

        // ── Delete video ──
        if ($action === 'delete_video') {
            $stmt = $db->prepare("DELETE FROM videos WHERE id = ?");
            $stmt->execute([$_POST['video_id']]);
            $msg = 'Video eliminado.'; $msgType = 'success';
        }

        // ── Add IA content ──
        if ($action === 'add_ia') {
            $slug = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['slug'] ?? '');
            $ia_id = trim($_POST['ia_id'] ?? '');
            $titulo = trim($_POST['titulo'] ?? '');
            if (!$slug || !$ia_id || !$titulo) {
                $msg = 'Slug, ID de Archive.org y título son obligatorios.'; $msgType = 'error';
            } else {
                $dup = $db->prepare("SELECT id FROM contenido_ia WHERE slug = ?");
                $dup->execute([$slug]);
                if ($dup->fetch()) {
                    $msg = 'Ya existe un contenido con ese slug.'; $msgType = 'error';
                } else {
                    $seccionVal = in_array($_POST['seccion'] ?? '', ['cine', 'audiolibros', 'libros']) ? $_POST['seccion'] : 'cine';
                    $stmt = $db->prepare("INSERT INTO contenido_ia (slug, ia_id, titulo, director, year, duracion, genero, descripcion, agregado_por, seccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $slug, $ia_id, $titulo,
                        trim($_POST['director'] ?? ''),
                        intval($_POST['year'] ?? 0) ?: null,
                        trim($_POST['duracion'] ?? ''),
                        validarGenero($_POST['genero'] ?? ''),
                        trim($_POST['descripcion'] ?? ''),
                        $_SESSION['admin_nombre'] ?? 'admin',
                        $seccionVal
                    ]);
                    $msg = 'Contenido "' . e($titulo) . '" agregado.'; $msgType = 'success';
                }
            }
        }

        // ── Edit IA content ──
        if ($action === 'edit_ia') {
            $id = intval($_POST['ia_content_id'] ?? 0);
            $stmt = $db->prepare("UPDATE contenido_ia SET slug=?, ia_id=?, titulo=?, director=?, year=?, duracion=?, genero=?, descripcion=? WHERE id=?");
            $stmt->execute([
                preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['slug'] ?? ''),
                trim($_POST['ia_id'] ?? ''),
                trim($_POST['titulo'] ?? ''),
                trim($_POST['director'] ?? ''),
                intval($_POST['year'] ?? 0) ?: null,
                trim($_POST['duracion'] ?? ''),
                validarGenero($_POST['genero'] ?? ''),
                trim($_POST['descripcion'] ?? ''),
                $id
            ]);
            $msg = 'Contenido actualizado.'; $msgType = 'success';
        }

        // ── Toggle IA content ──
        if ($action === 'toggle_ia') {
            $stmt = $db->prepare("UPDATE contenido_ia SET activo = NOT activo WHERE id = ?");
            $stmt->execute([$_POST['ia_content_id']]);
            $msg = 'Estado actualizado.'; $msgType = 'success';
        }

        // ── Delete IA content ──
        if ($action === 'delete_ia') {
            $stmt = $db->prepare("DELETE FROM contenido_ia WHERE id = ?");
            $stmt->execute([$_POST['ia_content_id']]);
            $msg = 'Contenido eliminado.'; $msgType = 'success';
        }

        // ── Bulk actions IA ──
        if (in_array($action, ['bulk_activar_ia', 'bulk_desactivar_ia', 'bulk_bloquear_ia', 'bulk_eliminar_ia'])) {
            $ids = json_decode($_POST['ids'] ?? '[]', true);
            if (is_array($ids) && count($ids) > 0) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $intIds = array_map('intval', $ids);
                if ($action === 'bulk_activar_ia') {
                    $db->prepare("UPDATE contenido_ia SET activo = 1, bloqueado = 0 WHERE id IN ($placeholders)")->execute($intIds);
                    $msg = count($intIds) . ' items activados.';
                } elseif ($action === 'bulk_desactivar_ia') {
                    $db->prepare("UPDATE contenido_ia SET activo = 0 WHERE id IN ($placeholders)")->execute($intIds);
                    $msg = count($intIds) . ' items desactivados.';
                } elseif ($action === 'bulk_bloquear_ia') {
                    $db->prepare("UPDATE contenido_ia SET activo = 0, bloqueado = 1 WHERE id IN ($placeholders)")->execute($intIds);
                    $msg = count($intIds) . ' items bloqueados.';
                } elseif ($action === 'bulk_eliminar_ia') {
                    $db->prepare("DELETE FROM contenido_ia WHERE id IN ($placeholders)")->execute($intIds);
                    $msg = count($intIds) . ' items eliminados.';
                }
                $msgType = 'success';
            }
        }

        // ── Fetch IA metadata ──
        if ($action === 'fetch_ia_meta') {
            $ia_id = trim($_POST['ia_id'] ?? '');
            if ($ia_id) {
                $json = @file_get_contents('https://archive.org/metadata/' . urlencode($ia_id));
                if ($json) {
                    $meta = json_decode($json, true);
                    $md = $meta['metadata'] ?? [];
                    $fetchedTitle = $md['title'] ?? '';
                    $fetchedYear = $md['year'] ?? ($md['date'] ? substr($md['date'], 0, 4) : '');
                    $fetchedDesc = $md['description'] ?? '';
                    if (is_array($fetchedDesc)) $fetchedDesc = implode("\n", $fetchedDesc);
                    $fetchedDesc = strip_tags($fetchedDesc);
                    $msg = 'Metadatos obtenidos de Archive.org.'; $msgType = 'success';
                } else {
                    $msg = 'No se pudo obtener metadatos de Archive.org.'; $msgType = 'error';
                }
            }
        }

        // ── Bulk import IA content ──
        if ($action === 'bulk_import_ia') {
            $items = json_decode($_POST['items'] ?? '[]', true);
            $imported = 0;
            $skipped = 0;
            if (is_array($items)) {
                $activo = isset($_POST['activo']) ? intval($_POST['activo']) : 1;
                $seccionVal = in_array($_POST['seccion'] ?? '', ['cine', 'audiolibros', 'libros']) ? $_POST['seccion'] : 'cine';
                $stmt = $db->prepare("INSERT INTO contenido_ia (slug, ia_id, titulo, director, year, duracion, genero, descripcion, agregado_por, activo, seccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $dupCheck = $db->prepare("SELECT id FROM contenido_ia WHERE ia_id = ?");
                foreach ($items as $item) {
                    $ia_id = trim($item['ia_id'] ?? '');
                    if (!$ia_id) continue;
                    $dupCheck->execute([$ia_id]);
                    if ($dupCheck->fetch()) { $skipped++; continue; }
                    $slug = preg_replace('/[^a-zA-Z0-9_-]/', '', $item['slug'] ?? $ia_id);
                    $baseSlug = $slug;
                    $n = 1;
                    $slugCheck = $db->prepare("SELECT id FROM contenido_ia WHERE slug = ?");
                    $slugCheck->execute([$slug]);
                    while ($slugCheck->fetch()) {
                        $slug = $baseSlug . '_' . $n++;
                        $slugCheck->execute([$slug]);
                    }
                    $titulo = trim($item['titulo'] ?? $ia_id);
                    $director = trim($item['director'] ?? '');
                    $year = intval($item['year'] ?? 0) ?: null;
                    $duracion = trim($item['duracion'] ?? '');
                    $genero = validarGenero($item['genero'] ?? '');
                    $descripcion = trim($item['descripcion'] ?? '');
                    $stmt->execute([$slug, $ia_id, $titulo, $director, $year, $duracion, $genero, $descripcion, $_SESSION['admin_nombre'] ?? 'admin', $activo, $seccionVal]);
                    $imported++;
                }
            }
            $msg = "Importados: $imported" . ($skipped ? " (ya existían: $skipped)" : '');
            $msgType = $imported > 0 ? 'success' : 'error';
        }

        // ── Change password ──
        if ($action === 'change_password') {
            $newPass = $_POST['new_password'] ?? '';
            if (strlen($newPass) < 8) {
                $msg = 'La contraseña debe tener al menos 8 caracteres.'; $msgType = 'error';
            } else {
                $hash = password_hash($newPass, PASSWORD_BCRYPT);
                $stmt = $db->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
                $stmt->execute([$hash, $_SESSION[ADMIN_SESSION_NAME]]);
                $msg = 'Contraseña actualizada.'; $msgType = 'success';
            }
        }

        // Regenerate CSRF
        $csrf = generateCSRF();
    }
}

// Stats
$totalVideos = $db->query("SELECT COUNT(*) FROM videos")->fetchColumn();
$totalVistas = $db->query("SELECT COUNT(*) FROM registro_vistas")->fetchColumn();
$totalCanales = $db->query("SELECT COUNT(*) FROM canales")->fetchColumn();
$totalCategorias = $db->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
try { $totalCine = $db->query("SELECT COUNT(*) FROM contenido_ia")->fetchColumn(); } catch (Exception $e) { $totalCine = 0; }

// Data
$videos = $db->query("SELECT v.*, c.nombre as canal_nombre, cat.nombre as cat_nombre FROM videos v LEFT JOIN canales c ON v.canal_id = c.id LEFT JOIN categorias cat ON v.categoria_id = cat.id ORDER BY v.created_at DESC LIMIT 50")->fetchAll();
$canales = $db->query("SELECT * FROM canales ORDER BY nombre")->fetchAll();
$categorias = $db->query("SELECT * FROM categorias ORDER BY orden")->fetchAll();

$section = $_GET['s'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTube — Admin</title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#f5f5f5; color:#1a1a1a; }
        .admin-layout { display:flex; min-height:100vh; }
        .admin-sidebar { width:220px; background:#fff; border-right:1px solid #e0e0e0; padding:1rem 0; flex-shrink:0; }
        .admin-sidebar .logo { display:flex; align-items:center; gap:0.4rem; padding:0.5rem 1rem 1.5rem; }
        .admin-sidebar .logo img { width:24px; }
        .admin-sidebar .logo span { font-weight:700; color:#2e8b47; }
        .admin-sidebar a { display:block; padding:0.55rem 1rem; color:#555; font-size:0.88rem; text-decoration:none; border-left:3px solid transparent; }
        .admin-sidebar a:hover { background:#f5f5f5; }
        .admin-sidebar a.active { background:#eef7f0; color:#2e8b47; border-left-color:#2e8b47; font-weight:600; }
        .admin-main { flex:1; padding:1.5rem 2rem; overflow-x:auto; }
        .admin-main h1 { font-size:1.4rem; margin-bottom:1.5rem; }
        .stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:2rem; }
        .stat-card { background:#fff; border-radius:12px; padding:1.25rem; border:1px solid #e0e0e0; }
        .stat-card .stat-num { font-size:1.8rem; font-weight:700; color:#2e8b47; }
        .stat-card .stat-label { font-size:0.8rem; color:#888; margin-top:0.2rem; }
        .card { background:#fff; border-radius:12px; padding:1.5rem; border:1px solid #e0e0e0; margin-bottom:1.5rem; }
        .card h2 { font-size:1.1rem; margin-bottom:1rem; }
        .form-row { display:flex; gap:1rem; margin-bottom:0.8rem; flex-wrap:wrap; }
        .form-group { flex:1; min-width:200px; }
        .form-group label { display:block; font-size:0.8rem; font-weight:500; color:#555; margin-bottom:0.2rem; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:0.5rem 0.7rem; border:1px solid #ddd; border-radius:8px; font-size:0.88rem; font-family:inherit; }
        .form-group textarea { height:80px; resize:vertical; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline:none; border-color:#2e8b47; }
        .btn { padding:0.5rem 1.2rem; border:none; border-radius:8px; font-size:0.88rem; font-weight:500; cursor:pointer; font-family:inherit; }
        .btn-primary { background:#2e8b47; color:#fff; }
        .btn-primary:hover { background:#38a555; }
        .btn-danger { background:#e63946; color:#fff; }
        .btn-sm { padding:0.3rem 0.7rem; font-size:0.78rem; }
        .btn-outline { background:none; border:1px solid #ddd; color:#555; }
        .btn-outline:hover { background:#f5f5f5; }
        table { width:100%; border-collapse:collapse; font-size:0.85rem; }
        th { text-align:left; padding:0.6rem; background:#f9f9f9; border-bottom:1px solid #e0e0e0; font-weight:600; font-size:0.78rem; color:#888; text-transform:uppercase; }
        td { padding:0.5rem 0.6rem; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
        .thumb-sm { width:80px; border-radius:4px; }
        .badge { padding:2px 8px; border-radius:10px; font-size:0.72rem; font-weight:500; }
        .badge-active { background:#eef7f0; color:#2e8b47; }
        .badge-inactive { background:#fee; color:#c00; }
        .badge-btn { padding:2px 10px; border-radius:10px; font-size:0.72rem; font-weight:500; border:1px solid transparent; cursor:pointer; font-family:inherit; transition:opacity 0.15s, border-color 0.15s; }
        .badge-btn:hover { opacity:0.8; border-color:currentColor; }
        .msg { padding:0.7rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:0.88rem; }
        .msg-success { background:#eef7f0; color:#1f6e34; border:1px solid #c3e6cb; }
        .msg-error { background:#fee; color:#c00; border:1px solid #f5c6cb; }
        @media (max-width:768px) {
            .admin-layout { flex-direction:column; }
            .admin-sidebar { width:100%; display:flex; overflow-x:auto; padding:0.5rem; gap:0.25rem; border-right:none; border-bottom:1px solid #e0e0e0; }
            .admin-sidebar .logo { display:none; }
            .admin-sidebar a { white-space:nowrap; border-left:none; border-bottom:2px solid transparent; padding:0.4rem 0.8rem; font-size:0.8rem; }
            .admin-sidebar a.active { border-left:none; border-bottom-color:#2e8b47; }
            .admin-main { padding:1rem; }
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <nav class="admin-sidebar">
        <div class="logo">
            <img src="loguito-edutube.png" alt="">
            <span>EduTube Admin</span>
        </div>
        <a href="?s=dashboard" class="<?= $section==='dashboard'?'active':'' ?>">📊 Dashboard</a>
        <a href="?s=videos" class="<?= $section==='videos'?'active':'' ?>">🎬 Videos</a>
        <a href="?s=add_video" class="<?= $section==='add_video'?'active':'' ?>">➕ Agregar video</a>
        <a href="?s=import" class="<?= $section==='import'?'active':'' ?>">📥 Importar canal</a>
        <a href="?s=canales" class="<?= $section==='canales'?'active':'' ?>">📺 Canales</a>
        <a href="?s=categorias" class="<?= $section==='categorias'?'active':'' ?>">🏷 Categorías</a>
        <a href="?s=contenido_ia" class="<?= $section==='contenido_ia'?'active':'' ?>">🎬 Cine</a>
        <a href="?s=audiolibros" class="<?= $section==='audiolibros'?'active':'' ?>">📖 Audiolibros</a>
        <a href="?s=libros" class="<?= $section==='libros'?'active':'' ?>">📚 Libros</a>
        <a href="?s=portada" class="<?= $section==='portada'?'active':'' ?>">🏠 Portada</a>
        <a href="?s=password" class="<?= $section==='password'?'active':'' ?>">🔑 Contraseña</a>
        <a href="/" target="_blank">🌐 Ver sitio</a>
        <a href="?logout=1">🚪 Salir</a>
    </nav>

    <main class="admin-main">
        <?php if ($msg): ?>
            <div class="msg msg-<?= $msgType ?>"><?= e($msg) ?></div>
        <?php endif; ?>

        <?php if ($section === 'dashboard'): ?>
            <h1>Dashboard</h1>
            <div class="stats">
                <div class="stat-card"><div class="stat-num"><?= $totalVideos ?></div><div class="stat-label">Videos</div></div>
                <div class="stat-card"><div class="stat-num"><?= $totalCanales ?></div><div class="stat-label">Canales</div></div>
                <div class="stat-card"><div class="stat-num"><?= $totalCategorias ?></div><div class="stat-label">Categorías</div></div>
                <div class="stat-card"><div class="stat-num"><?= $totalCine ?></div><div class="stat-label">Cine (IA)</div></div>
                <div class="stat-card"><div class="stat-num"><?= $totalVistas ?></div><div class="stat-label">Vistas en EduTube</div></div>
            </div>

        <?php elseif ($section === 'add_video'): ?>
            <h1>Agregar video</h1>
            <div class="card">
                <form method="POST">
                    <input type="hidden" name="action" value="add_video">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label>URL de YouTube *</label>
                            <input type="text" name="youtube_url" placeholder="https://www.youtube.com/watch?v=..." required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Título (dejar vacío para autocompletar)</label>
                            <input type="text" name="titulo">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Canal</label>
                            <select name="canal_id">
                                <option value="">— Sin canal —</option>
                                <?php foreach ($canales as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= e($c['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Categoría</label>
                            <select name="categoria_id">
                                <option value="">— Sin categoría —</option>
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= e($c['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tags (separados por coma)</label>
                            <input type="text" name="tags" placeholder="educación, derechos, argentina">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Descripción (dejar vacío para autocompletar)</label>
                            <textarea name="descripcion"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="this.disabled=true;this.innerHTML='⏳ Agregando...';this.form.submit();">Agregar video</button>
                </form>
            </div>

        <?php elseif ($section === 'import'): ?>
            <h1>Importar canal</h1>

            <?php
            // ── Preview mode ──
            $preview = null;
            $previewChId = '';
            if (isset($_GET['preview_url'])) {
                $previewChId = extractChannelId($_GET['preview_url']);
                if ($previewChId) {
                    $preview = getChannelInfo($previewChId);
                    if ($preview) {
                        $preview['playlists'] = getChannelPlaylists($previewChId);
                        $preview['channel_id'] = $previewChId;
                    }
                }
            }
            ?>

            <!-- Step 1: Enter URL -->
            <div class="card">
                <h2>1. Pegar URL del canal</h2>
                <form method="GET" action="admin.php">
                    <input type="hidden" name="s" value="import">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="preview_url" placeholder="https://www.youtube.com/@canal" value="<?= e($_GET['preview_url'] ?? '') ?>" required>
                        </div>
                        <div><button type="submit" class="btn btn-primary" style="margin-top:2px;">Obtener info</button></div>
                    </div>
                </form>
            </div>

            <?php if (isset($_GET['preview_url']) && !$preview): ?>
                <div class="msg msg-error">No se pudo obtener información del canal. Verificá la URL.</div>
            <?php endif; ?>

            <?php if ($preview): ?>
            <!-- Step 2: Preview -->
            <div class="card">
                <h2>2. Información del canal</h2>
                <div style="display:flex;gap:1rem;align-items:center;margin-bottom:1rem;">
                    <?php if ($preview['thumbnail']): ?>
                        <img src="<?= e($preview['thumbnail']) ?>" style="width:64px;height:64px;border-radius:50%;">
                    <?php endif; ?>
                    <div>
                        <div style="font-size:1.1rem;font-weight:600;"><?= e($preview['nombre']) ?></div>
                        <div style="font-size:0.85rem;color:#888;">
                            <?= number_format($preview['suscriptores']) ?> suscriptores ·
                            <?= number_format($preview['total_videos']) ?> videos
                        </div>
                        <div style="font-size:0.8rem;color:#aaa;">Channel ID: <?= e($previewChId) ?></div>
                    </div>
                </div>
                <?php if ($preview['descripcion']): ?>
                    <div style="font-size:0.85rem;color:#666;margin-bottom:1rem;max-height:80px;overflow:hidden;"><?= nl2br(e(mb_substr($preview['descripcion'], 0, 300))) ?></div>
                <?php endif; ?>

            </div>

            <!-- Step 3: Confirm import -->
            <div class="card">
                <h2>3. Confirmar importación</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="import_channel">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <input type="hidden" name="channel_url" value="<?= e($_GET['preview_url']) ?>">
                    <input type="hidden" name="canal_nombre" value="<?= e($preview['nombre']) ?>">
                    <input type="hidden" name="canal_codigo" value="CH">
                    <input type="hidden" name="canal_color" value="#2e8b47">

                    <?php
                    // Check which playlists are already imported
                    // Also get the real accessible count from YouTube API
                    $importedPlaylists = [];
                    $stmtImp = $db->query("SELECT youtube_playlist_id, (SELECT COUNT(*) FROM playlist_videos pv WHERE pv.playlist_id = playlists.id) AS imported_count FROM playlists");
                    foreach ($stmtImp->fetchAll() as $ip) {
                        if ($ip['youtube_playlist_id']) $importedPlaylists[$ip['youtube_playlist_id']] = intval($ip['imported_count']);
                    }
                    ?>

                    <!-- Sync all -->
                    <div style="background:#eef7f0;border:1px solid #c3e6cb;border-radius:8px;padding:0.75rem 1rem;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.88rem;font-weight:500;">
                            <input type="checkbox" name="sync_all" value="1">
                            ⚡ Sincronizar todo (máx 50 videos por vez)
                        </label>
                        <span style="font-size:0.78rem;color:#555;">Ejecutá varias veces hasta completar.</span>
                    </div>

                    <!-- Playlists list with "Sin lista" first -->
                    <?php if (!empty($preview['playlists'])): ?>
                    <?php
                        // Count videos not in any playlist (via uploads playlist)
                        $uploadsId = $preview['uploads_playlist'] ?? '';
                        // "Sin lista" = special entry for loose videos
                        // Get channel DB id
                        $stmtChDb = $db->prepare("SELECT id, default_categoria_id FROM canales WHERE youtube_channel_id = ?");
                        $stmtChDb->execute([$previewChId]);
                        $chDbRow = $stmtChDb->fetch();
                        $canalDbIdPreview = $chDbRow ? intval($chDbRow['id']) : 0;
                        $canalDefaultCatId = $chDbRow ? intval($chDbRow['default_categoria_id']) : 0;

                        // Count videos in this channel that are NOT in any playlist
                        $sinListaCount = 0;
                        if ($canalDbIdPreview > 0) {
                            $stmtSL = $db->prepare("SELECT COUNT(*) FROM videos v WHERE v.canal_id = ? AND v.activo = 1 AND v.id NOT IN (SELECT video_id FROM playlist_videos)");
                            $stmtSL->execute([$canalDbIdPreview]);
                            $sinListaCount = intval($stmtSL->fetchColumn());
                        }

                        // Estimate total loose videos from YouTube data
                        $totalEnPlaylists = 0;
                        foreach ($preview['playlists'] as $pl) $totalEnPlaylists += $pl['total_videos'];
                        $estimadoSueltos = max(0, $preview['total_videos'] - $totalEnPlaylists);

                        // Total imported for this channel
                        $totalImportados = 0;
                        if ($canalDbIdPreview > 0) {
                            $stmtTot = $db->prepare("SELECT COUNT(*) FROM videos WHERE canal_id = ? AND activo = 1");
                            $stmtTot->execute([$canalDbIdPreview]);
                            $totalImportados = intval($stmtTot->fetchColumn());
                        }
                    ?>
                    <div style="background:#f9f9f9;border:1px solid #e0e0e0;border-radius:8px;padding:1rem;margin-bottom:0.75rem;">
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.9rem;font-weight:500;margin-bottom:0.5rem;">
                            Listas (<?= count($preview['playlists']) + 1 ?>)
                        </label>
                        <div style="max-height:350px;overflow-y:auto;">
                            <!-- Sin lista (loose videos) -->
                            <?php
                                $sinListaComplete = $estimadoSueltos > 0 && $sinListaCount > 0 && ($sinListaCount / max($estimadoSueltos, 1) >= 0.90);
                                if ($estimadoSueltos <= 0) $sinListaComplete = $totalImportados >= $preview['total_videos'] * 0.95;
                            ?>
                            <label style="display:flex;align-items:center;gap:0.5rem;padding:0.35rem 0;font-size:0.85rem;cursor:pointer;font-weight:500;border-bottom:1px solid #e0e0e0;margin-bottom:0.3rem;padding-bottom:0.5rem;<?= $sinListaComplete ? 'opacity:0.5;' : '' ?>">
                                <input type="checkbox" name="import_latest" value="1" <?= $sinListaComplete ? 'disabled' : '' ?>>
                                📂 Sin lista (videos sueltos)
                                <span style="margin-left:auto;font-size:0.78rem;">
                                    <?php if ($sinListaComplete): ?>
                                        <span style="color:#2e8b47;">✓ <?= $sinListaCount ?: $totalImportados ?></span>
                                    <?php elseif ($sinListaCount > 0 && $estimadoSueltos > 0): ?>
                                        <span style="color:#f77f00;">↻ <?= $sinListaCount ?>/~<?= $estimadoSueltos ?></span>
                                    <?php elseif ($totalImportados > 0 && $estimadoSueltos > 0): ?>
                                        <span style="color:#888;">~<?= $estimadoSueltos ?> sueltos</span>
                                    <?php else: ?>
                                        <span style="color:#888;">~<?= $estimadoSueltos > 0 ? $estimadoSueltos : $preview['total_videos'] ?> videos</span>
                                    <?php endif; ?>
                                </span>
                                <input type="hidden" name="limit" value="50">
                            </label>
                            <?php foreach ($preview['playlists'] as $pl):
                                $isImported = isset($importedPlaylists[$pl['youtube_id']]);
                                $importedCount = $isImported ? $importedPlaylists[$pl['youtube_id']] : 0;
                                $ytTotal = $pl['total_videos'];
                                $isComplete = $isImported && ($importedCount >= $ytTotal || ($ytTotal > 0 && $importedCount / $ytTotal >= 0.95));
                                $needsUpdate = $isImported && !$isComplete;
                            ?>
                            <label style="display:flex;align-items:center;gap:0.5rem;padding:0.35rem 0;font-size:0.85rem;cursor:pointer;<?= $isComplete ? 'opacity:0.5;' : '' ?>">
                                <input type="checkbox" name="playlists[]" value="<?= e($pl['youtube_id']) ?>" <?= $isComplete ? 'disabled' : '' ?>>
                                <?= e($pl['nombre']) ?>
                                <span style="margin-left:auto;font-size:0.78rem;">
                                    <?php if ($isComplete): ?>
                                        <span style="color:#2e8b47;">✓ <?= $importedCount ?></span>
                                    <?php elseif ($needsUpdate): ?>
                                        <span style="color:#f77f00;">↻ <?= $importedCount ?>/<?= $ytTotal ?></span>
                                    <?php else: ?>
                                        <span style="color:#888;"><?= $ytTotal ?> videos</span>
                                    <?php endif; ?>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <div style="margin-top:0.5rem;">
                            <button type="button" class="btn btn-sm btn-outline" onclick="this.closest('div').querySelectorAll('input[type=checkbox]:not(:disabled)').forEach(function(c){c.checked=true});">Seleccionar pendientes</button>
                            <button type="button" class="btn btn-sm btn-outline" onclick="this.closest('div').querySelectorAll('input[type=checkbox]').forEach(function(c){c.checked=false});">Ninguna</button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-row" style="margin-top:0.75rem;">
                        <div class="form-group">
                            <label>Categoría por defecto</label>
                            <select name="categoria_id">
                                <option value="">— Sin categoría —</option>
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= (isset($canalDefaultCatId) && $canalDefaultCatId == $c['id']) ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="this.disabled=true;this.innerHTML='⏳ Importando... esto puede tardar un momento';this.form.submit();">Importar <?= e($preview['nombre']) ?></button>
                </form>
            </div>
            <?php endif; ?>

        <?php elseif ($section === 'videos'): ?>
            <h1>Videos (<?= $totalVideos ?>)</h1>
            <table>
                <tr><th></th><th>Título</th><th>Canal</th><th>Vistas YT</th><th>Estado</th><th>Acciones</th></tr>
                <?php foreach ($videos as $v): ?>
                <tr>
                    <td><img src="https://img.youtube.com/vi/<?= e($v['youtube_id']) ?>/default.jpg" class="thumb-sm"></td>
                    <td><a href="watch?v=<?= e($v['youtube_id']) ?>" target="_blank"><?= e(mb_substr($v['titulo'], 0, 60)) ?><?= mb_strlen($v['titulo'])>60?'...':'' ?></a></td>
                    <td><?= e($v['canal_nombre'] ?? '—') ?></td>
                    <td><?= number_format($v['vistas_yt']) ?></td>
                    <td><span class="badge <?= $v['activo']?'badge-active':'badge-inactive' ?>"><?= $v['activo']?'Activo':'Inactivo' ?></span></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="toggle_video">
                            <input type="hidden" name="video_id" value="<?= $v['id'] ?>">
                            <input type="hidden" name="csrf" value="<?= $csrf ?>">
                            <button class="btn btn-sm btn-outline"><?= $v['activo']?'Desactivar':'Activar' ?></button>
                        </form>
                        <form method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar este video?')">
                            <input type="hidden" name="action" value="delete_video">
                            <input type="hidden" name="video_id" value="<?= $v['id'] ?>">
                            <input type="hidden" name="csrf" value="<?= $csrf ?>">
                            <button class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($section === 'canales'): ?>
            <h1>Canales</h1>

            <?php
            $editCanal = null;
            if (isset($_GET['edit'])) {
                $stmtEdit = $db->prepare("SELECT * FROM canales WHERE id = ?");
                $stmtEdit->execute([$_GET['edit']]);
                $editCanal = $stmtEdit->fetch();
            }
            ?>

            <div class="card">
                <h2><?= $editCanal ? 'Editar canal' : 'Nuevo canal' ?></h2>
                <form method="POST">
                    <input type="hidden" name="action" value="<?= $editCanal ? 'edit_channel' : 'add_channel' ?>">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <?php if ($editCanal): ?>
                        <input type="hidden" name="canal_id" value="<?= $editCanal['id'] ?>">
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="form-group"><label>Nombre *</label><input type="text" name="nombre" value="<?= e($editCanal['nombre'] ?? '') ?>" required></div>
                        <div class="form-group"><label>YouTube Channel ID</label><input type="text" name="youtube_channel_id" placeholder="UC..." value="<?= e($editCanal['youtube_channel_id'] ?? '') ?>"></div>
                        <div class="form-group"><label>Código</label><input type="text" name="codigo" value="<?= e($editCanal['codigo'] ?? 'CH') ?>" maxlength="4"></div>
                        <div class="form-group"><label>Color</label><input type="color" name="color" value="<?= e($editCanal['color'] ?? '#2e8b47') ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Descripción</label><textarea name="descripcion"><?= e($editCanal['descripcion'] ?? '') ?></textarea></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Nota interna (solo admin)</label><textarea name="nota_interna" placeholder="Notas privadas sobre este canal..."><?= e($editCanal['nota_interna'] ?? '') ?></textarea></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Categoría por defecto</label>
                            <select name="default_categoria_id">
                                <option value="">— Sin categoría —</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($editCanal && $editCanal['default_categoria_id'] == $cat['id']) ? 'selected' : '' ?>><?= e($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:0.3rem;">
                            <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                                <input type="checkbox" name="auto_sync" value="1" <?= ($editCanal && $editCanal['auto_sync']) ? 'checked' : '' ?>>
                                Sincronizar automático (cron)
                            </label>
                        </div>
                    </div>
                    <?php if ($editCanal && !empty($editCanal['thumbnail_url'])): ?>
                    <div class="form-row" style="align-items:center;gap:1rem;margin-bottom:1rem;">
                        <img src="<?= e($editCanal['thumbnail_url']) ?>" alt="Thumbnail" style="width:60px;height:60px;border-radius:50%;object-fit:cover;">
                        <div style="font-size:0.82rem;color:var(--text-secondary);">
                            <?php if (!empty($editCanal['subscriber_count'])): ?>Suscriptores YT: <?= number_format($editCanal['subscriber_count']) ?> · <?php endif; ?>
                            <?php if (!empty($editCanal['video_count_yt'])): ?>Videos YT: <?= $editCanal['video_count_yt'] ?> · <?php endif; ?>
                            <?php if (!empty($editCanal['country'])): ?>País: <?= e($editCanal['country']) ?> · <?php endif; ?>
                            <?php if (!empty($editCanal['metadata_updated_at'])): ?>Última sync: <?= $editCanal['metadata_updated_at'] ?><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary"><?= $editCanal ? 'Guardar cambios' : 'Crear canal' ?></button>
                    <?php if ($editCanal): ?>
                        <a href="?s=canales" class="btn btn-outline" style="margin-left:0.5rem;">Cancelar</a>
                    <?php endif; ?>
                </form>
                <?php if ($editCanal && !empty($editCanal['youtube_channel_id'])): ?>
                <form method="POST" style="margin-top:0.75rem;display:inline;">
                    <input type="hidden" name="action" value="sync_channel_metadata">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <input type="hidden" name="canal_id" value="<?= $editCanal['id'] ?>">
                    <button type="submit" class="btn btn-outline" title="Descarga thumbnail, banner y estadísticas de YouTube">Sincronizar metadatos de YouTube</button>
                </form>
                <?php endif; ?>
            </div>
            <table>
                <tr><th></th><th>Nombre</th><th>Channel ID</th><th>Categoría</th><th>Auto-sync</th><th>Metadata</th><th>Acciones</th></tr>
                <?php foreach ($canales as $c):
                    $catNombre = '—';
                    if (!empty($c['default_categoria_id'])) {
                        foreach ($categorias as $cat) {
                            if ($cat['id'] == $c['default_categoria_id']) { $catNombre = $cat['nombre']; break; }
                        }
                    }
                ?>
                <tr>
                    <td>
                        <?php if (!empty($c['thumbnail_url'])): ?>
                            <img src="<?= e($c['thumbnail_url']) ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                        <?php else: ?>
                            <span style="display:inline-block;width:32px;height:32px;border-radius:50%;background:<?= e($c['color']) ?>;color:#fff;text-align:center;line-height:32px;font-size:0.7rem;font-weight:700;"><?= e($c['codigo']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><a href="canal.php?id=<?= $c['id'] ?>" target="_blank" style="color:var(--text);font-weight:500;"><?= e($c['nombre']) ?></a></td>
                    <td style="font-size:0.78rem;"><?= e($c['youtube_channel_id']) ?></td>
                    <td><?= e($catNombre) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="toggle_auto_sync">
                            <input type="hidden" name="csrf" value="<?= $csrf ?>">
                            <input type="hidden" name="canal_id" value="<?= $c['id'] ?>">
                            <button type="submit" class="badge-btn <?= !empty($c['auto_sync']) ? 'badge-active' : 'badge-inactive' ?>" title="Clic para cambiar"><?= !empty($c['auto_sync']) ? 'Sí' : 'No' ?></button>
                        </form>
                    </td>
                    <td>
                        <?php if (!empty($c['youtube_channel_id'])): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Sincronizar metadatos de YouTube para <?= e($c['nombre']) ?>?')">
                            <input type="hidden" name="action" value="sync_channel_metadata">
                            <input type="hidden" name="csrf" value="<?= $csrf ?>">
                            <input type="hidden" name="canal_id" value="<?= $c['id'] ?>">
                            <button type="submit" class="badge-btn <?= !empty($c['metadata_updated_at']) ? 'badge-active' : 'badge-inactive' ?>" title="Clic para sincronizar metadatos"><?= !empty($c['metadata_updated_at']) ? date('d/m', strtotime($c['metadata_updated_at'])) : 'Sin sync' ?></button>
                        </form>
                        <?php else: ?>
                        <span class="badge badge-inactive">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?s=canales&edit=<?= $c['id'] ?>" class="btn btn-sm btn-outline">Editar</a>
                        <?php if (!empty($c['youtube_channel_id'])): ?>
                        <a href="?s=import&preview_url=https://www.youtube.com/channel/<?= e($c['youtube_channel_id']) ?>" class="btn btn-sm btn-outline" style="margin-left:0.25rem;" title="Sincronizar videos y playlists">Sync</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($section === 'categorias'): ?>
            <h1>Categorías</h1>

            <?php
            $editCat = null;
            if (isset($_GET['edit_cat'])) {
                $stmtEditCat = $db->prepare("SELECT * FROM categorias WHERE id = ?");
                $stmtEditCat->execute([$_GET['edit_cat']]);
                $editCat = $stmtEditCat->fetch();
            }
            ?>

            <div class="card">
                <h2><?= $editCat ? 'Editar categoría' : 'Nueva categoría' ?></h2>
                <form method="POST">
                    <input type="hidden" name="action" value="<?= $editCat ? 'edit_categoria' : 'add_categoria' ?>">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <?php if ($editCat): ?>
                        <input type="hidden" name="categoria_id" value="<?= $editCat['id'] ?>">
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="form-group"><label>Nombre *</label><input type="text" name="nombre" value="<?= e($editCat['nombre'] ?? '') ?>" required></div>
                        <div class="form-group"><label>Icono (emoji)</label><input type="text" name="icono" value="<?= e($editCat['icono'] ?? '📚') ?>"></div>
                        <div class="form-group"><label>Orden</label><input type="number" name="orden" value="<?= $editCat['orden'] ?? 0 ?>"></div>
                        <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:0.3rem;">
                            <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                                <input type="checkbox" name="mostrar_en_portada" value="1" <?= ($editCat && !empty($editCat['mostrar_en_portada'])) ? 'checked' : '' ?>>
                                Mostrar en portada
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $editCat ? 'Guardar cambios' : 'Crear categoría' ?></button>
                    <?php if ($editCat): ?>
                        <a href="?s=categorias" class="btn btn-outline" style="margin-left:0.5rem;">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>
            <table>
                <tr><th>Icono</th><th>Nombre</th><th>Orden</th><th>Portada</th><th>Acciones</th></tr>
                <?php foreach ($categorias as $c): ?>
                <tr>
                    <td><?= $c['icono'] ?></td>
                    <td><?= e($c['nombre']) ?></td>
                    <td><?= $c['orden'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="toggle_portada">
                            <input type="hidden" name="csrf" value="<?= $csrf ?>">
                            <input type="hidden" name="categoria_id" value="<?= $c['id'] ?>">
                            <button type="submit" class="badge-btn <?= !empty($c['mostrar_en_portada']) ? 'badge-active' : 'badge-inactive' ?>" title="Clic para cambiar"><?= !empty($c['mostrar_en_portada']) ? 'Sí' : 'No' ?></button>
                        </form>
                    </td>
                    <td><a href="?s=categorias&edit_cat=<?= $c['id'] ?>" class="btn btn-sm btn-outline">Editar</a></td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($section === 'portada'): ?>
            <h1>Orden de Portada</h1>
            <p style="color:#888;font-size:0.85rem;margin-bottom:1rem;">Canales cuya categoría tiene "Mostrar en portada" activado. Usá las flechas para cambiar el orden.</p>

            <?php
            $portadaCanales = $db->query("
                SELECT c.id, c.nombre, c.codigo, c.color, c.prioridad_portada, cat.nombre AS cat_nombre
                FROM canales c
                JOIN categorias cat ON c.default_categoria_id = cat.id
                WHERE c.activo = 1 AND cat.mostrar_en_portada = 1
                ORDER BY c.prioridad_portada DESC, c.nombre
            ")->fetchAll();
            $totalPortada = count($portadaCanales);
            ?>

            <?php if ($totalPortada === 0): ?>
                <div class="msg msg-error">No hay canales con categorías de portada. Andá a Categorías y activá "Mostrar en portada" en alguna.</div>
            <?php else: ?>
            <table>
                <tr><th style="width:50px">#</th><th>Canal</th><th>Categoría</th><th>Prioridad</th><th style="width:120px">Orden</th></tr>
                <?php foreach ($portadaCanales as $i => $pc): ?>
                <tr>
                    <td style="font-weight:600;color:#888;"><?= $i + 1 ?></td>
                    <td>
                        <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:<?= e($pc['color']) ?>;color:#fff;text-align:center;line-height:24px;font-size:0.65rem;font-weight:700;vertical-align:middle;margin-right:0.4rem;"><?= e($pc['codigo']) ?></span>
                        <?= e($pc['nombre']) ?>
                    </td>
                    <td><?= e($pc['cat_nombre']) ?></td>
                    <td><?= $pc['prioridad_portada'] ?></td>
                    <td>
                        <?php if ($i > 0): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="move_channel">
                            <input type="hidden" name="canal_id" value="<?= $pc['id'] ?>">
                            <input type="hidden" name="direction" value="up">
                            <input type="hidden" name="csrf" value="<?= $csrf ?>">
                            <button class="btn btn-sm btn-outline" title="Subir">▲</button>
                        </form>
                        <?php endif; ?>
                        <?php if ($i < $totalPortada - 1): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="move_channel">
                            <input type="hidden" name="canal_id" value="<?= $pc['id'] ?>">
                            <input type="hidden" name="direction" value="down">
                            <input type="hidden" name="csrf" value="<?= $csrf ?>">
                            <button class="btn btn-sm btn-outline" title="Bajar">▼</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>

        <?php elseif ($section === 'contenido_ia' || $section === 'audiolibros' || $section === 'libros'):
            $isAudio = ($section === 'audiolibros');
            $isLibros = ($section === 'libros');
            $seccionDB = $isLibros ? 'libros' : ($isAudio ? 'audiolibros' : 'cine');
            $seccionLabel = $isLibros ? 'Libros' : ($isAudio ? 'Audiolibros' : 'Cine');
            $totalSeccion = $db->query("SELECT COUNT(*) FROM contenido_ia WHERE seccion = '$seccionDB'")->fetchColumn();
        ?>
            <h1><?= $seccionLabel ?> (<?= $totalSeccion ?>)</h1>

            <?php
            $editIA = null;
            if (isset($_GET['edit'])) {
                $stmtEdit = $db->prepare("SELECT * FROM contenido_ia WHERE id = ?");
                $stmtEdit->execute([$_GET['edit']]);
                $editIA = $stmtEdit->fetch();
            }
            ?>

            <div class="card">
                <h2><?= $editIA ? 'Editar contenido' : 'Agregar contenido de Internet Archive' ?></h2>
                <form method="POST" id="ia-form">
                    <input type="hidden" name="action" value="<?= $editIA ? 'edit_ia' : 'add_ia' ?>">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <input type="hidden" name="seccion" value="<?= $seccionDB ?>">
                    <?php if ($editIA): ?>
                        <input type="hidden" name="ia_content_id" value="<?= $editIA['id'] ?>">
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label>ID de Archive.org *</label>
                            <input type="text" name="ia_id" id="ia_id_input" value="<?= e($editIA['ia_id'] ?? (isset($fetchedTitle) ? ($_POST['ia_id'] ?? '') : '')) ?>" required placeholder="ej: Truffaut1969">
                        </div>
                        <div class="form-group" style="flex:0 0 auto;display:flex;align-items:flex-end;">
                            <button type="button" class="btn btn-outline" id="btn-fetch-meta" onclick="fetchIAMeta()">Obtener metadatos</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Slug * (ID corto, sin espacios)</label>
                            <input type="text" name="slug" value="<?= e($editIA['slug'] ?? '') ?>" required placeholder="ej: ElPequenoSalvaje" pattern="[a-zA-Z0-9_-]+">
                        </div>
                        <div class="form-group">
                            <label>Título *</label>
                            <input type="text" name="titulo" id="ia_titulo" value="<?= e($editIA['titulo'] ?? ($fetchedTitle ?? '')) ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Director</label>
                            <input type="text" name="director" value="<?= e($editIA['director'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Año</label>
                            <input type="number" name="year" value="<?= e($editIA['year'] ?? ($fetchedYear ?? '')) ?>" min="1800" max="2030">
                        </div>
                        <div class="form-group">
                            <label>Duración</label>
                            <input type="text" name="duracion" value="<?= e($editIA['duracion'] ?? '') ?>" placeholder="ej: 1:23:00">
                        </div>
                        <div class="form-group">
                            <label>Género</label>
                            <select name="genero">
                                <option value="">— Sin género —</option>
                                <?php
                                $generosLista = ['Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo'];
                                foreach ($generosLista as $g):
                                ?>
                                    <option value="<?= $g ?>" <?= (($editIA['genero'] ?? '') === $g) ? 'selected' : '' ?>><?= $g ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="descripcion"><?= e($editIA['descripcion'] ?? ($fetchedDesc ?? '')) ?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $editIA ? 'Guardar cambios' : 'Agregar' ?></button>
                    <?php if ($editIA): ?>
                        <a href="?s=contenido_ia" class="btn btn-outline">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>

            <script>
            function fetchIAMeta() {
                var iaId = document.getElementById('ia_id_input').value.trim();
                if (!iaId) { alert('Ingresá un ID de Archive.org primero.'); return; }
                var btn = document.getElementById('btn-fetch-meta');
                btn.textContent = 'Obteniendo...'; btn.disabled = true;
                fetch('https://archive.org/metadata/' + encodeURIComponent(iaId))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        var md = data.metadata || {};
                        if (md.title) document.getElementById('ia_titulo').value = md.title;
                        var yearInput = document.querySelector('input[name="year"]');
                        if (md.year) yearInput.value = md.year;
                        else if (md.date) yearInput.value = md.date.substring(0, 4);
                        var descEl = document.querySelector('textarea[name="descripcion"]');
                        var desc = md.description || '';
                        if (Array.isArray(desc)) desc = desc.join('\n');
                        descEl.value = desc.replace(/<[^>]*>/g, '');
                        btn.textContent = 'Obtener metadatos'; btn.disabled = false;
                    })
                    .catch(function() {
                        alert('No se pudo obtener metadatos.');
                        btn.textContent = 'Obtener metadatos'; btn.disabled = false;
                    });
            }
            </script>

            <!-- ── Buscar contenido ── -->
            <div class="card">
                <h2>Buscar en <?= $isLibros ? 'Proyecto Gutenberg' : 'Internet Archive' ?></h2>
                <div class="form-row">
                    <div class="form-group">
                        <label>Buscar (vacío = todo)</label>
                        <input type="text" id="ia-search-q" placeholder="ej: drama, Truffaut, terror..." value="">
                    </div>
                    <?php if (!$isLibros): ?>
                    <div class="form-group" style="max-width:200px;">
                        <label>Colección</label>
                        <select id="ia-search-col">
                            <?php if ($isAudio): ?>
                            <option value="" selected>Todas</option>
                            <option value="librivoxaudio">LibriVox (audiolibros)</option>
                            <option value="audio_bookspoetry">Libros y poesía</option>
                            <option value="audio_foreign">Audio no-inglés</option>
                            <option value="opensource_audio">Audio comunidad</option>
                            <?php else: ?>
                            <option value="" selected>Todas</option>
                            <option value="feature_films">Largometrajes</option>
                            <option value="short_films">Cortometrajes</option>
                            <option value="Film_Noir">Film Noir</option>
                            <option value="silent_films">Cine mudo</option>
                            <option value="classic_cartoons">Dibujos animados</option>
                            <option value="anime">Anime</option>
                            <option value="ephemera">Films educativos</option>
                            <option value="opensource_movies">Videos comunidad</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group" style="max-width:160px;">
                        <label>Idioma</label>
                        <select id="ia-search-lang">
                            <option value="Spanish OR español OR spa" selected>Español</option>
                            <option value="">Cualquier idioma</option>
                            <option value="English">Inglés</option>
                            <option value="Portuguese OR portugués">Portugués</option>
                            <option value="French OR français">Francés</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <input type="hidden" id="ia-search-mediatype" value="<?= $isAudio ? 'audio' : 'movies' ?>">
                    <input type="hidden" id="ia-search-seccion" value="<?= $seccionDB ?>">
                    <input type="hidden" id="ia-search-source" value="<?= $isLibros ? 'gutenberg' : 'ia' ?>">
                    <div class="form-group" style="flex:0 0 auto;display:flex;align-items:flex-end;">
                        <button type="button" class="btn btn-primary" id="btn-ia-search" onclick="searchIA(0)">Buscar</button>
                    </div>
                </div>
                <div class="form-row" style="margin-top:0;">
                    <div class="form-group" style="max-width:200px;">
                        <label>Género por defecto</label>
                        <select id="ia-import-genero">
                            <option value="">— Sin género —</option>
                            <option value="Drama">Drama</option>
                            <option value="Comedia">Comedia</option>
                            <option value="Terror">Terror</option>
                            <option value="Ciencia ficción">Ciencia ficción</option>
                            <option value="Aventura">Aventura</option>
                            <option value="Acción">Acción</option>
                            <option value="Suspenso">Suspenso</option>
                            <option value="Film Noir">Film Noir</option>
                            <option value="Animación">Animación</option>
                            <option value="Documental">Documental</option>
                            <option value="Historia">Historia</option>
                            <option value="Sociedad">Sociedad</option>
                            <option value="Musical">Musical</option>
                            <option value="Romance">Romance</option>
                            <option value="Western">Western</option>
                            <option value="Bélico">Bélico</option>
                            <option value="Cine mudo">Cine mudo</option>
                        </select>
                    </div>
                    <div class="form-group" style="display:flex;align-items:flex-end;font-size:0.82rem;color:#888;padding-bottom:0.5rem;">
                        Se usa cuando no se detecta género automáticamente
                    </div>
                </div>
                <div id="ia-search-status" style="font-size:0.85rem;color:#888;margin-bottom:0.5rem;"></div>
                <div id="ia-search-results"></div>
                <div id="ia-import-actions" style="display:none;margin-top:1rem;flex-wrap:wrap;gap:0.5rem;align-items:center;">
                    <button type="button" class="btn btn-primary" onclick="importSelected()">Importar seleccionados</button>
                    <button type="button" class="btn btn-outline" onclick="toggleSelectAll()">Sel./desel. todos</button>
                    <span id="ia-selected-count" style="font-size:0.85rem;color:#888;"></span>
                    <span style="flex:1;"></span>
                    <button type="button" class="btn btn-outline btn-sm" id="ia-prev-btn" onclick="searchIA(iaCurrentPage-1)" disabled>← Anterior</button>
                    <span style="font-size:0.82rem;color:#888;">Pág.</span>
                    <input type="number" id="ia-page-input" min="1" value="1" style="width:50px;padding:2px 4px;font-size:0.82rem;border:1px solid #ddd;border-radius:4px;text-align:center;" onchange="searchIA(parseInt(this.value)-1)">
                    <span id="ia-page-info" style="font-size:0.82rem;color:#888;"></span>
                    <button type="button" class="btn btn-outline btn-sm" id="ia-next-btn" onclick="searchIA(iaCurrentPage+1)">Siguiente →</button>
                </div>
                <div id="ia-import-all-actions" style="display:none;margin-top:0.5rem;padding:0.75rem;background:#f9f9f9;border-radius:8px;border:1px solid #e0e0e0;">
                    <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                        <button type="button" class="btn btn-primary" id="btn-import-all" onclick="importAll()">Importar TODOS los resultados</button>
                        <span id="ia-import-all-info" style="font-size:0.85rem;color:#888;"></span>
                    </div>
                    <div id="ia-import-progress" style="display:none;margin-top:0.5rem;">
                        <div style="background:#e0e0e0;border-radius:4px;height:8px;overflow:hidden;">
                            <div id="ia-progress-bar" style="background:#2e8b47;height:100%;width:0%;transition:width 0.3s;"></div>
                        </div>
                        <div id="ia-progress-text" style="font-size:0.82rem;color:#888;margin-top:0.3rem;"></div>
                    </div>
                </div>
            </div>

            <script>
            var iaSearchResults = [];
            var iaCurrentPage = 0;
            var iaPageSize = 30;
            var iaTotalResults = 0;

            var GENEROS = ['Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo'];
            var GENERO_MAP = {
                'drama':'Drama', 'dramatic':'Drama', 'tragedy':'Drama',
                'comedy':'Comedia', 'comedia':'Comedia', 'comic':'Comedia', 'humor':'Comedia',
                'horror':'Terror', 'terror':'Terror', 'scary':'Terror',
                'sci-fi':'Ciencia ficción', 'science fiction':'Ciencia ficción', 'ciencia ficción':'Ciencia ficción', 'scifi':'Ciencia ficción',
                'adventure':'Aventura', 'aventura':'Aventura',
                'action':'Acción', 'acción':'Acción',
                'thriller':'Suspenso', 'suspense':'Suspenso', 'suspenso':'Suspenso', 'mystery':'Suspenso',
                'noir':'Film Noir', 'film noir':'Film Noir',
                'animation':'Animación', 'animación':'Animación', 'animated':'Animación', 'cartoon':'Animación',
                'documentary':'Documental', 'documental':'Documental',
                'history':'Historia', 'historia':'Historia', 'historical':'Historia',
                'musical':'Musical',
                'romance':'Romance', 'romantic':'Romance', 'love':'Romance',
                'western':'Western',
                'war':'Bélico', 'bélico':'Bélico', 'guerra':'Bélico', 'military':'Bélico',
                'silent':'Cine mudo', 'silent film':'Cine mudo', 'cine mudo':'Cine mudo'
            };

            function detectGenero(subjectStr) {
                if (!subjectStr) return '';
                var lower = subjectStr.toLowerCase();
                // Try longest matches first (e.g. "science fiction" before "fiction")
                var keys = Object.keys(GENERO_MAP).sort(function(a,b) { return b.length - a.length; });
                for (var i = 0; i < keys.length; i++) {
                    if (lower.indexOf(keys[i]) > -1) return GENERO_MAP[keys[i]];
                }
                return '';
            }

            function generoSelectHTML(idx, detected) {
                var html = '<select class="ia-genero-sel" data-idx="' + idx + '" style="font-size:0.78rem;padding:2px 4px;border-radius:4px;border:1px solid #ddd;">';
                html += '<option value="">—</option>';
                GENEROS.forEach(function(g) {
                    html += '<option value="' + g + '"' + (g === detected ? ' selected' : '') + '>' + g + '</option>';
                });
                html += '</select>';
                return html;
            }

            function searchIA(page) {
                var q = document.getElementById('ia-search-q').value.trim();
                var langEl = document.getElementById('ia-search-lang');
                var colEl = document.getElementById('ia-search-col');
                var lang = langEl ? langEl.value : '';
                var col = colEl ? colEl.value : '';
                var source = document.getElementById('ia-search-source').value;
                if (source !== 'gutenberg' && !q && !col && !lang) { alert('Elegí al menos una colección o idioma.'); return; }
                iaCurrentPage = (page !== undefined && page !== null) ? page : 0;
                var btn = document.getElementById('btn-ia-search');
                var status = document.getElementById('ia-search-status');
                btn.textContent = 'Buscando...'; btn.disabled = true;
                status.textContent = 'Consultando Archive.org...';
                document.getElementById('ia-search-results').innerHTML = '';
                document.getElementById('ia-import-actions').style.display = 'none';

                var source = document.getElementById('ia-search-source').value;
                var url;
                if (source === 'gutenberg') {
                    url = 'api.php?action=search_gutenberg&page=' + (iaCurrentPage + 1);
                    if (q) url += '&q=' + encodeURIComponent(q);
                } else {
                    var mediatype = document.getElementById('ia-search-mediatype').value;
                    url = 'api.php?action=search_ia&q=' + encodeURIComponent(q || '*') + '&mediatype=' + mediatype;
                    if (lang) url += '&lang=' + encodeURIComponent(lang);
                    if (col) url += '&collection=' + encodeURIComponent(col);
                    url += '&rows=' + iaPageSize + '&page=' + iaCurrentPage;
                }

                fetch(url)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        btn.textContent = 'Buscar'; btn.disabled = false;
                        if (data.error) { status.textContent = data.error; return; }
                        iaSearchResults = data.results || [];
                        iaTotalResults = data.total || 0;
                        var from = iaCurrentPage * iaPageSize + 1;
                        var to = Math.min(from + iaSearchResults.length - 1, iaTotalResults);
                        status.textContent = iaTotalResults.toLocaleString() + ' resultados — mostrando ' + from + '-' + to;
                        renderIAResults();
                    })
                    .catch(function() {
                        btn.textContent = 'Buscar'; btn.disabled = false;
                        status.textContent = 'Error al buscar.';
                    });
            }

            function renderIAResults() {
                var container = document.getElementById('ia-search-results');
                if (!iaSearchResults.length) { container.innerHTML = '<p style="color:#888;">Sin resultados.</p>'; return; }
                var html = '<table><tr><th style="width:30px;"></th><th></th><th>Título</th><th>Director</th><th>Año</th><th>Colección</th><th>Género</th><th title="Marcar = importar inactivo (requiere revisión)">Revisar</th><th></th></tr>';
                iaSearchResults.forEach(function(r, i) {
                    var disabled = r.ya_existe ? ' disabled' : '';
                    var badge = r.ya_existe ? ' <span class="badge badge-active">Ya agregado</span>' : '';
                    var detected = detectGenero(r.genero) || detectGenero(r.titulo);
                    iaSearchResults[i]._genero = detected;
                    var needsReview = !r.curada;
                    iaSearchResults[i]._inactive = needsReview;
                    var colStyle = r.curada ? 'color:#2e8b47;' : 'color:#e63946;';
                    html += '<tr style="' + (r.ya_existe ? 'opacity:0.5;' : '') + '">' +
                        '<td><input type="checkbox" class="ia-check" data-idx="' + i + '"' + disabled + (r.ya_existe ? '' : ' checked') + '></td>' +
                        '<td><img src="' + (r.url_portada || ('https://archive.org/download/' + r.ia_id + '/__ia_thumb.jpg')) + '" style="width:60px;height:40px;object-fit:cover;border-radius:4px;" onerror="this.style.display=\'none\'"></td>' +
                        '<td>' + r.titulo + badge + '</td>' +
                        '<td>' + (r.director || '—') + '</td>' +
                        '<td>' + (r.year || '—') + '</td>' +
                        '<td style="font-size:0.78rem;' + colStyle + '">' + (r.coleccion || '—') + '</td>' +
                        '<td>' + generoSelectHTML(i, detected) + '</td>' +
                        '<td style="text-align:center;"><input type="checkbox" class="ia-review" data-idx="' + i + '"' + (needsReview ? ' checked' : '') + (r.ya_existe ? ' disabled' : '') + '></td>' +
                        '<td><a href="https://archive.org/details/' + r.ia_id + '" target="_blank" class="btn btn-sm btn-outline">Ver en IA</a></td>' +
                    '</tr>';
                });
                html += '</table>';
                container.innerHTML = html;
                document.getElementById('ia-import-actions').style.display = 'flex';
                updateSelectedCount();
                document.querySelectorAll('.ia-check').forEach(function(cb) {
                    cb.addEventListener('change', updateSelectedCount);
                });
                // Update pagination
                var totalPages = Math.ceil(iaTotalResults / iaPageSize);
                document.getElementById('ia-prev-btn').disabled = iaCurrentPage <= 0;
                document.getElementById('ia-next-btn').disabled = iaCurrentPage >= totalPages - 1;
                document.getElementById('ia-page-input').value = iaCurrentPage + 1;
                document.getElementById('ia-page-input').max = totalPages;
                document.getElementById('ia-page-info').textContent = 'de ' + totalPages;
                // Show import-all if there are many results
                if (iaTotalResults > iaPageSize) {
                    document.getElementById('ia-import-all-actions').style.display = '';
                    document.getElementById('ia-import-all-info').textContent = iaTotalResults.toLocaleString() + ' títulos en total — importa todos de una vez';
                } else {
                    document.getElementById('ia-import-all-actions').style.display = 'none';
                }
            }

            function updateSelectedCount() {
                var checked = document.querySelectorAll('.ia-check:checked:not(:disabled)').length;
                document.getElementById('ia-selected-count').textContent = checked + ' seleccionados';
            }

            function toggleSelectAll() {
                var cbs = document.querySelectorAll('.ia-check:not(:disabled)');
                var allChecked = true;
                cbs.forEach(function(cb) { if (!cb.checked) allChecked = false; });
                cbs.forEach(function(cb) { cb.checked = !allChecked; });
                updateSelectedCount();
            }

            function importSelected() {
                var generoGlobal = document.getElementById('ia-import-genero').value;
                var itemsActive = [];
                var itemsInactive = [];
                document.querySelectorAll('.ia-check:checked:not(:disabled)').forEach(function(cb) {
                    var idx = parseInt(cb.getAttribute('data-idx'));
                    var r = iaSearchResults[idx];
                    var sel = document.querySelector('.ia-genero-sel[data-idx="' + idx + '"]');
                    var genero = (sel && sel.value) ? sel.value : generoGlobal;
                    var reviewCb = document.querySelector('.ia-review[data-idx="' + idx + '"]');
                    var needsReview = reviewCb && reviewCb.checked;
                    var item = {
                        ia_id: r.ia_id, titulo: r.titulo, director: r.director || '',
                        year: r.year || '', duracion: r.duracion || '',
                        subject: r.genero || '', descripcion: r.descripcion || '',
                        url_portada: r.url_portada || '', url_contenido: r.url_contenido || '',
                        fuente: document.getElementById('ia-search-source').value === 'gutenberg' ? 'gutenberg' : 'archive.org'
                    };
                    if (needsReview) itemsInactive.push(item); else itemsActive.push(item);
                });
                var total = itemsActive.length + itemsInactive.length;
                if (!total) { alert('Seleccioná al menos un item.'); return; }
                var desc = total + ' título' + (total > 1 ? 's' : '');
                if (itemsInactive.length) desc += ' (' + itemsInactive.length + ' para revisión)';
                if (!confirm('¿Importar ' + desc + '?')) return;

                var promises = [];
                if (itemsActive.length) {
                    promises.push(fetch('api.php?action=import_ia_batch', {
                        method: 'POST', headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({items: itemsActive, genero: generoGlobal, activo: true})
                    }).then(function(r) { return r.json(); }));
                }
                if (itemsInactive.length) {
                    promises.push(fetch('api.php?action=import_ia_batch', {
                        method: 'POST', headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({items: itemsInactive, genero: generoGlobal, activo: false})
                    }).then(function(r) { return r.json(); }));
                }
                Promise.all(promises).then(function(results) {
                    var imp = 0, skip = 0;
                    results.forEach(function(r) { imp += r.imported || 0; skip += r.skipped || 0; });
                    alert('Importados: ' + imp + (skip ? ', ya existían: ' + skip : ''));
                    window.location.reload();
                });
            }

            // ── Import ALL results ──
            function importAll() {
                var genero = document.getElementById('ia-import-genero').value;
                if (!confirm('¿Importar los ' + iaTotalResults.toLocaleString() + ' resultados?\nGénero: ' + (genero || 'Sin género') + '\nEsto puede tardar unos minutos.')) return;

                var btn = document.getElementById('btn-import-all');
                btn.disabled = true; btn.textContent = 'Importando...';
                var progressDiv = document.getElementById('ia-import-progress');
                var progressBar = document.getElementById('ia-progress-bar');
                var progressText = document.getElementById('ia-progress-text');
                progressDiv.style.display = '';

                // Build the same search URL but with bigger batches
                var q = document.getElementById('ia-search-q').value.trim() || '*';
                var langEl = document.getElementById('ia-search-lang');
                var colEl = document.getElementById('ia-search-col');
                var lang = langEl ? langEl.value : '';
                var col = colEl ? colEl.value : '';
                var batchSize = 100;
                var totalPages = Math.ceil(iaTotalResults / batchSize);
                var totalImported = 0;
                var totalSkipped = 0;
                var currentPage = 0;

                function processBatch() {
                    if (currentPage >= totalPages) {
                        // Done
                        progressBar.style.width = '100%';
                        progressText.textContent = 'Completado: ' + totalImported + ' nuevos importados, ' + totalSkipped + ' ya existían. Recargando...';
                        btn.textContent = 'Importar TODOS los resultados'; btn.disabled = false;
                        // Reload page to refresh list
                        var sec = document.getElementById('ia-search-seccion').value;
                        var sMap = {audiolibros:'audiolibros',libros:'libros'};
                        setTimeout(function() { window.location.href = '?s=' + (sMap[sec] || 'contenido_ia'); }, 2000);
                        return;
                    }

                    var pct = Math.round((currentPage / totalPages) * 100);
                    progressBar.style.width = pct + '%';
                    progressText.textContent = 'Página ' + (currentPage + 1) + ' de ' + totalPages + ' — importados: ' + totalImported + ', ya existían: ' + totalSkipped;

                    // Fetch results
                    var importSource = document.getElementById('ia-search-source').value;
                    var url;
                    if (importSource === 'gutenberg') {
                        url = 'api.php?action=search_gutenberg&page=' + (currentPage + 1);
                        if (q && q !== '*') url += '&q=' + encodeURIComponent(q);
                    } else {
                        url = 'api.php?action=search_ia&q=' + encodeURIComponent(q) + '&mediatype=' + document.getElementById('ia-search-mediatype').value + '&rows=' + batchSize + '&page=' + currentPage;
                        if (lang) url += '&lang=' + encodeURIComponent(lang);
                        if (col) url += '&collection=' + encodeURIComponent(col);
                    }

                    fetch(url)
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (data.error || !data.results || !data.results.length) {
                                currentPage = totalPages; // stop
                                processBatch();
                                return;
                            }
                            // Split into curated (active) and community (inactive)
                            var newResults = data.results.filter(function(r) { return !r.ya_existe; });
                            totalSkipped += data.results.length - newResults.length;

                            if (!newResults.length) {
                                currentPage++;
                                processBatch();
                                return;
                            }

                            var activeItems = [], inactiveItems = [];
                            newResults.forEach(function(r) {
                                var item = {
                                    ia_id: r.ia_id, titulo: r.titulo, director: r.director || '',
                                    year: r.year || '', duracion: r.duracion || '',
                                    descripcion: r.descripcion || '', subject: r.genero || '',
                                    url_portada: r.url_portada || '', url_contenido: r.url_contenido || '',
                                    fuente: document.getElementById('ia-search-source').value === 'gutenberg' ? 'gutenberg' : 'archive.org'
                                };
                                if (r.curada) activeItems.push(item); else inactiveItems.push(item);
                            });

                            // Import both batches
                            var batchPromises = [];
                            if (activeItems.length) {
                                batchPromises.push(fetch('api.php?action=import_ia_batch', {
                                    method: 'POST', headers: {'Content-Type': 'application/json'},
                                    body: JSON.stringify({items: activeItems, genero: genero, activo: true, seccion: document.getElementById('ia-search-seccion').value})
                                }).then(function(r) { return r.json(); }));
                            }
                            if (inactiveItems.length) {
                                batchPromises.push(fetch('api.php?action=import_ia_batch', {
                                    method: 'POST', headers: {'Content-Type': 'application/json'},
                                    body: JSON.stringify({items: inactiveItems, genero: genero, activo: false, seccion: document.getElementById('ia-search-seccion').value})
                                }).then(function(r) { return r.json(); }));
                            }
                            Promise.all(batchPromises)
                            .then(function(r) { return r.json(); })
                            .then(function(results) {
                                results.forEach(function(result) {
                                    totalImported += result.imported || 0;
                                    totalSkipped += result.skipped || 0;
                                    if (result.errors) {
                                        progressText.textContent += ' ⚠ ' + result.errors + ' errores';
                                    }
                                });
                                currentPage++;
                                processBatch();
                            })
                            .catch(function() {
                                progressText.textContent = 'Error en lote ' + (currentPage + 1) + '. Reintentando...';
                                setTimeout(processBatch, 2000);
                            });
                        })
                        .catch(function() {
                            progressText.textContent = 'Error de conexión. Reintentando...';
                            setTimeout(processBatch, 2000);
                        });
                }

                processBatch();
            }

            // When global genre changes, fill empty per-row selects
            document.getElementById('ia-import-genero').addEventListener('change', function() {
                var val = this.value;
                document.querySelectorAll('.ia-genero-sel').forEach(function(sel) {
                    if (!sel.value && val) sel.value = val;
                });
            });

            // Search on Enter
            document.getElementById('ia-search-q').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') { e.preventDefault(); searchIA(0); }
            });
            </script>

            <?php
            $iaEstado = $_GET['estado'] ?? '';
            $iaBuscar = trim($_GET['buscar'] ?? '');
            $iaPage = max(intval($_GET['p'] ?? 1), 1);
            $iaPerPage = 50;
            $iaOffset = ($iaPage - 1) * $iaPerPage;
            $iaWhere = "seccion = '$seccionDB'";
            if ($iaEstado === 'activo') $iaWhere .= ' AND activo = 1 AND bloqueado = 0';
            elseif ($iaEstado === 'inactivo') $iaWhere .= ' AND activo = 0 AND bloqueado = 0';
            elseif ($iaEstado === 'bloqueado') $iaWhere .= ' AND bloqueado = 1';
            $iaParams = [];
            if ($iaBuscar) {
                $iaWhere .= ' AND (titulo LIKE ? OR director LIKE ? OR genero LIKE ?)';
                $iaParams = ["%$iaBuscar%", "%$iaBuscar%", "%$iaBuscar%"];
            }
            $countStmt = $db->prepare("SELECT COUNT(*) FROM contenido_ia WHERE $iaWhere");
            $countStmt->execute($iaParams);
            $iaTotalItems = $countStmt->fetchColumn();
            $iaTotalActivos = $db->query("SELECT COUNT(*) FROM contenido_ia WHERE seccion = '$seccionDB' AND activo = 1 AND bloqueado = 0")->fetchColumn();
            $iaTotalInactivos = $db->query("SELECT COUNT(*) FROM contenido_ia WHERE seccion = '$seccionDB' AND activo = 0 AND bloqueado = 0")->fetchColumn();
            $iaTotalBloqueados = $db->query("SELECT COUNT(*) FROM contenido_ia WHERE seccion = '$seccionDB' AND bloqueado = 1")->fetchColumn();
            $iaTotalPages = max(ceil($iaTotalItems / $iaPerPage), 1);
            $listStmt = $db->prepare("SELECT * FROM contenido_ia WHERE $iaWhere ORDER BY activo DESC, orden, titulo LIMIT $iaPerPage OFFSET $iaOffset");
            $listStmt->execute($iaParams);
            $iaItems = $listStmt->fetchAll();
            $iaQS = 's=contenido_ia' . ($iaEstado ? '&estado='.$iaEstado : '') . ($iaBuscar ? '&buscar='.urlencode($iaBuscar) : '');
            ?>

            <div class="card">
                <h2>Catálogo</h2>
                <div style="margin-bottom:0.75rem;display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
                    <?php $sParam = $isAudio ? 'audiolibros' : 'contenido_ia'; ?>
                    <a href="?s=<?= $sParam ?>" class="btn btn-sm <?= !$iaEstado ? 'btn-primary' : 'btn-outline' ?>">Todos (<?= $iaTotalActivos + $iaTotalInactivos + $iaTotalBloqueados ?>)</a>
                    <a href="?s=<?= $sParam ?>&estado=activo" class="btn btn-sm <?= $iaEstado==='activo' ? 'btn-primary' : 'btn-outline' ?>">Activos (<?= $iaTotalActivos ?>)</a>
                    <a href="?s=<?= $sParam ?>&estado=inactivo" class="btn btn-sm <?= $iaEstado==='inactivo' ? 'btn-primary' : 'btn-outline' ?>">Pendientes (<?= $iaTotalInactivos ?>)</a>
                    <a href="?s=<?= $sParam ?>&estado=bloqueado" class="btn btn-sm <?= $iaEstado==='bloqueado' ? 'btn-primary' : 'btn-outline' ?>" style="<?= $iaTotalBloqueados ? '' : 'opacity:0.5;' ?>">Bloqueados (<?= $iaTotalBloqueados ?>)</a>
                    <form style="margin-left:auto;display:flex;gap:0.3rem;" method="GET">
                        <input type="hidden" name="s" value="contenido_ia">
                        <?php if ($iaEstado): ?><input type="hidden" name="estado" value="<?= $iaEstado ?>"><?php endif; ?>
                        <input type="text" name="buscar" value="<?= e($iaBuscar) ?>" placeholder="Buscar título, director, género..." style="padding:0.3rem 0.6rem;border:1px solid #ddd;border-radius:6px;font-size:0.82rem;width:220px;">
                        <button class="btn btn-sm btn-outline">Buscar</button>
                        <?php if ($iaBuscar): ?><a href="?s=contenido_ia<?= $iaEstado ? '&estado='.$iaEstado : '' ?>" class="btn btn-sm btn-outline">✕</a><?php endif; ?>
                    </form>
                </div>
                <!-- Bulk actions -->
                <div id="ia-bulk-bar" style="display:none;margin-bottom:0.75rem;padding:0.5rem 0.75rem;background:#eef7f0;border-radius:8px;display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
                    <span id="ia-bulk-count" style="font-size:0.85rem;font-weight:500;"></span>
                    <button type="button" class="btn btn-sm btn-primary" onclick="bulkAction('activar')">Activar</button>
                    <button type="button" class="btn btn-sm btn-outline" onclick="bulkAction('desactivar')">Desactivar</button>
                    <button type="button" class="btn btn-sm btn-outline" onclick="bulkAction('bloquear')" style="color:#e67e22;border-color:#e67e22;">Bloquear</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('eliminar')">Eliminar</button>
                </div>
                <table>
                    <tr>
                        <th style="width:30px;"><input type="checkbox" id="ia-cat-select-all"></th>
                        <th></th><th>Título</th><th>Director</th><th>Género</th><th>Estado</th><th>Acciones</th>
                    </tr>
                    <?php foreach ($iaItems as $ia):
                        $estadoLabel = 'Inactivo'; $estadoClass = 'badge-inactive';
                        if ($ia['bloqueado']) { $estadoLabel = 'Bloqueado'; $estadoClass = ''; }
                        elseif ($ia['activo']) { $estadoLabel = 'Activo'; $estadoClass = 'badge-active'; }
                    ?>
                    <tr>
                        <td><input type="checkbox" class="ia-cat-check" value="<?= $ia['id'] ?>"></td>
                        <td><img src="https://archive.org/download/<?= e($ia['ia_id']) ?>/__ia_thumb.jpg" class="thumb-sm" style="width:60px;height:40px;object-fit:cover;"></td>
                        <td><a href="watch?v=ia:<?= e($ia['slug']) ?>" target="_blank"><?= e(mb_substr($ia['titulo'], 0, 50)) ?><?= mb_strlen($ia['titulo'])>50?'...':'' ?></a></td>
                        <td><?= e(mb_substr($ia['director'], 0, 25)) ?></td>
                        <td><?= e($ia['genero']) ?></td>
                        <td><span class="badge <?= $estadoClass ?>" <?= $ia['bloqueado'] ? 'style="background:#fff3e0;color:#e67e22;"' : '' ?>><?= $estadoLabel ?></span></td>
                        <td>
                            <a href="?s=contenido_ia&edit=<?= $ia['id'] ?>" class="btn btn-sm btn-outline">Editar</a>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="toggle_ia">
                                <input type="hidden" name="ia_content_id" value="<?= $ia['id'] ?>">
                                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                <button class="btn btn-sm btn-outline"><?= $ia['activo']?'Desactivar':'Activar' ?></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php if ($iaTotalPages > 1): ?>
                <div style="margin-top:1rem;display:flex;gap:0.5rem;align-items:center;justify-content:center;">
                    <?php if ($iaPage > 1): ?>
                        <a href="?<?= $iaQS ?>&p=<?= $iaPage-1 ?>" class="btn btn-sm btn-outline">← Anterior</a>
                    <?php endif; ?>
                    <span style="font-size:0.85rem;color:#888;">Pág. <?= $iaPage ?> de <?= $iaTotalPages ?> (<?= $iaTotalItems ?> items)</span>
                    <?php if ($iaPage < $iaTotalPages): ?>
                        <a href="?<?= $iaQS ?>&p=<?= $iaPage+1 ?>" class="btn btn-sm btn-outline">Siguiente →</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Bulk action form (hidden) -->
                <form method="POST" id="ia-bulk-form" style="display:none;">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <input type="hidden" name="action" id="ia-bulk-action" value="">
                    <input type="hidden" name="ids" id="ia-bulk-ids" value="">
                </form>

                <script>
                // Select all checkbox
                document.getElementById('ia-cat-select-all').addEventListener('change', function() {
                    var checked = this.checked;
                    document.querySelectorAll('.ia-cat-check').forEach(function(cb) { cb.checked = checked; });
                    updateBulkBar();
                });
                document.querySelectorAll('.ia-cat-check').forEach(function(cb) {
                    cb.addEventListener('change', updateBulkBar);
                });
                function updateBulkBar() {
                    var checked = document.querySelectorAll('.ia-cat-check:checked');
                    var bar = document.getElementById('ia-bulk-bar');
                    if (checked.length > 0) {
                        bar.style.display = 'flex';
                        document.getElementById('ia-bulk-count').textContent = checked.length + ' seleccionados';
                    } else {
                        bar.style.display = 'none';
                    }
                }
                function bulkAction(action) {
                    var ids = [];
                    document.querySelectorAll('.ia-cat-check:checked').forEach(function(cb) { ids.push(cb.value); });
                    if (!ids.length) return;
                    var labels = {activar:'activar',desactivar:'desactivar',bloquear:'bloquear (no se re-importa)',eliminar:'ELIMINAR permanentemente'};
                    if (!confirm('¿' + labels[action].charAt(0).toUpperCase() + labels[action].slice(1) + ' ' + ids.length + ' items?')) return;
                    document.getElementById('ia-bulk-action').value = 'bulk_' + action + '_ia';
                    document.getElementById('ia-bulk-ids').value = JSON.stringify(ids);
                    document.getElementById('ia-bulk-form').submit();
                }
                updateBulkBar();
                </script>
            </div>

        <?php elseif ($section === 'password'): ?>
            <h1>Cambiar contraseña</h1>
            <div class="card">
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <div class="form-group" style="max-width:400px;">
                        <label>Nueva contraseña (mínimo 8 caracteres)</label>
                        <input type="password" name="new_password" required minlength="8">
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
                </form>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
