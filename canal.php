<?php
// Server-side meta tags for SEO
$canalId = intval($_GET['id'] ?? 0);
$title = 'EduTube — Canal';
$description = 'Canal en EduTube — Plataforma de videos educativos curados.';
$image = 'https://edutube.universidadliberte.org/loguito-edutube.png';
$url = 'https://edutube.universidadliberte.org/canal?id=' . $canalId;

if ($canalId) {
    try {
        require_once __DIR__ . '/config.php';
        $db = getDB();
        $stmt = $db->prepare("SELECT nombre, descripcion, thumbnail_url FROM canales WHERE id = ? AND activo = 1");
        $stmt->execute([$canalId]);
        $c = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($c) {
            $title = $c['nombre'] . ' — EduTube';
            $description = $c['descripcion'] ?: $title;
            if ($c['thumbnail_url']) $image = $c['thumbnail_url'];
        }
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        img-src 'self' https://img.youtube.com https://i.ytimg.com https://yt3.ggpht.com https://lh3.googleusercontent.com;
        style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
        font-src https://fonts.gstatic.com;
        script-src 'self' 'unsafe-inline';
    ">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">

    <meta name="description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?php echo $image; ?>">
    <meta property="og:url" content="<?php echo $url; ?>">
    <meta property="og:type" content="profile">
    <meta property="og:site_name" content="EduTube">

    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topbar">
    <div class="topbar-left">
        <a href="/" class="logo">
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

<main class="main canal-page" id="canal-page"></main>

<div class="toast" id="toast"></div>

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
    if (!dateStr) return '';
    var d = new Date(dateStr);
    var months = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
}

function videoCardHTML(v) {
    var isWL = isInStore('watchlater', v.youtube_id);
    return '<div class="video-card" data-id="' + v.youtube_id + '">' +
        '<a href="watch?v=' + v.youtube_id + '" class="thumb">' +
            '<img src="https://img.youtube.com/vi/' + v.youtube_id + '/mqdefault.jpg" alt="" loading="lazy">' +
            (v.duracion ? '<span class="duration-badge">' + v.duracion + '</span>' : '') +
            '<div class="thumb-actions">' +
                '<button class="thumb-action-btn btn-wl' + (isWL ? ' saved' : '') + '" data-id="' + v.youtube_id + '" title="Reproducir después">🕐</button>' +
            '</div>' +
        '</a>' +
        '<div class="card-info">' +
            '<div class="channel-avatar" style="background:' + (v.canal_color || '#2e8b47') + '">' + (v.canal_codigo || '?') + '</div>' +
            '<div class="card-text">' +
                '<a href="watch?v=' + v.youtube_id + '" class="card-title">' + v.titulo + '</a>' +
                '<div class="card-channel-static">' + (v.canal_nombre || '') + '</div>' +
                '<div class="card-stats">' + formatViews(v.vistas_yt) + ' reproducciones · ' + timeAgo(v.fecha_yt) + '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
}

function bindWatchLaterButtons(container) {
    container.querySelectorAll('.btn-wl').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); e.stopPropagation();
            var added = toggleStore('watchlater', this.getAttribute('data-id'));
            this.classList.toggle('saved', added);
            showToast(added ? 'Agregado a Reproducir después' : 'Quitado de Reproducir después');
        });
    });
}

// ── Search redirect ──
document.getElementById('search-top-btn').addEventListener('click', function() {
    var q = document.getElementById('search-top').value.trim();
    if (q) window.location.href = 'index.php?q=' + encodeURIComponent(q);
});
document.getElementById('search-top').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        var q = this.value.trim();
        if (q) window.location.href = 'index.php?q=' + encodeURIComponent(q);
    }
});

