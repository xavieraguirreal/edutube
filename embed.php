<?php
// ═══════════════════════════════════════════
// EduTube — /embed?v=VIDEO_ID
// Reproductor limpio (sin topbar/sidebar/relacionados) para incrustar en <iframe>
// desde sitios de la Cooperativa / Universidad Liberté.
// ═══════════════════════════════════════════

// ── Dominios que pueden incrustar este reproductor ──
$frameAncestors =
    "frame-ancestors 'self' "
    . "https://cooperativaliberte.coop https://*.cooperativaliberte.coop "
    . "https://universidadliberte.org https://*.universidadliberte.org "
    . "https://tallersolidarioliberte.com.ar https://*.tallersolidarioliberte.com.ar";

// CSP por header: frame-ancestors (quién puede embebernos) + recursos del reproductor.
// NO enviamos X-Frame-Options (rompería el embebido en navegadores que lo priorizan).
header("Content-Security-Policy: "
    . "default-src 'self'; "
    . "frame-src https://www.youtube-nocookie.com; "
    . "img-src 'self' https://i.ytimg.com https://img.youtube.com; "
    . "style-src 'self' 'unsafe-inline'; "
    . "script-src 'self' 'unsafe-inline' https://www.youtube.com; "
    . "connect-src 'self'; "
    . $frameAncestors . ";");

// ── Leer y sanitizar el ID ──
$videoId = isset($_GET['v']) ? $_GET['v'] : (isset($_GET['id']) ? $_GET['id'] : '');
$videoId = preg_replace('/[^a-zA-Z0-9_-]/', '', $videoId);

