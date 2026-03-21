<?php
// Server-side meta tags for social sharing (crawlers don't execute JS)
$videoId = isset($_GET['v']) ? $_GET['v'] : (isset($_GET['id']) ? $_GET['id'] : '');
$videoId = preg_replace('/[^a-zA-Z0-9_-]/', '', $videoId); // sanitize

// Video titles for meta tags (subset of videos.js data)
$metaData = json_decode(file_get_contents(__DIR__ . '/videos-meta.json'), true);

$title = 'EduTube — Videos Educativos';
$description = 'Video educativo en EduTube — Plataforma de videos educativos curados.';
$image = 'https://edutube.universidadliberte.org/loguito-edutube.png';
$url = 'https://edutube.universidadliberte.org/watch?v=' . $videoId;

if ($videoId && isset($metaData[$videoId])) {
    $v = $metaData[$videoId];
    $title = $v['titulo'] . ' — EduTube';
    $description = $v['descripcion'];
    $image = 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg';
}
?>
<!DOCTYPE html>
<html lang="es" class="page-player">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        frame-src https://www.youtube-nocookie.com;
        img-src 'self' https://img.youtube.com https://i.ytimg.com;
        style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
        font-src https://fonts.gstatic.com;
        script-src 'self' 'unsafe-inline' https://www.youtube.com;
    ">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">

    <!-- SEO / Open Graph -->
    <meta name="description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?php echo $image; ?>">
    <meta property="og:url" content="<?php echo $url; ?>">
    <meta property="og:type" content="video.other">
    <meta property="og:site_name" content="EduTube">
    <meta property="og:locale" content="es_AR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image" content="<?php echo $image; ?>">

    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topbar">
    <div class="topbar-left">
        <a href="index.php" class="logo">
            <img src="loguito-edutube.png" alt="EduTube" class="logo-icon">
            <span class="logo-text">EduTube</span>
        </a>
    </div>
    <div class="topbar-center">
        <div class="search-form">
            <input type="text" class="search-input" placeholder="Buscar videos educativos..." id="search-top">
            <button class="search-btn" title="Buscar" id="search-top-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            </button>
        </div>
    </div>
    <div class="topbar-right"></div>
</header>

<main class="main player-page" id="player-page"></main>

<div class="toast" id="toast"></div>

<script src="videos.js"></script>
<script>
// ── Helpers ──
function getStore(key) { try { return JSON.parse(localStorage.getItem('edutube_' + key)) || []; } catch(e) { return []; } }
function setStore(key, val) { localStorage.setItem('edutube_' + key, JSON.stringify(val)); }
function toggleStore(key, id) {
    var list = getStore(key); var i = list.indexOf(id);
    if (i > -1) list.splice(i, 1); else list.push(id);
    setStore(key, list); return i === -1;
}
function isInStore(key, id) { return getStore(key).indexOf(id) > -1; }

var toastTimer;
function showToast(msg) {
    var t = document.getElementById('toast'); t.textContent = msg; t.classList.add('show');
    clearTimeout(toastTimer); toastTimer = setTimeout(function() { t.classList.remove('show'); }, 2500);
}

function timeAgo(dateStr) {
    var diff = Math.floor((new Date() - new Date(dateStr)) / 1000);
    if (diff < 86400) return 'hoy';
    var d = Math.floor(diff / 86400);
    if (d < 7) return 'hace ' + d + (d===1?' día':' días');
    if (d < 30) return 'hace ' + Math.floor(d/7) + (Math.floor(d/7)===1?' semana':' semanas');
    if (d < 365) return 'hace ' + Math.floor(d/30) + (Math.floor(d/30)===1?' mes':' meses');
    return 'hace ' + Math.floor(d/365) + ' año(s)';
}

function formatViews(n) {
    if (n >= 1000000) return (n/1000000).toFixed(1).replace('.0','') + ' M';
    if (n >= 1000) return (n/1000).toFixed(1).replace('.0','') + ' K';
    return n;
}

function formatDate(dateStr) {
    var d = new Date(dateStr);
    var months = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
}

