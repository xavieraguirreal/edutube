<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTube — Videos Educativos</title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">

    <!-- SEO -->
    <meta name="description" content="EduTube: plataforma de videos educativos curados para contextos con restricciones de acceso a redes sociales. Sin comentarios, sin algoritmos. Solo educación.">
    <meta name="keywords" content="videos educativos, plataforma educativa, educación en contextos de encierro, Comité de Convivencia Mario Juliano">
    <meta name="author" content="Comité de Convivencia Mario Juliano">
    <link rel="canonical" href="https://edutube.universidadliberte.org/">

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
<script>
// Redirect first-time visitors to landing page
if (!localStorage.getItem('edutube_welcomed')) {
    window.location.replace('landing');
}
</script>

<!-- ── TOPBAR ── -->
<header class="topbar">
    <div class="topbar-left">
        <button class="icon-btn" id="menu-toggle" title="Menú">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
        </button>
        <a href="index.php" class="logo">
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
        <a href="index.php" class="sidebar-item active" data-cat="todos">
            <span class="si-icon">🏠</span><span class="si-label">Inicio</span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Categorías</div>
        <div id="sidebar-categorias"><!-- categorías --></div>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Canales</div>
        <div id="sidebar-channels"><!-- canales + sus playlists --></div>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Tu actividad</div>
        <a href="#" class="sidebar-item" id="nav-history">
            <span class="si-icon">⏱️</span><span class="si-label">Historial</span>
            <span class="si-badge" id="history-count" style="display:none">0</span>
        </a>
        <a href="#" class="sidebar-item" id="nav-watchlater">
            <span class="si-icon">🕐</span><span class="si-label">Ver después</span>
            <span class="si-badge" id="watchlater-count" style="display:none">0</span>
        </a>
        <a href="#" class="sidebar-item" id="nav-liked">
            <span class="si-icon">👍</span><span class="si-label">Videos que me gustan</span>
            <span class="si-badge" id="liked-count" style="display:none">0</span>
        </a>
    </div>
    <div class="sidebar-footer">
        <strong>EduTube</strong> — Plataforma de Videos Educativos<br>
        Comité de Convivencia Mario Juliano &copy; 2026
    </div>
</nav>

<!-- ── MAIN ── -->
<main class="main" id="main-content">
    <div class="chips" id="chips">
        <button class="chip active" data-cat="todos">Todos</button>
        <span id="channel-chips"><!-- se llena dinámicamente --></span>
        <button class="chip" data-filter="watchlater">🕐 Ver después</button>
        <button class="chip" data-filter="liked">👍 Me gusta</button>
        <button class="chip" data-filter="history">⏱ Historial</button>
    </div>
    <div class="video-grid" id="video-grid"></div>
</main>

<!-- ── BOTTOM NAV (mobile) ── -->
<nav class="bottom-nav">
    <a href="index.php" class="bottom-nav-item active">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        Inicio
    </a>
    <button class="bottom-nav-item" id="mobile-search-trigger">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        Buscar
    </button>
    <button class="bottom-nav-item" id="mobile-watchlater-nav">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/></svg>
        Ver después
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
var currentApiUrl = 'api.php?action=videos'; // portada por defecto

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
            buildChips();
            showVideosWithSort(ALL_VIDEOS);
            updateBadges();
            document.getElementById('video-count').textContent = ALL_VIDEOS.length + ' videos';
        });
}

function buildSidebarCategorias() {
    var container = document.getElementById('sidebar-categorias');
    if (!container) return;
    var html = '';
    ALL_CATEGORIAS.forEach(function(cat) {
        html += '<a href="#" class="sidebar-item" data-categoria="' + cat.nombre + '">' +
            '<span class="si-icon">' + (cat.icono || '📁') + '</span>' +
            '<span class="si-label">' + cat.nombre + '</span></a>';
    });
    container.innerHTML = html;

    // Click handler para categorías
    container.querySelectorAll('[data-categoria]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            var catName = this.getAttribute('data-categoria');
            // Marcar activo
            document.querySelectorAll('.sidebar-item').forEach(function(s) { s.classList.remove('active'); });
            this.classList.add('active');
            // Cargar videos de esta categoría
            loadVideos('api.php?action=videos&categoria=' + encodeURIComponent(catName));
        });
    });
}