// ── Validar contra la BD ──
$titulo = '';
$ok = false;
if ($videoId !== '') {
    try {
        require_once __DIR__ . '/config.php';
        $db = getDB();
        $stmt = $db->prepare("SELECT titulo FROM videos WHERE youtube_id = ? AND activo = 1");
        $stmt->execute([$videoId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $titulo = $row['titulo'];
            $ok = true;
        }
    } catch (Exception $e) {
        // Silencioso: no exponer detalles
    }
}
?>
<!DOCTYPE html>
<html lang="es" class="page-player">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSP del documento: el reproductor de YouTube y la IFrame API -->
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        frame-src https://www.youtube-nocookie.com;
        img-src 'self' https://i.ytimg.com https://img.youtube.com;
        style-src 'self' 'unsafe-inline';
        script-src 'self' 'unsafe-inline' https://www.youtube.com;
        connect-src 'self';
    ">
    <title><?php echo $ok ? htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . ' — EduTube' : 'EduTube'; ?></title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Full-bleed: el iframe padre controla tamaño/aspect-ratio */
        html, body { margin: 0; height: 100%; background: #000; overflow: hidden; }
        .embed-stage { position: fixed; inset: 0; background: #000; }
        .embed-stage .player-wrapper { width: 100%; height: 100%; }
        .embed-stage .player-container { width: 100%; height: 100%; }
        .embed-stage .player-container iframe { width: 100%; height: 100%; border: 0; }
        /* Controles propios anclados abajo */
        .embed-stage .custom-controls { position: absolute; left: 0; right: 0; bottom: 0; z-index: 5; }
        /* Badge EduTube */
        .embed-badge {
            position: absolute; top: 10px; right: 12px; z-index: 10;
            display: flex; align-items: center; gap: 6px;
            padding: 5px 9px; border-radius: 8px;
            background: rgba(0,0,0,0.55); backdrop-filter: blur(4px);
            color: #fff; font: 600 12px/1 system-ui, sans-serif;
            text-decoration: none; opacity: 0.85; transition: opacity .15s;
        }
        .embed-badge:hover { opacity: 1; }
        .embed-badge img { width: 18px; height: 18px; display: block; }
        /* Mensaje de error sobrio */
        .embed-msg {
            position: fixed; inset: 0; display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 10px;
            color: #bbb; font: 400 15px/1.4 system-ui, sans-serif; text-align: center; padding: 1rem;
        }
        .embed-msg img { width: 40px; height: 40px; opacity: .7; }
    </style>
</head>
<body>

<?php if (!$ok): ?>

    <div class="embed-msg">
        <img src="loguito-edutube.png" alt="">
        <div>Video no disponible</div>
    </div>

<?php else: ?>

    <div class="embed-stage" id="stage">
        <a class="embed-badge" href="https://edutube.universidadliberte.org/watch?v=<?php echo rawurlencode($videoId); ?>" target="_blank" rel="noopener">
            <img src="loguito-edutube.png" alt="EduTube">
            <span>EduTube</span>
        </a>
        <div class="player-wrapper">
            <div class="player-container" id="player-container">
                <iframe id="yt-player"
                    sandbox="allow-scripts allow-same-origin allow-presentation"
                    allow="autoplay; encrypted-media; fullscreen"
                    tabindex="-1"
                    title="<?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>"></iframe>
                <div class="yt-shield-top"></div>
                <div class="yt-shield-bottom"></div>
            </div>
            <div class="custom-controls">
                <button id="btn-play" class="ctrl-btn" title="Reproducir"><svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></button>
                <div class="ctrl-vol-group">
                    <button id="btn-mute" class="ctrl-btn" title="Silenciar">
                        <svg id="icon-vol" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
                        <svg id="icon-muted" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" style="display:none"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
                    </button>
                    <input type="range" id="volume-slider" class="ctrl-volume" min="0" max="100" value="100">
                </div>
                <span id="time-current" class="ctrl-time">0:00</span>
                <span class="ctrl-time-separator">/</span>
                <span id="time-total" class="ctrl-time">0:00</span>
                <div class="ctrl-progress-wrap">
                    <input type="range" id="progress-bar" class="ctrl-progress" min="0" max="1000" value="0">
                    <div class="ctrl-progress-fill" id="progress-fill"></div>
                </div>
                <button id="btn-fullscreen" class="ctrl-btn" title="Pantalla completa"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg></button>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var videoId = <?php echo json_encode($videoId); ?>;
        var origin = window.location.protocol + '//' + window.location.host;
        var embedSrc = 'https://www.youtube-nocookie.com/embed/' + videoId +
            '?rel=0&modestbranding=1&iv_load_policy=3&controls=0&fs=0&disablekb=0&playsinline=1&enablejsapi=1&origin=' + encodeURIComponent(origin);
        document.getElementById('yt-player').src = embedSrc;

        function fmt(s) {
            var h = Math.floor(s/3600), m = Math.floor((s%3600)/60), sec = Math.floor(s%60);
            if (h > 0) return h + ':' + String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
            return m + ':' + String(sec).padStart(2,'0');
        }

        // ── YouTube IFrame API ──
        var tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(tag);

        var player, progressInterval, isMuted = false;

        window.onYouTubeIframeAPIReady = function() {
            player = new YT.Player('yt-player', { events: {
                onReady: function() { updateTotal(); setTimeout(updateTotal, 2000); },
                onStateChange: onState,
                onError: function(e) {
                    var container = document.getElementById('player-container');
                    var msgs = { 2: 'ID de video inválido', 5: 'Error de reproducción', 100: 'Video no encontrado', 101: 'Video no disponible', 150: 'Video no disponible' };
                    var msg = msgs[e.data] || 'Error al reproducir el video';
                    var overlay = document.createElement('div');
                    overlay.className = 'yt-error-overlay';
                    overlay.innerHTML = '<svg viewBox="0 0 24 24" width="48" height="48" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg><p>' + msg + '</p>';
                    container.appendChild(overlay);
                }
            }});
        };

        // Click en container → play/pause (el iframe tiene pointer-events:none vía CSS)
        document.getElementById('player-container').addEventListener('click', function() {
            if (!player || !player.getPlayerState) return;
            if (player.getPlayerState() === YT.PlayerState.PLAYING) player.pauseVideo();
            else player.playVideo();
        });

        // Evitar que Tab entre al iframe
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab' && document.activeElement && document.activeElement.tagName === 'IFRAME') {
                e.preventDefault();
                document.getElementById('btn-play').focus();
            }
        });

        function onState(e) {
            var btn = document.getElementById('btn-play');
            var play = '<svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
            var pause = '<svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
            if (e.data === YT.PlayerState.PLAYING) { btn.innerHTML = pause; startProgress(); }
            else { btn.innerHTML = play; if (e.data !== YT.PlayerState.BUFFERING) stopProgress(); }
        }

        function startProgress() { stopProgress(); progressInterval = setInterval(updateProgress, 250); }
        function stopProgress() { if (progressInterval) clearInterval(progressInterval); }

        function updateProgress() {
            if (!player || !player.getDuration) return;
            var d = player.getDuration(), c = player.getCurrentTime();
            if (d > 0) {
                var pct = (c/d)*1000;
                document.getElementById('progress-bar').value = pct;
                document.getElementById('progress-fill').style.width = (pct/10) + '%';
                document.getElementById('time-current').textContent = fmt(c);
            }
        }

        function updateTotal() {
            if (!player || !player.getDuration) return;
            var d = player.getDuration();
            if (d > 0) document.getElementById('time-total').textContent = fmt(d);
        }

        document.getElementById('btn-play').addEventListener('click', function() {
            if (!player) return;
            if (player.getPlayerState() === YT.PlayerState.PLAYING) player.pauseVideo(); else player.playVideo();
        });

        document.getElementById('btn-mute').addEventListener('click', function() {
            if (!player) return;
            isMuted = !isMuted;
            if (isMuted) player.mute(); else player.unMute();
            document.getElementById('icon-vol').style.display = isMuted ? 'none' : '';
            document.getElementById('icon-muted').style.display = isMuted ? '' : 'none';
            document.getElementById('volume-slider').value = isMuted ? 0 : player.getVolume();
        });

        document.getElementById('volume-slider').addEventListener('input', function() {
            if (!player) return;
            var v = parseInt(this.value); player.setVolume(v);
            if (v === 0) { player.mute(); isMuted = true; } else if (isMuted) { player.unMute(); isMuted = false; }
            document.getElementById('icon-vol').style.display = isMuted ? 'none' : '';
            document.getElementById('icon-muted').style.display = isMuted ? '' : 'none';
        });

        document.getElementById('progress-bar').addEventListener('input', function() {
            if (!player || !player.getDuration) return;
            player.seekTo((parseInt(this.value)/1000)*player.getDuration(), true);
            document.getElementById('progress-fill').style.width = (parseInt(this.value)/10) + '%';
        });

        document.getElementById('btn-fullscreen').addEventListener('click', function() {
            var w = document.querySelector('.embed-stage');
            if (document.fullscreenElement) document.exitFullscreen();
            else if (w.requestFullscreen) w.requestFullscreen();
            else if (w.webkitRequestFullscreen) w.webkitRequestFullscreen();
        });

        document.getElementById('player-container').addEventListener('contextmenu', function(e) { e.preventDefault(); });
    })();
    </script>

<?php endif; ?>

</body>
</html>
