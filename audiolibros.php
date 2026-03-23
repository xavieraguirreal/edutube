<?php
$title = 'Audiolibros — EduTube';
$description = 'Audiolibros de dominio público en EduTube.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        img-src 'self' https://archive.org https://*.us.archive.org https://*.archive.org https://img.youtube.com;
        style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
        font-src https://fonts.gstatic.com;
        script-src 'self' 'unsafe-inline';
    ">
    <title><?php echo $title; ?></title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">
    <meta name="description" content="<?php echo $description; ?>">
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
            <span class="logo-count" id="total-count"></span>
        </a>
    </div>
    <div class="topbar-center">
        <div class="search-form">
            <input type="text" class="search-input" id="search" placeholder="Buscar audiolibros...">
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
    <input type="text" class="search-input" id="mobile-search-input" placeholder="Buscar audiolibros...">
</div>

<!-- ── SIDEBAR ── -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-section">
        <a href="/" class="sidebar-item">
            <span class="si-icon">🏠</span><span class="si-label">Inicio</span>
        </a>
        <a href="videos" class="sidebar-item">
            <span class="si-icon">📺</span><span class="si-label">Videos <span id="cnt-videos" style="color:var(--text-muted);font-size:0.8em;"></span></span>
        </a>
        <a href="cine" class="sidebar-item">
            <span class="si-icon">🎬</span><span class="si-label">Cine <span id="cnt-cine" style="color:var(--text-muted);font-size:0.8em;"></span></span>
        </a>
        <a href="audiolibros" class="sidebar-item active">
            <span class="si-icon">📖</span><span class="si-label">Audiolibros <span id="cnt-audiolibros" style="color:var(--text-muted);font-size:0.8em;"></span></span>
        </a>
        <a href="libros" class="sidebar-item">
            <span class="si-icon">📚</span><span class="si-label">Libros <span id="cnt-libros" style="color:var(--text-muted);font-size:0.8em;"></span></span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Géneros</div>
        <div id="sidebar-generos"></div>
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
            <span class="si-icon">👍</span><span class="si-label">Me gusta</span>
            <span class="si-badge" id="liked-count" style="display:none">0</span>
        </a>
    </div>
    <div class="sidebar-footer">
        <strong>EduTube</strong> — Plataforma de Videos Educativos<br>
        Fuente: Internet Archive (dominio público)<br>
        Comité de Convivencia Mario Juliano &copy; 2026
    </div>
</nav>

<!-- ── MAIN ── -->
<main class="main" id="main-content">
    <div class="chips" id="chips">
        <button class="chip active" data-genero="todos">Todos</button>
    </div>
    <div class="video-grid" id="video-grid" style="display:block;"></div>
</main>

<!-- ── BOTTOM NAV (mobile) ── -->
<nav class="bottom-nav">
    <a href="/" class="bottom-nav-item">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        Inicio
    </a>
    <a href="videos" class="bottom-nav-item">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/></svg>
        Videos
    </a>
    <a href="cine" class="bottom-nav-item">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
        Cine
    </a>
    <a href="audiolibros" class="bottom-nav-item active">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
        Audiolibros
    </a>
</nav>

<div class="toast" id="toast"></div>

<script>
var toastTimer;
function showToast(msg) {
    var t = document.getElementById('toast'); t.textContent = msg; t.classList.add('show');
    clearTimeout(toastTimer); toastTimer = setTimeout(function() { t.classList.remove('show'); }, 2500);
}

// ── Sidebar toggle ──
var sidebar = document.getElementById('sidebar');
var backdrop = document.getElementById('sidebar-backdrop');
document.getElementById('menu-toggle').addEventListener('click', function() {
    sidebar.classList.toggle('open');
    backdrop.classList.toggle('open');
});
backdrop.addEventListener('click', function() {
    sidebar.classList.remove('open');
    backdrop.classList.remove('open');
});
function closeSidebar() { sidebar.classList.remove('open'); backdrop.classList.remove('open'); }

// Mobile search
document.getElementById('mobile-search-toggle').addEventListener('click', function() {
    document.getElementById('mobile-search-overlay').classList.add('open');
    document.getElementById('mobile-search-input').focus();
});
document.getElementById('mobile-search-close').addEventListener('click', function() {
    document.getElementById('mobile-search-overlay').classList.remove('open');
});

// ── Catálogo ──
var catalogo = [];
var generos = [];

function buildGenreUI() {
    generos = [];
    catalogo.forEach(function(p) {
        if (generos.indexOf(p.genero) === -1) generos.push(p.genero);
    });
    generos.sort();

    var sidebarGen = document.getElementById('sidebar-generos');
    var sidebarHtml = '';
    generos.forEach(function(g) {
        var count = catalogo.filter(function(p) { return p.genero === g; }).length;
        sidebarHtml += '<a href="#" class="sidebar-item sidebar-genero" data-genero="' + g + '">' +
            '<span class="si-icon">•</span><span class="si-label">' + g + '</span>' +
            '<span class="si-badge">' + count + '</span>' +
        '</a>';
    });
    sidebarGen.innerHTML = sidebarHtml;

    var chipsDiv = document.getElementById('chips');
    chipsDiv.innerHTML = '<button class="chip active" data-genero="todos">Todos</button>';
    generos.forEach(function(g) {
        var btn = document.createElement('button');
        btn.className = 'chip';
        btn.setAttribute('data-genero', g);
        btn.textContent = g;
        chipsDiv.appendChild(btn);
    });

    document.querySelectorAll('.chip').forEach(function(c) {
        c.addEventListener('click', function() { renderGrid(this.getAttribute('data-genero')); });
    });
    document.querySelectorAll('.sidebar-genero').forEach(function(s) {
        s.addEventListener('click', function(e) {
            e.preventDefault(); renderGrid(this.getAttribute('data-genero')); closeSidebar();
        });
    });
}