function buildSidebar() {
    buildSidebarCategorias();
    var container = document.getElementById('sidebar-channels');
    var html = '';
    ALL_CHANNELS.forEach(function(c) {
        // Count playlists for this channel
        var chPlaylists = ALL_PLAYLISTS.filter(function(p) { return String(p.canal_id) === String(c.id) && parseInt(p.total_videos) > 0; });
        var hasPlaylists = chPlaylists.length > 0;

        html += '<div class="sidebar-channel-group">' +
            '<a href="#" class="sidebar-item" data-canal="' + c.id + '" title="' + c.nombre + '">' +
                '<span class="si-icon" style="color:' + c.color + '">●</span>' +
                '<span class="si-label">' + c.nombre + '</span>' +
                (hasPlaylists ? '<span class="si-expand" data-toggle="ch-' + c.id + '">▸</span>' : '') +
            '</a>' +
            '<div class="sidebar-playlists-group" id="ch-' + c.id + '" style="display:none;">';
        chPlaylists.forEach(function(p) {
            html += '<a href="#" class="sidebar-item" data-playlist="' + p.id + '" title="' + p.nombre + '" style="padding-left:2.5rem;font-size:0.82rem;">' +
                '<span class="si-icon" style="font-size:0.85rem;">📋</span>' +
                '<span class="si-label">' + p.nombre + '</span>' +
                '<span class="si-badge">' + p.total_videos + '</span></a>';
        });
        html += '</div></div>';
    });
    // Playlists sin canal
    ALL_PLAYLISTS.forEach(function(p) {
        if (!p.canal_id && parseInt(p.total_videos) > 0) {
            html += '<a href="#" class="sidebar-item" data-playlist="' + p.id + '" title="' + p.nombre + '">' +
                '<span class="si-icon">📋</span>' +
                '<span class="si-label">' + p.nombre + '</span>' +
                '<span class="si-badge">' + p.total_videos + '</span></a>';
        }
    });
    container.innerHTML = html;

    // Expand/collapse toggles
    container.querySelectorAll('.si-expand').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var targetId = this.getAttribute('data-toggle');
            var target = document.getElementById(targetId);
            var isOpen = target.style.display !== 'none';
            target.style.display = isOpen ? 'none' : '';
            this.textContent = isOpen ? '▸' : '▾';
        });
    });

    container.querySelectorAll('.sidebar-item[data-canal]').forEach(function(s) {
        s.addEventListener('click', function(e) { e.preventDefault(); filterCanal(this.getAttribute('data-canal')); closeSidebar(); });
    });
    container.querySelectorAll('.sidebar-item[data-playlist]').forEach(function(s) {
        s.addEventListener('click', function(e) { e.preventDefault(); filterPlaylist(this.getAttribute('data-playlist')); closeSidebar(); });
    });
}

