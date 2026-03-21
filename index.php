<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTube — Videos Educativos</title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>

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
        <!-- Mobile search toggle -->
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
    <input type="text" class="search-input" id="mobile-search-input" placeholder="Buscar videos..." autofocus>
</div>

<!-- ── SIDEBAR ── -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-section">
        <a href="index.php" class="sidebar-item active" data-cat="todos">
            <span class="si-icon">🏠</span>
            <span class="si-label">Inicio</span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Categorías</div>
        <a href="#" class="sidebar-item" data-cat="Cursos">
            <span class="si-icon">📖</span>
            <span class="si-label">Cursos</span>
        </a>
        <a href="#" class="sidebar-item" data-cat="Encuentros Liberté">
            <span class="si-icon">🎓</span>
            <span class="si-label">Encuentros Liberté</span>
        </a>
        <a href="#" class="sidebar-item" data-cat="Cambio de Paradigma">
            <span class="si-icon">🔄</span>
            <span class="si-label">Cambio de Paradigma</span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Tu actividad</div>
        <a href="#" class="sidebar-item" id="nav-history">
            <span class="si-icon">⏱️</span>
            <span class="si-label">Historial</span>
            <span class="si-badge" id="history-count" style="display:none">0</span>
        </a>
        <a href="#" class="sidebar-item" id="nav-watchlater">
            <span class="si-icon">🕐</span>
            <span class="si-label">Ver después</span>
            <span class="si-badge" id="watchlater-count" style="display:none">0</span>
        </a>
        <a href="#" class="sidebar-item" id="nav-liked">
            <span class="si-icon">👍</span>
            <span class="si-label">Videos que me gustan</span>
            <span class="si-badge" id="liked-count" style="display:none">0</span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Listas de reproducción</div>
        <a href="#" class="sidebar-item" data-playlist="mujeres-al">
            <span class="si-icon">📋</span>
            <span class="si-label">Mujeres en América Latina</span>
        </a>
        <a href="#" class="sidebar-item" data-playlist="cambio-paradigma">
            <span class="si-icon">📋</span>
            <span class="si-label">Cambio de Paradigma</span>
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
        <button class="chip" data-cat="Cursos">Cursos</button>
        <button class="chip" data-cat="Encuentros Liberté">Encuentros</button>
        <button class="chip" data-cat="Cambio de Paradigma">Cambio de Paradigma</button>
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

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
// ── Data ──
var VIDEOS = {
    'Xw2zGp-Fjr8': {
        titulo: 'Adultocentrismo y producción de subjetividad en contextos de exclusión',
        descripcion: 'Encuentro Liberté del 28/02/2026. Análisis sobre adultocentrismo y la producción de subjetividad en contextos de exclusión social.',
        categoria: 'Encuentros Liberté', catCode: 'EL',
        duracion: '2:13:18', vistas: 101, fecha: '2026-03-01',
        tags: ['adultocentrismo', 'subjetividad', 'exclusión', 'derechos']
    },
    'iTGbnWacFxw': {
        titulo: '5ta Clase — Mujeres Privadas de la Libertad en América Latina',
        descripcion: '5ta clase del 4to curso sobre Mujeres Privadas de la Libertad en América Latina.',
        categoria: 'Cursos', catCode: 'CL',
        duracion: '1:55:29', vistas: 163, fecha: '2026-02-21',
        tags: ['mujeres', 'libertad', 'latinoamérica', 'género', 'cárceles']
    },
    'bKnEOaW1hsc': {
        titulo: '4ta Clase — Mujeres Privadas de la Libertad en América Latina',
        descripcion: '4ta clase del 4to curso sobre Mujeres Privadas de la Libertad en América Latina.',
        categoria: 'Cursos', catCode: 'CL',
        duracion: '1:53:42', vistas: 147, fecha: '2026-02-20',
        tags: ['mujeres', 'libertad', 'latinoamérica', 'género', 'cárceles']
    },
    'hHYjl0R0F3I': {
        titulo: '3er Clase — Mujeres Privadas de la Libertad en América Latina',
        descripcion: '3er clase del 4to curso sobre Mujeres Privadas de la Libertad en América Latina.',
        categoria: 'Cursos', catCode: 'CL',
        duracion: '1:54:10', vistas: 180, fecha: '2026-02-19',
        tags: ['mujeres', 'libertad', 'latinoamérica', 'género', 'cárceles']
    },
    'T4XPU8vvviw': {
        titulo: '2da Clase — Mujeres Privadas de la Libertad en América Latina',
        descripcion: '2da clase del 4to curso sobre Mujeres Privadas de la Libertad en América Latina.',
        categoria: 'Cursos', catCode: 'CL',
        duracion: '1:49:17', vistas: 206, fecha: '2026-02-18',
        tags: ['mujeres', 'libertad', 'latinoamérica', 'género', 'cárceles']
    },
    '9-6TrW8JBWg': {
        titulo: '1er Clase — Mujeres Privadas de la Libertad en América Latina',
        descripcion: '1er clase del 4to curso sobre Mujeres Privadas de la Libertad en América Latina.',
        categoria: 'Cursos', catCode: 'CL',
        duracion: '1:48:40', vistas: 339, fecha: '2026-02-17',
        tags: ['mujeres', 'libertad', 'latinoamérica', 'género', 'cárceles']
    },
    'pqs4xltUE48': {
        titulo: 'Encuentro Liberté — 31/01/2026',
        descripcion: 'Encuentro Liberté del 31 de enero de 2026.',
        categoria: 'Encuentros Liberté', catCode: 'EL',
        duracion: '2:11:00', vistas: 124, fecha: '2026-02-01',
        tags: ['encuentro', 'liberté', 'debate']
    },
    'BRHahRFVmQ8': {
        titulo: 'Clase 15 — Cambio de Paradigma',
        descripcion: 'Clase 15 del curso Cambio de Paradigma. Sábado 13/12/2025.',
        categoria: 'Cambio de Paradigma', catCode: 'CP',
        duracion: '3:00:36', vistas: 236, fecha: '2025-12-14',
        tags: ['paradigma', 'cambio', 'sociedad', 'educación']
    },
    'ZbSTrUSRU-4': {
        titulo: 'Clase 14 — Salud Mental',
        descripcion: 'Clase 14 sobre Salud Mental. Sábado 29/11/2025.',
        categoria: 'Cambio de Paradigma', catCode: 'CP',
        duracion: '3:00:01', vistas: 396, fecha: '2025-11-30',
        tags: ['salud mental', 'bienestar', 'paradigma']
    },
    'hO0V0y_Yn2U': {
        titulo: 'Clase 9 — Última Jornada',
        descripcion: 'Clase 9, última jornada del curso.',
        categoria: 'Cambio de Paradigma', catCode: 'CP',
        duracion: '1:29:26', vistas: 107, fecha: '2025-11-29',
        tags: ['jornada', 'paradigma', 'cierre']
    }
};