var activeGenero = 'todos';

function cardHTML(p) {
    var thumbUrl = 'https://archive.org/download/' + p.ia_id + '/__ia_thumb.jpg';
    return '<div class="video-card">' +
        '<a href="watch?v=' + p.id + '" class="thumb">' +
            '<img src="' + thumbUrl + '" alt="" loading="lazy">' +
            (p.duracion ? '<span class="duration-badge">' + p.duracion + '</span>' : '') +
        '</a>' +
        '<div class="card-info">' +
            '<div class="channel-avatar" style="background:#6a4c93;font-size:0.65rem;">📖</div>' +
            '<div class="card-text">' +
                '<a href="watch?v=' + p.id + '" class="card-title">' + p.titulo + '</a>' +
                '<div class="card-channel-static">' + (p.director || '') + '</div>' +
                '<div class="card-stats">' + p.genero + (p.year ? ' · ' + p.year : '') + '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
}

function renderGrid(genero) {
    activeGenero = genero || 'todos';
    var filtered = activeGenero === 'todos' ? catalogo : catalogo.filter(function(p) { return p.genero === activeGenero; });
    var grid = document.getElementById('video-grid');
    grid.style.display = '';
    var html = '';
    filtered.forEach(function(p) { html += cardHTML(p); });
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No se encontraron audiolibros</p>';

    document.querySelectorAll('.chip').forEach(function(c) {
        c.classList.toggle('active', c.getAttribute('data-genero') === activeGenero);
    });
    document.querySelectorAll('.sidebar-genero').forEach(function(s) {
        s.classList.toggle('active', s.getAttribute('data-genero') === activeGenero);
    });
}

// Search
function doSearch(q) {
    q = q.toLowerCase().trim();
    if (!q) { renderGrid(activeGenero); return; }
    var results = catalogo.filter(function(p) {
        return p.titulo.toLowerCase().indexOf(q) > -1 ||
               (p.director || '').toLowerCase().indexOf(q) > -1 ||
               p.genero.toLowerCase().indexOf(q) > -1;
    });
    var grid = document.getElementById('video-grid');
    grid.style.display = '';
    var html = '';
    results.forEach(function(p) { html += cardHTML(p); });
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">Sin resultados para "' + q + '"</p>';
}

document.getElementById('search-btn').addEventListener('click', function() { doSearch(document.getElementById('search').value); });
document.getElementById('search').addEventListener('keydown', function(e) { if (e.key === 'Enter') doSearch(this.value); });
document.getElementById('mobile-search-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { doSearch(this.value); document.getElementById('mobile-search-overlay').classList.remove('open'); }
});

// ── Activity ──
function getStore(key) { try { return JSON.parse(localStorage.getItem('edutube_' + key)) || []; } catch(e) { return []; } }
function updateBadges() {
    var counts = {history: getStore('history').length, watchlater: getStore('watchlater').length, liked: getStore('liked').length};
    ['history','watchlater','liked'].forEach(function(k) {
        var el = document.getElementById(k + '-count');
        if (el) { el.textContent = counts[k]; el.style.display = counts[k] > 0 ? '' : 'none'; }
    });
}

function filterActivity(type) {
    var list = getStore(type);
    var filtered = catalogo.filter(function(p) { return list.indexOf(p.id) > -1; });
    var grid = document.getElementById('video-grid');
    grid.style.display = '';
    var html = '';
    filtered.forEach(function(p) { html += cardHTML(p); });
    var labels = {history:'historial',watchlater:'reproducir después',liked:'me gusta'};
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No hay contenido en ' + (labels[type]||type) + '</p>';
    document.querySelectorAll('.chip').forEach(function(c) { c.classList.remove('active'); });
    document.querySelectorAll('.sidebar-genero').forEach(function(s) { s.classList.remove('active'); });
}

document.getElementById('nav-history').addEventListener('click', function(e) { e.preventDefault(); filterActivity('history'); closeSidebar(); });
document.getElementById('nav-watchlater').addEventListener('click', function(e) { e.preventDefault(); filterActivity('watchlater'); closeSidebar(); });
document.getElementById('nav-liked').addEventListener('click', function(e) { e.preventDefault(); filterActivity('liked'); closeSidebar(); });

updateBadges();

// ── Load total count (all sections) ──
fetch('api.php?action=total_titulos').then(function(r){return r.json();}).then(function(d){
    document.getElementById('total-count').textContent = d.total + ' títulos';
    document.getElementById('cnt-videos').textContent = '(' + d.videos + ')';
    document.getElementById('cnt-cine').textContent = '(' + d.cine + ')';
    document.getElementById('cnt-audiolibros').textContent = '(' + d.audiolibros + ')';
    var cl = document.getElementById('cnt-libros'); if (cl) cl.textContent = '(' + (d.libros||0) + ')';
}).catch(function(){});

// Load audiolibros from API
fetch('api.php?action=contenido_ia&seccion=audiolibros')
    .then(function(r) { return r.json(); })
    .then(function(data) {
        catalogo = data;
        buildGenreUI();
        renderGrid('todos');
    })
    .catch(function() {
        document.getElementById('video-grid').innerHTML = '<p style="color:var(--text-muted);padding:2rem;text-align:center;">Error al cargar audiolibros</p>';
    });
</script>

</body>
</html>
