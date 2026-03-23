<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videos — EduTube</title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">

    <!-- SEO -->
    <meta name="description" content="EduTube: plataforma de videos educativos curados para contextos con restricciones de acceso a redes sociales. Sin comentarios, sin algoritmos. Solo educación.">
    <meta name="keywords" content="videos educativos, plataforma educativa, educación en contextos de encierro, Comité de Convivencia Mario Juliano">
    <meta name="author" content="Comité de Convivencia Mario Juliano">
    <link rel="canonical" href="https://edutube.universidadliberte.org/videos">

    <!-- Open Graph -->
    <meta property="og:title" content="EduTube — Videos Educativos">
    <meta property="og:description" content="Contenido audiovisual educativo curado. Sin comentarios, sin algoritmos. Solo educación.">
    <meta property="og:image" content="https://edutube.universidadliberte.org/loguito-edutube.png">
    <meta property="og:url" content="https://edutube.universidadliberte.org">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="EduTube">
    <meta property="og:locale" content="es_AR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="EduTube — Videos Educativos">
    <meta name="twitter:description" content="Contenido audiovisual educativo curado. Sin comentarios, sin algoritmos. Solo educación.">
    <meta name="twitter:image" content="https://edutube.universidadliberte.org/loguito-edutube.png">

    <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- ── TOPBAR ── -->
<header class="topbar">
    <div class="topbar-left">
        <button class="icon-btn" id="menu-toggle" title="Menú">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
        </button>
        <a href="/" class="logo">
            <img src="loguito-edutube.png" alt="EduTube" class="logo-icon">
            <span class="logo-text">EduTube</span>
            <span class="logo-count" id="video-count"></span>
        </a>
    </div>
    <div class="topbar-center">
        <div class="search-form">
            <input type="text" class="search-input" id="search" placeholder="Buscar videos educativos...">
            <button class="search-btn" id="search-btn" title="Buscar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            </button>
        </div>
    </div>
    <div class="topbar-right">
        <button class="icon-btn mobile-search-btn" id="mobile-search-toggle" title="Buscar">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        </button>
    </div>
</header>

<!-- Mobile search overlay -->
<div class="mobile-search-overlay" id="mobile-search-overlay">
    <button class="icon-btn" id="mobile-search-close">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
    </button>
    <input type="text" class="search-input" id="mobile-search-input" placeholder="Buscar videos...">
</div>

<!-- ── SIDEBAR ── -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-section">
        <a href="/" class="sidebar-item">
            <span class="si-icon">🏠</span><span class="si-label">Inicio</span>
        </a>
        <a href="videos" class="sidebar-item active" data-cat="todos">
            <span class="si-icon">📺</span><span class="si-label">Videos <span id="cnt-videos" style="color:var(--text-muted);font-size:0.8em;"></span></span>
        </a>
        <a href="cine" class="sidebar-item">
            <span class="si-icon">🎬</span><span class="si-label">Cine <span id="cnt-cine" style="color:var(--text-muted);font-size:0.8em;"></span></span>
        </a>
        <a href="audiolibros" class="sidebar-item">
            <span class="si-icon">📖</span><span class="si-label">Audiolibros <span id="cnt-audiolibros" style="color:var(--text-muted);font-size:0.8em;"></span></span>
        </a>
        <a href="libros" class="sidebar-item">
            <span class="si-icon">📚</span><span class="si-label">Libros <span id="cnt-libros" style="color:var(--text-muted);font-size:0.8em;"></span></span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Categorías</div>
        <div id="sidebar-categorias"><!-- categorías --></div>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Siguiendo</div>
        <div id="sidebar-following"><!-- canales que sigo --></div>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Tu actividad</div>
        <a href="#" class="sidebar-item" id="nav-history">
            <span class="si-icon">⏱️</span><span class="si-label">Historial</span>
            <span class="si-badge" id="history-count" style="display:none">0</span>
        </a>
        <a href="#" class="sidebar-item" id="nav-watchlater">
            <span class="si-icon">🕐</span><span class="si-label">Reproducir después</span>
            <span class="si-badge" id="watchlater-count" style="display:none">0</span>
        </a>
        <a href="#" class="sidebar-item" id="nav-liked">
            <span class="si-icon">👍</span><span class="si-label">Videos que me gustan</span>
            <span class="si-badge" id="liked-count" style="display:none">0</span>
        </a>
    </div>
    <div class="sidebar-footer">
        <strong>EduTube</strong> — Plataforma de Videos Educativos<br>
        <a href="https://comite.cooperativaliberte.coop/" target="_blank" style="color:inherit;text-decoration:underline;">Comité de Convivencia Mario Juliano</a> &copy; 2026
    </div>