// ── localStorage helpers ──
function getStore(key) {
    try { return JSON.parse(localStorage.getItem('edutube_' + key)) || []; }
    catch(e) { return []; }
}
function setStore(key, val) { localStorage.setItem('edutube_' + key, JSON.stringify(val)); }
function toggleStore(key, id) {
    var list = getStore(key);
    var i = list.indexOf(id);
    if (i > -1) { list.splice(i, 1); } else { list.push(id); }
    setStore(key, list);
    return i === -1; // true if added
}
function isInStore(key, id) { return getStore(key).indexOf(id) > -1; }

// ── Toast ──
var toastTimer;
function showToast(msg) {
    var t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function() { t.classList.remove('show'); }, 2500);
}

// ── Time ago ──
function timeAgo(dateStr) {
    var now = new Date();
    var date = new Date(dateStr);
    var diff = Math.floor((now - date) / 1000);
    if (diff < 60) return 'hace un momento';
    if (diff < 3600) return 'hace ' + Math.floor(diff/60) + ' min';
    if (diff < 86400) return 'hace ' + Math.floor(diff/3600) + ' h';
    var days = Math.floor(diff / 86400);
    if (days < 7) return 'hace ' + days + (days === 1 ? ' día' : ' días');
    if (days < 30) return 'hace ' + Math.floor(days/7) + (Math.floor(days/7) === 1 ? ' semana' : ' semanas');
    if (days < 365) return 'hace ' + Math.floor(days/30) + (Math.floor(days/30) === 1 ? ' mes' : ' meses');
    return 'hace ' + Math.floor(days/365) + (Math.floor(days/365) === 1 ? ' año' : ' años');
}

function formatViews(n) {
    if (n >= 1000000) return (n/1000000).toFixed(1).replace('.0','') + ' M';
    if (n >= 1000) return (n/1000).toFixed(1).replace('.0','') + ' K';
    return n.toString();
}

// ── Render Grid ──
function renderGrid(filterFn) {
    var grid = document.getElementById('video-grid');
    var html = '';
    var ids = Object.keys(VIDEOS);

    ids.forEach(function(id) {
        var v = VIDEOS[id];
        if (filterFn && !filterFn(id, v)) return;

        var isWL = isInStore('watchlater', id);

        html += '<div class="video-card" data-id="' + id + '" data-cat="' + v.categoria + '">' +
            '<a href="ver.php?id=' + id + '" class="thumb">' +
                '<img src="https://img.youtube.com/vi/' + id + '/mqdefault.jpg" alt="" loading="lazy">' +
                '<span class="duration-badge">' + v.duracion + '</span>' +
                '<div class="thumb-actions">' +
                    '<button class="thumb-action-btn btn-wl' + (isWL ? ' saved' : '') + '" data-id="' + id + '" title="Ver después">🕐</button>' +
                '</div>' +
            '</a>' +
            '<div class="card-info">' +
                '<div class="channel-avatar">' + v.catCode + '</div>' +
                '<div class="card-text">' +
                    '<a href="ver.php?id=' + id + '" class="card-title">' + v.titulo + '</a>' +
                    '<div class="card-channel">Cooperativa Liberté</div>' +
                    '<div class="card-stats">' + formatViews(v.vistas) + ' reproducciones · ' + timeAgo(v.fecha) + '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    });

    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No se encontraron videos</p>';

    // Watch later buttons
    grid.querySelectorAll('.btn-wl').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var vid = this.getAttribute('data-id');
            var added = toggleStore('watchlater', vid);
            this.classList.toggle('saved', added);
            showToast(added ? 'Agregado a Ver después' : 'Quitado de Ver después');
            updateBadges();
        });
    });
}

