<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTube — Videos Educativos</title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">

    <!-- SEO -->
    <meta name="description" content="EduTube: plataforma de videos educativos curados para contextos con restricciones de acceso a redes sociales. Sin comentarios, sin algoritmos. Solo educación.">
    <meta name="keywords" content="videos educativos, plataforma educativa, educación en contextos de encierro, Universidad Liberté">
    <meta name="author" content="Universidad Liberté">
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
        <div class="sidebar-title">Canales</div>
        <a href="#" class="sidebar-item" data-canal="liberte">
            <span class="si-icon">🟢</span><span class="si-label">Cooperativa Liberté</span>
        </a>
        <a href="#" class="sidebar-item" data-canal="infobae">
            <span class="si-icon">🔴</span><span class="si-label">Infobae</span>
        </a>
        <a href="#" class="sidebar-item" data-canal="aterciopelados">
            <span class="si-icon">🟣</span><span class="si-label">Aterciopelados</span>
        </a>
        <a href="#" class="sidebar-item" data-canal="a24">
            <span class="si-icon">🟠</span><span class="si-label">A24</span>
        </a>
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
        Universidad Liberté &copy; 2026
    </div>
</nav>

<!-- ── MAIN ── -->
<main class="main" id="main-content">
    <div class="chips" id="chips">
        <button class="chip active" data-cat="todos">Todos</button>
        <button class="chip" data-canal="liberte">Liberté</button>
        <button class="chip" data-canal="infobae">Infobae</button>
        <button class="chip" data-canal="aterciopelados">Aterciopelados</button>
        <button class="chip" data-canal="a24">A24</button>
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
    if (n >= 1000000) return (n/1000000).toFixed(1).replace('.0','') + ' M';
    if (n >= 1000) return (n/1000).toFixed(1).replace('.0','') + ' K';
    return n.toString();
}

// ── Render ──
function renderGrid(filterFn) {
    var grid = document.getElementById('video-grid');
    var html = '';
    Object.keys(VIDEOS).forEach(function(id) {
        var v = VIDEOS[id];
        if (filterFn && !filterFn(id, v)) return;
        var ch = CHANNELS[v.canal];
        var isWL = isInStore('watchlater', id);
        html += '<div class="video-card" data-id="' + id + '">' +
            '<a href="watch?v=' + id + '" class="thumb">' +
                '<img src="https://img.youtube.com/vi/' + id + '/mqdefault.jpg" alt="" loading="lazy">' +
                '<span class="duration-badge">' + v.duracion + '</span>' +
                '<div class="thumb-actions">' +
                    '<button class="thumb-action-btn btn-wl' + (isWL ? ' saved' : '') + '" data-id="' + id + '" title="Ver después">🕐</button>' +
                '</div>' +
            '</a>' +
            '<div class="card-info">' +
                '<div class="channel-avatar" style="background:' + ch.color + '">' + ch.code + '</div>' +
                '<div class="card-text">' +
                    '<a href="watch?v=' + id + '" class="card-title">' + v.titulo + '</a>' +
                    '<div class="card-channel">' + ch.nombre + '</div>' +
                    '<div class="card-stats">' + formatViews(v.vistas) + ' reproducciones · ' + timeAgo(v.fecha) + '</div>' +
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

function doSearch(q) {
    q = q.toLowerCase().trim();
    if (!q) { renderGrid(); return; }
    renderGrid(function(id, v) {
        var ch = CHANNELS[v.canal];
        return v.titulo.toLowerCase().includes(q) ||
               v.descripcion.toLowerCase().includes(q) ||
               v.categoria.toLowerCase().includes(q) ||
               ch.nombre.toLowerCase().includes(q) ||
               (v.tags && v.tags.some(function(t) { return t.includes(q); }));
    });
}

function clearAllActive() {
    document.querySelectorAll('.chip').forEach(function(c) { c.classList.remove('active'); });
    document.querySelectorAll('.sidebar-item').forEach(function(s) { s.classList.remove('active'); });
}

function filterCat(cat) {
    clearAllActive();
    document.querySelector('.chip[data-cat="' + cat + '"]').classList.add('active');
    var sideItem = document.querySelector('.sidebar-item[data-cat="' + cat + '"]');
    if (sideItem) sideItem.classList.add('active');
    if (cat === 'todos') renderGrid();
    else renderGrid(function(id, v) { return v.categoria === cat; });
}

function filterCanal(canal) {
    clearAllActive();
    var chip = document.querySelector('.chip[data-canal="' + canal + '"]');
    if (chip) chip.classList.add('active');
    var sideItem = document.querySelector('.sidebar-item[data-canal="' + canal + '"]');
    if (sideItem) sideItem.classList.add('active');
    renderGrid(function(id, v) { return v.canal === canal; });
}

function filterSpecial(type) {
    clearAllActive();
    var chip = document.querySelector('.chip[data-filter="' + type + '"]');
    if (chip) chip.classList.add('active');
    var list = getStore(type);
    renderGrid(function(id) { return list.indexOf(id) > -1; });
}

// ── Init ──
renderGrid();
updateBadges();

// Chips
document.querySelectorAll('.chip[data-cat]').forEach(function(c) {
    c.addEventListener('click', function() { filterCat(this.getAttribute('data-cat')); });
});
document.querySelectorAll('.chip[data-canal]').forEach(function(c) {
    c.addEventListener('click', function() { filterCanal(this.getAttribute('data-canal')); });
});
document.querySelectorAll('.chip[data-filter]').forEach(function(c) {
    c.addEventListener('click', function() { filterSpecial(this.getAttribute('data-filter')); });
});

// Sidebar
document.querySelectorAll('.sidebar-item[data-cat]').forEach(function(s) {
    s.addEventListener('click', function(e) { e.preventDefault(); filterCat(this.getAttribute('data-cat')); closeSidebar(); });
});
document.querySelectorAll('.sidebar-item[data-canal]').forEach(function(s) {
    s.addEventListener('click', function(e) { e.preventDefault(); filterCanal(this.getAttribute('data-canal')); closeSidebar(); });
});
document.getElementById('nav-history').addEventListener('click', function(e) { e.preventDefault(); filterSpecial('history'); closeSidebar(); });
document.getElementById('nav-watchlater').addEventListener('click', function(e) { e.preventDefault(); filterSpecial('watchlater'); closeSidebar(); });
document.getElementById('nav-liked').addEventListener('click', function(e) { e.preventDefault(); filterSpecial('liked'); closeSidebar(); });

// Search
document.getElementById('search').addEventListener('input', function() { doSearch(this.value); });

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
document.getElementById('mobile-search-input').addEventListener('input', function() { doSearch(this.value); });
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