</nav>

<!-- ── MAIN ── -->
<main class="main" id="main-content">
    <div class="chips" id="chips">
        <button class="chip active" data-cat="todos">Todos</button>
        <button class="chip" data-filter="watchlater">🕐 Reproducir después</button>
        <button class="chip" data-filter="liked">👍 Me gusta</button>
        <button class="chip" data-filter="history">⏱ Historial</button>
    </div>

    <div class="video-grid" id="video-grid"></div>
</main>

<!-- ── BOTTOM NAV (mobile) ── -->
<nav class="bottom-nav">
    <a href="/" class="bottom-nav-item">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        Inicio
    </a>
    <a href="videos" class="bottom-nav-item active">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/></svg>
        Videos
    </a>
    <button class="bottom-nav-item" id="mobile-search-trigger">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        Buscar
    </button>
    <button class="bottom-nav-item" id="mobile-watchlater-nav">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/></svg>
        Reproducir después
    </button>
    <button class="bottom-nav-item" id="mobile-history-nav">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M13 3a9 9 0 00-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42A8.954 8.954 0 0013 21a9 9 0 000-18zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
        Historial
    </button>
</nav>

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
    if (!dateStr) return '';
    var diff = Math.floor((new Date() - new Date(dateStr)) / 1000);
    if (diff < 60) return 'hace un momento';
    if (diff < 3600) return 'hace ' + Math.floor(diff/60) + ' min';
    if (diff < 86400) return 'hace ' + Math.floor(diff/3600) + ' h';
    var d = Math.floor(diff / 86400);
    if (d < 7) return 'hace ' + d + (d===1?' día':' días');
    if (d < 30) return 'hace ' + Math.floor(d/7) + (Math.floor(d/7)===1?' semana':' semanas');
    if (d < 365) return 'hace ' + Math.floor(d/30) + (Math.floor(d/30)===1?' mes':' meses');
    return 'hace ' + Math.floor(d/365) + (Math.floor(d/365)===1?' año':' años');
}

function formatViews(n) {
    n = parseInt(n) || 0;
    if (n >= 1000000) return (n/1000000).toFixed(1).replace('.0','') + ' M';
    if (n >= 1000) return (n/1000).toFixed(1).replace('.0','') + ' K';
    return n.toString();
}

// ── Data from API ──
var ALL_VIDEOS = [];
var ALL_CHANNELS = [];
var ALL_PLAYLISTS = [];
var ALL_CATEGORIAS = [];
var currentApiUrl = 'api.php?action=videos';

function loadVideos(apiUrl) {
    if (apiUrl) currentApiUrl = apiUrl;
    fetch(currentApiUrl)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            ALL_VIDEOS = data.videos || [];
            ALL_CHANNELS = data.canales || [];
            ALL_PLAYLISTS = data.playlists || [];
            ALL_CATEGORIAS = data.categorias || [];
            buildSidebar();
            // Check if URL has ?canal= or ?q= parameter
            var urlParams = new URLSearchParams(window.location.search);
            var canalParam = urlParams.get('canal');
            var qParam = urlParams.get('q');
            if (canalParam) {
                filterCanal(canalParam);
            } else if (qParam) {
                document.getElementById('search').value = qParam;
                doSearch(qParam);
            } else {
                showVideosWithSort(ALL_VIDEOS);
            }
            updateBadges();
            // Show total count
            fetch('api.php?action=total_titulos').then(function(r){return r.json();}).then(function(d){
                document.getElementById('video-count').textContent = d.total + ' títulos';
                document.getElementById('cnt-videos').textContent = '(' + d.videos + ')';
                document.getElementById('cnt-cine').textContent = '(' + d.cine + ')';
                document.getElementById('cnt-audiolibros').textContent = '(' + d.audiolibros + ')';
                var cl = document.getElementById('cnt-libros'); if (cl) cl.textContent = '(' + (d.libros||0) + ')';
            });
        });
}