// ── Update sidebar badges ──
function updateBadges() {
    var h = getStore('history').length;
    var w = getStore('watchlater').length;
    var l = getStore('liked').length;
    var hBadge = document.getElementById('history-count');
    var wBadge = document.getElementById('watchlater-count');
    var lBadge = document.getElementById('liked-count');
    if (h > 0) { hBadge.textContent = h; hBadge.style.display = ''; } else { hBadge.style.display = 'none'; }
    if (w > 0) { wBadge.textContent = w; wBadge.style.display = ''; } else { wBadge.style.display = 'none'; }
    if (l > 0) { lBadge.textContent = l; lBadge.style.display = ''; } else { lBadge.style.display = 'none'; }
}

// ── Search ──
function doSearch(q) {
    q = q.toLowerCase().trim();
    if (!q) { renderGrid(); return; }
    renderGrid(function(id, v) {
        return v.titulo.toLowerCase().includes(q) ||
               v.descripcion.toLowerCase().includes(q) ||
               v.categoria.toLowerCase().includes(q) ||
               (v.tags && v.tags.some(function(t) { return t.includes(q); }));
    });
}

// ── Filter by category ──
var currentFilter = 'todos';
function filterCat(cat) {
    currentFilter = cat;
    document.querySelectorAll('.chip').forEach(function(c) {
        c.classList.toggle('active', c.getAttribute('data-cat') === cat);
    });
    document.querySelectorAll('[data-filter]').forEach(function(c) { c.classList.remove('active'); });
    document.querySelectorAll('.sidebar-item[data-cat]').forEach(function(s) {
        s.classList.toggle('active', s.getAttribute('data-cat') === cat);
    });
    if (cat === 'todos') renderGrid();
    else renderGrid(function(id, v) { return v.categoria === cat; });
}

function filterSpecial(type) {
    document.querySelectorAll('.chip').forEach(function(c) { c.classList.remove('active'); });
    document.querySelectorAll('.sidebar-item[data-cat]').forEach(function(s) { s.classList.remove('active'); });
    document.querySelectorAll('[data-filter]').forEach(function(c) {
        c.classList.toggle('active', c.getAttribute('data-filter') === type);
    });
    var list = getStore(type);
    renderGrid(function(id) { return list.indexOf(id) > -1; });
}

// ── Init ──
renderGrid();
updateBadges();

// Chips
document.querySelectorAll('.chip[data-cat]').forEach(function(chip) {
    chip.addEventListener('click', function() { filterCat(this.getAttribute('data-cat')); });
});

document.querySelectorAll('.chip[data-filter]').forEach(function(chip) {
    chip.addEventListener('click', function() { filterSpecial(this.getAttribute('data-filter')); });
});

// Sidebar category
document.querySelectorAll('.sidebar-item[data-cat]').forEach(function(item) {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        filterCat(this.getAttribute('data-cat'));
        closeSidebar();
    });
});

// Sidebar special filters
document.getElementById('nav-history').addEventListener('click', function(e) { e.preventDefault(); filterSpecial('history'); closeSidebar(); });
document.getElementById('nav-watchlater').addEventListener('click', function(e) { e.preventDefault(); filterSpecial('watchlater'); closeSidebar(); });
document.getElementById('nav-liked').addEventListener('click', function(e) { e.preventDefault(); filterSpecial('liked'); closeSidebar(); });

// Search
document.getElementById('search').addEventListener('input', function() { doSearch(this.value); });

// Mobile search
document.getElementById('mobile-search-toggle').addEventListener('click', function() {
    document.getElementById('mobile-search-overlay').classList.add('open');
    document.getElementById('mobile-search-input').focus();
});
document.getElementById('mobile-search-close').addEventListener('click', function() {
    document.getElementById('mobile-search-overlay').classList.remove('open');
    document.getElementById('mobile-search-input').value = '';
    renderGrid();
});
document.getElementById('mobile-search-input').addEventListener('input', function() { doSearch(this.value); });
document.getElementById('mobile-search-trigger').addEventListener('click', function() {
    document.getElementById('mobile-search-overlay').classList.add('open');
    document.getElementById('mobile-search-input').focus();
});

// Mobile bottom nav
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
    var sb = document.getElementById('sidebar');
    var bd = document.getElementById('sidebar-backdrop');
    sb.classList.toggle('open');
    bd.classList.toggle('open');
});

document.getElementById('sidebar-backdrop').addEventListener('click', closeSidebar);

// Show mobile search btn only on mobile
var mobileSearchBtn = document.getElementById('mobile-search-toggle');
function checkMobileSearch() {
    mobileSearchBtn.style.display = window.innerWidth <= 768 ? '' : 'none';
}
checkMobileSearch();
window.addEventListener('resize', checkMobileSearch);
</script>

</body>
</html>