// ── Main ──
(function() {
    var params = new URLSearchParams(window.location.search);
    var canalId = params.get('id');
    var page = document.getElementById('canal-page');

    if (!canalId) {
        page.innerHTML = '<p style="padding:3rem;text-align:center;color:var(--text-muted);">Canal no especificado.</p>';
        return;
    }

    page.innerHTML = '<p style="padding:3rem;text-align:center;color:var(--text-muted);">Cargando canal...</p>';

    fetch('api.php?action=canal&id=' + canalId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.error) {
                page.innerHTML = '<p style="padding:3rem;text-align:center;color:var(--text-muted);">' + data.error + '</p>';
                return;
            }
            renderCanalPage(data);
        });

    function renderCanalPage(data) {
        var canal = data.canal;
        var stats = data.stats;
        var videos = data.videos || [];
        var playlists = data.playlists || [];
        var isFollowing = isInStore('following', String(canal.id));

        var html = '';

        // ── Banner ──
        if (canal.banner_url) {
            html += '<div class="canal-banner" style="background-image:url(\'' + canal.banner_url + '=w1707\')"></div>';
        } else {
            html += '<div class="canal-banner canal-banner-gradient" style="background:linear-gradient(135deg, ' + canal.color + ' 0%, ' + canal.color + '88 100%)"></div>';
        }

        // ── Profile header ──
        var avatarHTML;
        if (canal.thumbnail_url) {
            avatarHTML = '<img src="' + canal.thumbnail_url + '" alt="' + canal.nombre + '" class="canal-profile-img">';
        } else {
            avatarHTML = '<div class="canal-profile-avatar" style="background:' + canal.color + '">' + canal.codigo + '</div>';
        }

        html += '<div class="canal-profile-header">' +
            '<div class="canal-profile-left">' +
                avatarHTML +
                '<div class="canal-profile-info">' +
                    '<h1 class="canal-profile-name">' + canal.nombre + '</h1>' +
                    '<div class="canal-profile-meta">';

        // Stats line
        var metaParts = [];
        if (canal.custom_url) metaParts.push(canal.custom_url);
        if (canal.country) metaParts.push(canal.country);
        if (canal.youtube_created_at) metaParts.push('En YouTube desde ' + formatDate(canal.youtube_created_at));
        if (metaParts.length > 0) html += metaParts.join(' · ');
        html += '</div>';

        html += '</div></div>' +
            '<button class="btn-follow' + (isFollowing ? ' following' : '') + '" id="btn-follow" data-canal="' + canal.id + '">' + (isFollowing ? 'Siguiendo' : 'Seguir') + '</button>' +
        '</div>';

        // ── Stats cards ──
        // Count followers from localStorage
        var followers = getStore('following');
        var isFollowedHere = followers.indexOf(String(canal.id)) > -1;
        // Count likes for this channel's videos
        var likes = getStore('likes');
        var channelLikes = 0;
        videos.forEach(function(v) { if (likes.indexOf(v.youtube_id) > -1) channelLikes++; });

        html += '<div class="canal-stats-grid">';
        html += '<div class="canal-stat-card"><div class="canal-stat-number">' + stats.edutube_videos + '</div><div class="canal-stat-label">Videos en EduTube</div></div>';
        html += '<div class="canal-stat-card"><div class="canal-stat-number">' + stats.edutube_playlists + '</div><div class="canal-stat-label">Listas</div></div>';
        html += '<div class="canal-stat-card"><div class="canal-stat-number">' + formatViews(stats.edutube_views) + '</div><div class="canal-stat-label">Reproducciones EduTube</div></div>';
        html += '<div class="canal-stat-card"><div class="canal-stat-number">' + channelLikes + '</div><div class="canal-stat-label">Me gusta</div></div>';
        if (canal.subscriber_count > 0) html += '<div class="canal-stat-card"><div class="canal-stat-number">' + formatViews(canal.subscriber_count) + '</div><div class="canal-stat-label">Suscriptores YouTube</div></div>';
        if (stats.total_yt_views > 0) html += '<div class="canal-stat-card"><div class="canal-stat-number">' + formatViews(stats.total_yt_views) + '</div><div class="canal-stat-label">Vistas YouTube</div></div>';
        html += '</div>';

        // ── Description ──
        if (canal.descripcion) {
            var desc = canal.descripcion;
            var isLong = desc.length > 200;
            html += '<div class="canal-description-card">' +
                '<div class="canal-description-text' + (isLong ? ' collapsed' : '') + '" id="canal-desc">' + desc.replace(/\n/g, '<br>') + '</div>' +
                (isLong ? '<button class="canal-desc-toggle" id="desc-toggle">Mostrar más</button>' : '') +
            '</div>';
        }

        // ── Category ──
        if (canal.categoria_nombre) {
            html += '<div class="canal-category-badge">Categoría: ' + canal.categoria_nombre + '</div>';
        }

        // ── Videos recientes ──
        if (videos.length > 0) {
            var recentVideos = videos.slice(0, 8);
            html += '<div class="canal-section">' +
                '<div class="canal-section-header">' +
                    '<h2 class="canal-section-title">Videos recientes</h2>' +
                    (videos.length > 8 ? '<button class="canal-section-more" id="btn-show-all-videos">Ver todos (' + videos.length + ')</button>' : '') +
                '</div>' +
                '<div class="channel-row-videos">';
            recentVideos.forEach(function(v) { html += videoCardHTML(v); });
            html += '</div></div>';
        }

        // ── Playlists ──
        if (playlists.length > 0) {
            playlists.forEach(function(p) {
                html += '<div class="canal-section canal-playlist-section" data-playlist="' + p.id + '">' +
                    '<div class="canal-section-header">' +
                        '<h2 class="canal-section-title">' + p.nombre + '</h2>' +
                        '<span class="canal-section-count">' + p.total_videos + ' videos</span>' +
                        '<a href="index.php?playlist=' + p.id + '" class="canal-section-more playlist-link" data-playlist="' + p.id + '">Ver lista completa</a>' +
                    '</div>' +
                    '<div class="channel-row-videos playlist-videos-container" data-playlist="' + p.id + '">' +
                        '<div class="playlist-loading">Cargando...</div>' +
                    '</div>' +
                '</div>';
            });
        }

        page.innerHTML = html;
        bindWatchLaterButtons(page);

        // Load playlist videos
        playlists.forEach(function(p) {
            fetch('api.php?action=playlist&id=' + p.id)
                .then(function(r) { return r.json(); })
                .then(function(plData) {
                    var container = page.querySelector('.playlist-videos-container[data-playlist="' + p.id + '"]');
                    if (!container) return;
                    var vids = (plData.videos || []).slice(0, 8);
                    var vhtml = '';
                    vids.forEach(function(v) { vhtml += videoCardHTML(v); });
                    container.innerHTML = vhtml;
                    bindWatchLaterButtons(container);
                });
        });

        // Bind follow
        document.getElementById('btn-follow').addEventListener('click', function() {
            var added = toggleStore('following', String(canal.id));
            this.classList.toggle('following', added);
            this.textContent = added ? 'Siguiendo' : 'Seguir';
        });

        // Bind description toggle
        var descToggle = document.getElementById('desc-toggle');
        if (descToggle) {
            descToggle.addEventListener('click', function() {
                var el = document.getElementById('canal-desc');
                el.classList.toggle('collapsed');
                this.textContent = el.classList.contains('collapsed') ? 'Mostrar más' : 'Mostrar menos';
            });
        }

        // Bind show all videos
        var btnAllVids = document.getElementById('btn-show-all-videos');
        if (btnAllVids) {
            btnAllVids.addEventListener('click', function() {
                // Replace recent section with full grid
                var section = this.closest('.canal-section');
                var sorted = videos.slice();
                var sortHtml = '<div class="canal-section">' +
                    '<div class="canal-section-header">' +
                        '<h2 class="canal-section-title">Todos los videos (' + videos.length + ')</h2>' +
                    '</div>' +
                    '<div class="canal-all-videos-grid">';
                sorted.forEach(function(v) { sortHtml += videoCardHTML(v); });
                sortHtml += '</div></div>';
                section.outerHTML = sortHtml;
                bindWatchLaterButtons(page);
            });
        }
    }
})();
</script>

</body>
</html>