function buildSidebarCategorias() {
    var container = document.getElementById('sidebar-categorias');
    if (!container) return;
    var html = '';
    ALL_CATEGORIAS.forEach(function(cat) {
        // Canales de esta categoría
        var catChannels = ALL_CHANNELS.filter(function(c) { return c.categoria_nombre === cat.nombre; });

        html += '<div class="sidebar-channel-group">' +
            '<a href="#" class="sidebar-item" data-categoria="' + cat.nombre + '" title="' + cat.nombre + '">' +
                '<span class="si-icon">' + (cat.icono || '📁') + '</span>' +
                '<span class="si-label">' + cat.nombre + '</span>' +
                (catChannels.length > 0 ? '<span class="si-expand" data-toggle="cat-' + cat.id + '">▸</span>' : '') +
            '</a>' +
            '<div class="sidebar-playlists-group" id="cat-' + cat.id + '" style="display:none;">';
        catChannels.forEach(function(c) {
            html += '<a href="#" class="sidebar-item" data-canal="' + c.id + '" title="' + c.nombre + '" style="padding-left:2.5rem;font-size:0.82rem;">' +
                '<span class="si-icon" style="color:' + c.color + '">●</span>' +
                '<span class="si-label">' + c.nombre + '</span></a>';
        });
        html += '</div></div>';
    });
    container.innerHTML = html;

    // Click handlers para categorías
    container.querySelectorAll('[data-categoria]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            var catName = this.getAttribute('data-categoria');
            clearAllActive();
            this.classList.add('active');
            loadVideos('api.php?action=videos&categoria=' + encodeURIComponent(catName));
            closeSidebar();
        });
    });

    // Click handlers para canales dentro de categorías
    container.querySelectorAll('[data-canal]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            filterCanal(this.getAttribute('data-canal'));
            closeSidebar();
        });
    });

    // Expand/collapse
    container.querySelectorAll('.si-expand').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var targetId = this.getAttribute('data-toggle');
            var target = document.getElementById(targetId);
            if (target) {
                var show = target.style.display === 'none';
                target.style.display = show ? 'block' : 'none';
                this.textContent = show ? '▾' : '▸';
            }
        });
    });
}

function buildSidebarFollowing() {
    var container = document.getElementById('sidebar-following');
    if (!container) return;
    var following = getStore('following');
    if (following.length === 0) {
        container.innerHTML = '<div style="padding:0.3rem 1rem;font-size:0.8rem;color:#999;">No seguís ningún canal</div>';
        return;
    }
    var html = '';
    ALL_CHANNELS.forEach(function(c) {
        if (following.indexOf(String(c.id)) > -1) {
            html += '<a href="#" class="sidebar-item" data-canal="' + c.id + '" title="' + c.nombre + '">' +
                '<span class="si-icon" style="color:' + c.color + '">●</span>' +
                '<span class="si-label">' + c.nombre + '</span></a>';
        }
    });
    container.innerHTML = html || '<div style="padding:0.3rem 1rem;font-size:0.8rem;color:#999;">No seguís ningún canal</div>';

    container.querySelectorAll('[data-canal]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            filterCanal(this.getAttribute('data-canal'));
            closeSidebar();
        });
    });
}

function toggleFollow(canalId) {
    var added = toggleStore('following', String(canalId));
    showToast(added ? 'Siguiendo canal' : 'Dejaste de seguir');
    buildSidebarFollowing();
    return added;
}

function buildSidebar() {
    buildSidebarCategorias();
    buildSidebarFollowing();
}

function filterPlaylist(plId) {
    clearAllActive();
    var side = document.querySelector('.sidebar-item[data-playlist="' + plId + '"]');
    if (side) side.classList.add('active');
    fetch('api.php?action=playlist&id=' + plId)
        .then(function(r) { return r.json(); })
        .then(function(data) { showVideosWithSort(data.videos || []); });
}

// ── Render ──
var PAGE_SIZE = 24;
var currentVideos = [];
var currentPage = 0;

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
                '<a href="canal?id=' + (v.canal_id || '') + '" class="card-channel">' + (v.canal_nombre || '') + '</a>' +
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
            updateBadges();
        });
    });
}

function renderGrid(videos) {
    var list = videos || ALL_VIDEOS;
    currentVideos = list;
    currentPage = 0;
    var grid = document.getElementById('video-grid');
    grid.style.display = ''; // Restaurar grid CSS

    var page = list.slice(0, PAGE_SIZE);
    var html = '';
    page.forEach(function(v) { html += videoCardHTML(v); });

    if (list.length > PAGE_SIZE) {
        html += '<div class="load-more-container" id="load-more-container">' +
            '<button class="btn-load-more" id="btn-load-more">Cargar más videos (' + (list.length - PAGE_SIZE) + ' restantes)</button>' +
        '</div>';
    }

    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No se encontraron videos</p>';
    bindWatchLaterButtons(grid);

    var btnMore = document.getElementById('btn-load-more');
    if (btnMore) {
        btnMore.addEventListener('click', loadMoreVideos);
    }
}