function buildChips() {
    var container = document.getElementById('channel-chips');
    var html = '';
    ALL_CHANNELS.forEach(function(c) {
        html += '<button class="chip" data-canal="' + c.id + '">' + c.nombre + '</button>';
    });
    container.innerHTML = html;
    container.querySelectorAll('.chip').forEach(function(c) {
        c.addEventListener('click', function() { filterCanal(this.getAttribute('data-canal')); });
    });
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
function renderGrid(videos) {
    var list = videos || ALL_VIDEOS;
    var grid = document.getElementById('video-grid');
    var html = '';
    list.forEach(function(v) {
        var isWL = isInStore('watchlater', v.youtube_id);
        html += '<div class="video-card" data-id="' + v.youtube_id + '">' +
            '<a href="watch?v=' + v.youtube_id + '" class="thumb">' +
                '<img src="https://img.youtube.com/vi/' + v.youtube_id + '/mqdefault.jpg" alt="" loading="lazy">' +
                (v.duracion ? '<span class="duration-badge">' + v.duracion + '</span>' : '') +
                '<div class="thumb-actions">' +
                    '<button class="thumb-action-btn btn-wl' + (isWL ? ' saved' : '') + '" data-id="' + v.youtube_id + '" title="Ver después">🕐</button>' +
                '</div>' +
            '</a>' +
            '<div class="card-info">' +
                '<div class="channel-avatar" style="background:' + (v.canal_color || '#2e8b47') + '">' + (v.canal_codigo || '?') + '</div>' +
                '<div class="card-text">' +
                    '<a href="watch?v=' + v.youtube_id + '" class="card-title">' + v.titulo + '</a>' +
                    '<div class="card-channel">' + (v.canal_nombre || '') + '</div>' +
                    '<div class="card-stats">' + formatViews(v.vistas_yt) + ' reproducciones · ' + timeAgo(v.fecha_yt) + '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    });
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No se encontraron videos</p>';
    grid.querySelectorAll('.btn-wl').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); e.stopPropagation();
            var added = toggleStore('watchlater', this.getAttribute('data-id'));
            this.classList.toggle('saved', added);
            showToast(added ? 'Agregado a Ver después' : 'Quitado de Ver después');
            updateBadges();
        });
    });
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
    // Cargar todos los videos de este canal
    loadVideos('api.php?action=videos&canal_id=' + canalId);

    // Find channel info
    var canal = null;
    ALL_CHANNELS.forEach(function(c) { if (String(c.id) === String(canalId)) canal = c; });
    if (!canal) return;

    // Get playlists for this channel
    var chPlaylists = ALL_PLAYLISTS.filter(function(p) { return String(p.canal_id) === String(canalId) && parseInt(p.total_videos) > 0; });
    var totalVideos = ALL_VIDEOS.filter(function(v) { return String(v.canal_id) === String(canalId); }).length;

    var grid = document.getElementById('video-grid');
    var html = '';

    // Channel header
    html += '<div class="channel-header">' +
        '<div class="channel-header-avatar" style="background:' + canal.color + '">' + canal.codigo + '</div>' +
        '<div class="channel-header-info">' +
            '<div class="channel-header-name">' + canal.nombre + '</div>' +
            '<div class="channel-header-stats">' + totalVideos + ' videos · ' + chPlaylists.length + ' listas</div>' +
        '</div>' +
        '<button class="btn-all-videos" data-canal="' + canalId + '">Ver todos los videos</button>' +
    '</div>';

    // Playlists as cards
    if (chPlaylists.length > 0) {
        html += '<div class="playlist-grid">';
        chPlaylists.forEach(function(p) {
            html += '<a href="#" class="playlist-card" data-playlist="' + p.id + '">' +
                '<div class="playlist-card-icon">📋</div>' +
                '<div class="playlist-card-info">' +
                    '<div class="playlist-card-name">' + p.nombre + '</div>' +
                    '<div class="playlist-card-count">' + p.total_videos + ' videos</div>' +
                '</div>' +
            '</a>';
        });
        html += '</div>';
    }

    grid.innerHTML = html;

    // Bind events
    grid.querySelectorAll('.playlist-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            filterPlaylist(this.getAttribute('data-playlist'));
        });
    });

    var btnAll = grid.querySelector('.btn-all-videos');
    if (btnAll) {
        btnAll.addEventListener('click', function() {
            var cid = this.getAttribute('data-canal');
            showVideosWithSort(ALL_VIDEOS.filter(function(v) { return String(v.canal_id) === String(cid); }));
        });
    }
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

function showVideosWithSort(videos, activeSort) {
    activeSort = activeSort || 'newest';
    var grid = document.getElementById('video-grid');
    var sortBar = '<div class="sort-bar">' +
        '<span class="sort-label">' + videos.length + ' videos · Ordenar por:</span>' +
        '<button class="sort-btn' + (activeSort==='newest'?' active':'') + '" data-sort="newest">Más recientes</button>' +
        '<button class="sort-btn' + (activeSort==='popular'?' active':'') + '" data-sort="popular">Más vistos</button>' +
        '<button class="sort-btn' + (activeSort==='oldest'?' active':'') + '" data-sort="oldest">Más antiguos</button>' +
        '<button class="sort-btn' + (activeSort==='az'?' active':'') + '" data-sort="az">A-Z</button>' +
    '</div>';
    var sorted = sortVideos(videos, activeSort);
    // Temporarily render to get HTML
    renderGrid(sorted);
    grid.insertAdjacentHTML('afterbegin', sortBar);

    grid.querySelectorAll('.sort-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            showVideosWithSort(videos, this.getAttribute('data-sort'));
        });
    });
}

function filterSpecial(type) {
    clearAllActive();
    var chip = document.querySelector('.chip[data-filter="' + type + '"]');
    if (chip) chip.classList.add('active');
    var list = getStore(type);
    showVideosWithSort(ALL_VIDEOS.filter(function(v) { return list.indexOf(v.youtube_id) > -1; }));
}

function doSearch(q) {
    q = q.trim();
    if (!q) { renderGrid(); return; }
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
    renderGrid();
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