// ── Main ──
(function() {
    var params = new URLSearchParams(window.location.search);
    var videoId = params.get('v') || params.get('id');
    var video = VIDEOS[videoId];
    var page = document.getElementById('player-page');

    if (!video || !videoId) {
        page.innerHTML = '<div style="padding:4rem;text-align:center;"><h2 style="color:var(--text-muted);margin-bottom:1rem;">Video no encontrado</h2><a href="index.php" class="btn-back">← Volver al inicio</a></div>';
        return;
    }

    var ch = CHANNELS[video.canal];

    // Save to history
    var hist = getStore('history');
    hist = hist.filter(function(h) { return h !== videoId; });
    hist.unshift(videoId);
    if (hist.length > 50) hist = hist.slice(0, 50);
    setStore('history', hist);

    // Build related lists
    function buildCardList(ids) {
        var html = '';
        ids.forEach(function(id) {
            var v = VIDEOS[id];
            var rc = CHANNELS[v.canal];
            html += '<a href="watch?v=' + id + '" class="related-card">' +
                '<div class="r-thumb">' +
                    '<img src="https://img.youtube.com/vi/' + id + '/mqdefault.jpg" alt="" loading="lazy">' +
                    '<span class="r-duration">' + v.duracion + '</span>' +
                '</div>' +
                '<div class="r-info">' +
                    '<div class="r-title">' + v.titulo + '</div>' +
                    '<div class="r-meta">' + rc.nombre + '</div>' +
                    '<div class="r-meta">' + formatViews(v.vistas) + ' repr. · ' + timeAgo(v.fecha) + '</div>' +
                '</div></a>';
        });
        return html;
    }

    // Same channel videos
    var sameChIds = [];
    Object.keys(VIDEOS).forEach(function(id) {
        if (id !== videoId && VIDEOS[id].canal === video.canal) sameChIds.push(id);
    });

    // All others (related)
    var otherIds = [];
    Object.keys(VIDEOS).forEach(function(id) {
        if (id !== videoId && VIDEOS[id].canal !== video.canal) otherIds.push(id);
    });

    var canalHtml = buildCardList(sameChIds.slice(0, 10));
    var relacionadosHtml = buildCardList(otherIds.slice(0, 10));

    var origin = window.location.protocol + '//' + window.location.host;
    var embedSrc = 'https://www.youtube-nocookie.com/embed/' + videoId +
        '?rel=0&modestbranding=1&iv_load_policy=3&controls=0&fs=0&disablekb=0&playsinline=1&enablejsapi=1&origin=' + encodeURIComponent(origin);

    var isLiked = isInStore('liked', videoId);
    var isWL = isInStore('watchlater', videoId);

    page.innerHTML =
        '<div class="player-layout">' +
            '<div class="player-main">' +
                '<div class="player-wrapper">' +
                    '<div class="player-container" id="player-container">' +
                        '<iframe id="yt-player" src="' + embedSrc + '" ' +
                            'sandbox="allow-scripts allow-same-origin allow-presentation" ' +
                            'allow="autoplay; encrypted-media" ' +
                            'title="' + video.titulo + '"></iframe>' +
                        '<div class="yt-shield-top"></div>' +
                        '<div class="yt-shield-bottom"></div>' +
                    '</div>' +
                    '<div class="custom-controls">' +
                        '<button id="btn-play" class="ctrl-btn" title="Reproducir"><svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></button>' +
                        '<div class="ctrl-vol-group">' +
                            '<button id="btn-mute" class="ctrl-btn" title="Silenciar">' +
                                '<svg id="icon-vol" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>' +
                                '<svg id="icon-muted" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" style="display:none"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>' +
                            '</button>' +
                            '<input type="range" id="volume-slider" class="ctrl-volume" min="0" max="100" value="100">' +
                        '</div>' +
                        '<span id="time-current" class="ctrl-time">0:00</span>' +
                        '<span class="ctrl-time-separator">/</span>' +
                        '<span id="time-total" class="ctrl-time">0:00</span>' +
                        '<div class="ctrl-progress-wrap">' +
                            '<input type="range" id="progress-bar" class="ctrl-progress" min="0" max="1000" value="0">' +
                            '<div class="ctrl-progress-fill" id="progress-fill"></div>' +
                        '</div>' +
                        '<button id="btn-fullscreen" class="ctrl-btn" title="Pantalla completa"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg></button>' +
                    '</div>' +
                '</div>' +
                '<div class="video-info">' +
                    '<h1>' + video.titulo + '</h1>' +
                    '<div class="video-info-row">' +
                        '<div class="video-info-channel">' +
                            '<div class="ch-avatar" style="background:' + ch.color + '">' + ch.code + '</div>' +
                            '<div><div class="ch-name">' + ch.nombre + '</div><div class="ch-subs">' + video.categoria + '</div></div>' +
                        '</div>' +
                        '<div class="video-actions">' +
                            '<button class="action-btn' + (isLiked ? ' active' : '') + '" id="btn-like"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg><span id="like-label">' + (isLiked ? 'Te gusta' : 'Me gusta') + '</span></button>' +
                            '<button class="action-btn' + (isWL ? ' active' : '') + '" id="btn-save"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/></svg><span id="save-label">' + (isWL ? 'Guardado' : 'Ver después') + '</span></button>' +
                            '<a href="index.php" class="action-btn"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>Inicio</a>' +
                        '</div>' +
                    '</div>' +
                    '<div class="video-description" id="video-desc">' +
                        '<div class="desc-stats">' + formatViews(video.vistas) + ' reproducciones · ' + formatDate(video.fecha) + '</div>' +
                        '<div class="desc-text">' + video.descripcion + '</div>' +
                        '<div class="desc-toggle" id="desc-toggle">Mostrar más</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="player-sidebar">' +
                '<div class="related-tabs">' +
                    '<button class="related-tab active" data-tab="canal">Del canal</button>' +
                    '<button class="related-tab" data-tab="relacionados">Relacionados</button>' +
                '</div>' +
                '<div class="related-list" id="related-canal">' + canalHtml + '</div>' +
                '<div class="related-list" id="related-relacionados" style="display:none">' + relacionadosHtml + '</div>' +
            '</div>' +
        '</div>';

    // Related tabs
    document.querySelectorAll('.related-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.related-tab').forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            var target = this.getAttribute('data-tab');
            document.getElementById('related-canal').style.display = target === 'canal' ? '' : 'none';
            document.getElementById('related-relacionados').style.display = target === 'relacionados' ? '' : 'none';
        });
    });

    // Description toggle
    document.getElementById('video-desc').addEventListener('click', function() {
        this.classList.toggle('expanded');
        document.getElementById('desc-toggle').textContent = this.classList.contains('expanded') ? 'Mostrar menos' : 'Mostrar más';
    });

    // Like & Save
    document.getElementById('btn-like').addEventListener('click', function() {
        var added = toggleStore('liked', videoId);
        this.classList.toggle('active', added);
        document.getElementById('like-label').textContent = added ? 'Te gusta' : 'Me gusta';
        showToast(added ? 'Agregado a Me gusta' : 'Quitado de Me gusta');
    });
    document.getElementById('btn-save').addEventListener('click', function() {
        var added = toggleStore('watchlater', videoId);
        this.classList.toggle('active', added);
        document.getElementById('save-label').textContent = added ? 'Guardado' : 'Ver después';
        showToast(added ? 'Agregado a Ver después' : 'Quitado de Ver después');
    });

    // Search redirect
    document.getElementById('search-top-btn').addEventListener('click', function() {
        var q = document.getElementById('search-top').value.trim();
        if (q) window.location.href = 'index.php?q=' + encodeURIComponent(q);
    });
    document.getElementById('search-top').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { var q = this.value.trim(); if (q) window.location.href = 'index.php?q=' + encodeURIComponent(q); }
    });

    // ── YouTube IFrame API ──
    var tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    document.head.appendChild(tag);

    var player, progressInterval, isMuted = false;

    window.onYouTubeIframeAPIReady = function() {
        player = new YT.Player('yt-player', { events: { onReady: function() { updateTotal(); setTimeout(updateTotal, 2000); }, onStateChange: onState } });
    };

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
            document.getElementById('progress-fill').style.width = (pct/10)+'%';
            document.getElementById('time-current').textContent = fmt(c);
        }
    }

    function updateTotal() {
        if (!player || !player.getDuration) return;
        var d = player.getDuration();
        if (d > 0) document.getElementById('time-total').textContent = fmt(d);
    }

    function fmt(s) {
        var h=Math.floor(s/3600), m=Math.floor((s%3600)/60), sec=Math.floor(s%60);
        if (h>0) return h+':'+String(m).padStart(2,'0')+':'+String(sec).padStart(2,'0');
        return m+':'+String(sec).padStart(2,'0');
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
        document.getElementById('progress-fill').style.width = (parseInt(this.value)/10)+'%';
    });

    document.getElementById('btn-fullscreen').addEventListener('click', function() {
        var w = document.querySelector('.player-wrapper');
        if (document.fullscreenElement) document.exitFullscreen();
        else if (w.requestFullscreen) w.requestFullscreen();
        else if (w.webkitRequestFullscreen) w.webkitRequestFullscreen();
    });

    document.getElementById('player-container').addEventListener('contextmenu', function(e) { e.preventDefault(); });
})();
</script>

</body>
</html>