function loadMoreVideos() {
    currentPage++;
    var start = currentPage * PAGE_SIZE;
    var end = start + PAGE_SIZE;
    var page = currentVideos.slice(start, end);
    var remaining = currentVideos.length - end;

    // Quitar botón actual
    var container = document.getElementById('load-more-container');
    if (container) container.remove();

    // Agregar videos
    var grid = document.getElementById('video-grid');
    var tempDiv = document.createElement('div');
    var html = '';
    page.forEach(function(v) { html += videoCardHTML(v); });

    if (remaining > 0) {
        html += '<div class="load-more-container" id="load-more-container">' +
            '<button class="btn-load-more" id="btn-load-more">Cargar más videos (' + remaining + ' restantes)</button>' +
        '</div>';
    }

    tempDiv.innerHTML = html;
    while (tempDiv.firstChild) {
        grid.appendChild(tempDiv.firstChild);
    }
    bindWatchLaterButtons(grid);

    var btnMore = document.getElementById('btn-load-more');
    if (btnMore) {
        btnMore.addEventListener('click', loadMoreVideos);
    }
}

function updateBadges() {
    ['history','watchlater','liked'].forEach(function(key) {
        var n = getStore(key).length;
        var el = document.getElementById(key + '-count');
        if (n > 0) { el.textContent = n; el.style.display = ''; } else { el.style.display = 'none'; }
    });
}

// ── Filters ──
function clearAllActive() {
    document.querySelectorAll('.chip').forEach(function(c) { c.classList.remove('active'); });
    document.querySelectorAll('.sidebar-item').forEach(function(s) { s.classList.remove('active'); });
}

function filterAll() {
    clearAllActive();
    document.querySelector('.chip[data-cat="todos"]').classList.add('active');
    loadVideos('api.php?action=videos');
}

function filterCanal(canalId) {
    clearAllActive();
    var chip = document.querySelector('.chip[data-canal="' + canalId + '"]');
    if (chip) chip.classList.add('active');
    var side = document.querySelector('.sidebar-item[data-canal="' + canalId + '"]');
    if (side) side.classList.add('active');

    // Cargar todos los videos de este canal via API
    fetch('api.php?action=videos&canal_id=' + canalId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var canalVideos = data.videos || [];
            renderCanalView(canalId, canalVideos);
        });
}

