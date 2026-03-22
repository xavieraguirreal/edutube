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

                        $stmt = $db->prepare("INSERT INTO canales (nombre, youtube_channel_id, codigo, color) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$nombre, $channelId, $autoCodigo, $colores[array_rand($colores)]]);
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
            $stmt = $db->prepare("INSERT INTO canales (nombre, youtube_channel_id, codigo, color, descripcion, auto_sync, default_categoria_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nombre'], $_POST['youtube_channel_id'] ?? '',
                $_POST['codigo'], $_POST['color'] ?? '#2e8b47',
                $_POST['descripcion'] ?? '',
                isset($_POST['auto_sync']) ? 1 : 0,
                $_POST['default_categoria_id'] ?: null
            ]);
            $msg = 'Canal creado correctamente.'; $msgType = 'success';
        }

        // ── Edit channel ──
        if ($action === 'edit_channel') {
            $stmt = $db->prepare("UPDATE canales SET nombre=?, youtube_channel_id=?, codigo=?, color=?, descripcion=?, auto_sync=?, default_categoria_id=? WHERE id=?");
            $stmt->execute([
                $_POST['nombre'], $_POST['youtube_channel_id'] ?? '',
                $_POST['codigo'], $_POST['color'] ?? '#2e8b47',
                $_POST['descripcion'] ?? '',
                isset($_POST['auto_sync']) ? 1 : 0,
                $_POST['default_categoria_id'] ?: null,
                $_POST['canal_id']
            ]);
            $msg = 'Canal actualizado.'; $msgType = 'success';
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
                        $stmtChDb = $db->prepare("SELECT id FROM canales WHERE youtube_channel_id = ?");
                        $stmtChDb->execute([$previewChId]);
                        $chDbRow = $stmtChDb->fetch();
                        $canalDbIdPreview = $chDbRow ? intval($chDbRow['id']) : 0;

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
                                    <option value="<?= $c['id'] ?>"><?= e($c['nombre']) ?></option>
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
                    <button type="submit" class="btn btn-primary"><?= $editCanal ? 'Guardar cambios' : 'Crear canal' ?></button>
                    <?php if ($editCanal): ?>
                        <a href="?s=canales" class="btn btn-outline" style="margin-left:0.5rem;">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>
            <table>
                <tr><th>Nombre</th><th>Código</th><th>Channel ID</th><th>Categoría</th><th>Auto-sync</th><th>Color</th><th>Acciones</th></tr>
                <?php foreach ($canales as $c):
                    $catNombre = '—';
                    if (!empty($c['default_categoria_id'])) {
                        foreach ($categorias as $cat) {
                            if ($cat['id'] == $c['default_categoria_id']) { $catNombre = $cat['nombre']; break; }
                        }
                    }
                ?>
                <tr>
                    <td><?= e($c['nombre']) ?></td>
                    <td><?= e($c['codigo']) ?></td>
                    <td style="font-size:0.78rem;"><?= e($c['youtube_channel_id']) ?></td>
                    <td><?= e($catNombre) ?></td>
                    <td><span class="badge <?= !empty($c['auto_sync']) ? 'badge-active' : 'badge-inactive' ?>"><?= !empty($c['auto_sync']) ? 'Sí' : 'No' ?></span></td>
                    <td><span style="display:inline-block;width:20px;height:20px;border-radius:4px;background:<?= e($c['color']) ?>"></span></td>
                    <td><a href="?s=canales&edit=<?= $c['id'] ?>" class="btn btn-sm btn-outline">Editar</a></td>
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
                    <td><span class="badge <?= !empty($c['mostrar_en_portada']) ? 'badge-active' : 'badge-inactive' ?>"><?= !empty($c['mostrar_en_portada']) ? 'Sí' : 'No' ?></span></td>
                    <td><a href="?s=categorias&edit_cat=<?= $c['id'] ?>" class="btn btn-sm btn-outline">Editar</a></td>
                </tr>
                <?php endforeach; ?>
            </table>

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