function renderCanalView(canalId, canalVideos) {
    // Find channel info
    var canal = null;
    ALL_CHANNELS.forEach(function(c) { if (String(c.id) === String(canalId)) canal = c; });
    if (!canal) return;

    // Get playlists for this channel
    var chPlaylists = ALL_PLAYLISTS.filter(function(p) { return String(p.canal_id) === String(canalId) && parseInt(p.total_videos) > 0; });
    var totalVideos = canalVideos.length;

    var grid = document.getElementById('video-grid');
    grid.style.display = 'block';
    var html = '';

    // Channel header
    var isFollowing = isInStore('following', String(canalId));
    html += '<div class="channel-header">' +
        '<div class="channel-header-avatar" style="background:' + canal.color + '">' + canal.codigo + '</div>' +
        '<div class="channel-header-info">' +
            '<div class="channel-header-name">' + canal.nombre + '</div>' +
            '<div class="channel-header-stats">' + totalVideos + ' videos · ' + chPlaylists.length + ' listas</div>' +
        '</div>' +
        '<button class="btn-follow' + (isFollowing ? ' following' : '') + '" data-canal="' + canalId + '">' + (isFollowing ? 'Siguiendo' : 'Seguir') + '</button>' +
    '</div>';

    // Videos recientes row (newest 6)
    var recentVideos = sortVideos(canalVideos, 'newest').slice(0, 6);
    if (recentVideos.length > 0) {
        html += '<div class="channel-row">' +
            '<div class="channel-row-header">' +
                '<span class="channel-section-title">Videos recientes</span>' +
                (totalVideos > 6 ? '<a href="#" class="channel-row-more btn-all-videos">Ver todos los videos</a>' : '') +
            '</div>' +
            '<div class="channel-row-videos">';
        recentVideos.forEach(function(v) { html += videoCardHTML(v); });
        html += '</div></div>';
    }

    // Each playlist as a horizontal row with its videos
    if (chPlaylists.length > 0) {
        chPlaylists.forEach(function(p) {
            html += '<div class="channel-row canal-playlist-row" data-playlist="' + p.id + '">' +
                '<div class="channel-row-header">' +
                    '<span class="channel-section-title">' + p.nombre + '</span>' +
                    '<span class="channel-section-count">' + p.total_videos + ' videos</span>' +
                    '<a href="#" class="channel-row-more playlist-row-more" data-playlist="' + p.id + '">Ver lista completa</a>' +
                '</div>' +
                '<div class="channel-row-videos playlist-videos-container" data-playlist="' + p.id + '">' +
                    '<div class="playlist-loading">Cargando...</div>' +
                '</div>' +
            '</div>';
        });
    }

    grid.innerHTML = html;
    bindWatchLaterButtons(grid);

    // Load videos for each playlist
    chPlaylists.forEach(function(p) {
        fetch('api.php?action=playlist&id=' + p.id)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var container = grid.querySelector('.playlist-videos-container[data-playlist="' + p.id + '"]');
                if (!container) return;
                var vids = (data.videos || []).slice(0, 6);
                var vhtml = '';
                vids.forEach(function(v) { vhtml += videoCardHTML(v); });
                container.innerHTML = vhtml;
                bindWatchLaterButtons(container);
            });
    });

    // Bind follow button
    var btnFollow = grid.querySelector('.btn-follow');
    if (btnFollow) {
        btnFollow.addEventListener('click', function() {
            var cid = this.getAttribute('data-canal');
            var added = toggleFollow(cid);
            this.classList.toggle('following', added);
            this.textContent = added ? 'Siguiendo' : 'Seguir';
        });
    }

    // Bind "Ver todos los videos" button
    var btnAll = grid.querySelector('.btn-all-videos');
    if (btnAll) {
        btnAll.addEventListener('click', function(e) {
            e.preventDefault();
            showVideosWithSort(canalVideos);
        });
    }

    // Bind "Ver lista completa" links
    grid.querySelectorAll('.playlist-row-more').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            filterPlaylist(this.getAttribute('data-playlist'));
        });
    });
}

// ── Sort ──
function sortVideos(videos, sortBy) {
    var sorted = videos.slice();
    switch (sortBy) {
        case 'newest': sorted.sort(function(a,b) { return (b.fecha_yt||'').localeCompare(a.fecha_yt||''); }); break;
        case 'oldest': sorted.sort(function(a,b) { return (a.fecha_yt||'').localeCompare(b.fecha_yt||''); }); break;
        case 'popular': sorted.sort(function(a,b) { return (parseInt(b.vistas_yt)||0) - (parseInt(a.vistas_yt)||0); }); break;
        case 'az': sorted.sort(function(a,b) { return (a.titulo||'').localeCompare(b.titulo||''); }); break;
    }
    return sorted;
}

var VIDEOS_PER_CHANNEL = 4;

function showVideosWithSort(videos, activeSort) {
    activeSort = activeSort || 'newest';
    var sorted = sortVideos(videos, activeSort);

    var grid = document.getElementById('video-grid');
    var sortBar = '<div class="sort-bar">' +
        '<span class="sort-label">' + videos.length + ' videos · Ordenar por:</span>' +
        '<button class="sort-btn' + (activeSort==='newest'?' active':'') + '" data-sort="newest">Más recientes</button>' +
        '<button class="sort-btn' + (activeSort==='popular'?' active':'') + '" data-sort="popular">Más vistos</button>' +
        '<button class="sort-btn' + (activeSort==='oldest'?' active':'') + '" data-sort="oldest">Más antiguos</button>' +
        '<button class="sort-btn' + (activeSort==='az'?' active':'') + '" data-sort="az">A-Z</button>' +
    '</div>';
    renderGrid(sorted);
    grid.insertAdjacentHTML('afterbegin', sortBar);

    grid.querySelectorAll('.sort-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            showVideosWithSort(videos, this.getAttribute('data-sort'));
        });
    });
}

// Catálogos IA para actividad cruzada (cargado desde API)
var iaAllCatalog = [];
fetch('api.php?action=contenido_ia')
    .then(function(r) { return r.json(); })
    .then(function(data) { iaAllCatalog = data; })
    .catch(function() {});

function iaCardHTML(item) {
    var thumbUrl = 'https://archive.org/download/' + item.ia_id + '/__ia_thumb.jpg';
    return '<div class="video-card">' +
        '<a href="watch?v=' + item.id + '" class="thumb">' +
            '<img src="' + thumbUrl + '" alt="" loading="lazy">' +
            (item.duracion ? '<span class="duration-badge">' + item.duracion + '</span>' : '') +
        '</a>' +
        '<div class="card-info">' +
            '<div class="channel-avatar" style="background:#e63946;font-size:0.65rem;">🎬</div>' +
            '<div class="card-text">' +
                '<a href="watch?v=' + item.id + '" class="card-title">' + item.titulo + '</a>' +
                '<div class="card-channel-static">' + (item.director || '') + '</div>' +
                '<div class="card-stats">' + item.section + '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
}

function filterSpecial(type) {
    clearAllActive();
    var chip = document.querySelector('.chip[data-filter="' + type + '"]');
    if (chip) chip.classList.add('active');
    var list = getStore(type);
    var ytVideos = ALL_VIDEOS.filter(function(v) { return list.indexOf(v.youtube_id) > -1; });
    var ytIds = ytVideos.map(function(v) { return v.youtube_id; });
    var iaItems = iaAllCatalog.filter(function(item) { return list.indexOf(item.id) > -1; });

    // Render YouTube videos + IA items
    var grid = document.getElementById('video-grid');
    grid.style.display = '';
    var html = '';
    ytVideos.forEach(function(v) { html += videoCardHTML(v); });
    iaItems.forEach(function(item) { html += iaCardHTML(item); });
    var total = ytVideos.length + iaItems.length;
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No hay contenido guardado</p>';
    bindWatchLaterButtons(grid);
}

function doSearch(q) {
    q = q.trim();
    if (!q) { filterAll(); return; }
    fetch('api.php?action=search&q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(data) { renderGrid(data.videos || []); });
}

// ── Init ──
loadVideos();

// Chips (static)
document.querySelector('.chip[data-cat="todos"]').addEventListener('click', filterAll);
document.querySelectorAll('.chip[data-filter]').forEach(function(c) {
    c.addEventListener('click', function() { filterSpecial(this.getAttribute('data-filter')); });
});

// Sidebar static items
document.querySelector('.sidebar-item[data-cat="todos"]').addEventListener('click', function(e) { e.preventDefault(); filterAll(); closeSidebar(); });
document.getElementById('nav-history').addEventListener('click', function(e) { e.preventDefault(); filterSpecial('history'); closeSidebar(); });
document.getElementById('nav-watchlater').addEventListener('click', function(e) { e.preventDefault(); filterSpecial('watchlater'); closeSidebar(); });
document.getElementById('nav-liked').addEventListener('click', function(e) { e.preventDefault(); filterSpecial('liked'); closeSidebar(); });

// Search
var searchTimer;
document.getElementById('search').addEventListener('input', function() {
    clearTimeout(searchTimer);
    var q = this.value;
    searchTimer = setTimeout(function() { doSearch(q); }, 300);
});

// Mobile search
['mobile-search-toggle','mobile-search-trigger'].forEach(function(id) {
    document.getElementById(id).addEventListener('click', function() {
        document.getElementById('mobile-search-overlay').classList.add('open');
        document.getElementById('mobile-search-input').focus();
    });
});
document.getElementById('mobile-search-close').addEventListener('click', function() {
    document.getElementById('mobile-search-overlay').classList.remove('open');
    document.getElementById('mobile-search-input').value = '';
    filterAll();
});
document.getElementById('mobile-search-input').addEventListener('input', function() {
    clearTimeout(searchTimer);
    var q = this.value;
    searchTimer = setTimeout(function() { doSearch(q); }, 300);
});
document.getElementById('mobile-watchlater-nav').addEventListener('click', function() { filterSpecial('watchlater'); });
document.getElementById('mobile-history-nav').addEventListener('click', function() { filterSpecial('history'); });

// Sidebar toggle
function closeSidebar() {
    if (window.innerWidth <= 1024) {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-backdrop').classList.remove('open');
    }
}
document.getElementById('menu-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebar-backdrop').classList.toggle('open');
});
document.getElementById('sidebar-backdrop').addEventListener('click', closeSidebar);

var msb = document.getElementById('mobile-search-toggle');
function chkMobile() { msb.style.display = window.innerWidth <= 768 ? '' : 'none'; }
chkMobile(); window.addEventListener('resize', chkMobile);
</script>

</body>
</html>
