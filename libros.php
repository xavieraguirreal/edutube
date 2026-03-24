<?php
$title = 'Libros — EduTube';
$description = 'Libros clásicos de dominio público en EduTube.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        img-src 'self' https://archive.org https://*.us.archive.org https://*.archive.org https://img.youtube.com https://www.gutenberg.org https://*.gutenberg.org;
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
            <input type="text" class="search-input" id="search" placeholder="Buscar libros...">
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

<div class="mobile-search-overlay" id="mobile-search-overlay">
    <button class="icon-btn" id="mobile-search-close">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
    </button>
    <input type="text" class="search-input" id="mobile-search-input" placeholder="Buscar libros...">
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
        <a href="audiolibros" class="sidebar-item">
            <span class="si-icon">📖</span><span class="si-label">Audiolibros <span id="cnt-audiolibros" style="color:var(--text-muted);font-size:0.8em;"></span></span>
        </a>
        <a href="libros" class="sidebar-item active">
            <span class="si-icon">📚</span><span class="si-label">Libros <span id="cnt-libros" style="color:var(--text-muted);font-size:0.8em;"></span></span>
        </a>
        <a href="novedades" class="sidebar-item">
            <span class="si-icon">🆕</span><span class="si-label">Novedades</span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Tu actividad</div>
        <a href="#" class="sidebar-item" id="nav-history">
            <span class="si-icon">⏱️</span><span class="si-label">Historial</span>
            <span class="si-badge" id="history-count" style="display:none">0</span>
        </a>
        <a href="#" class="sidebar-item" id="nav-watchlater">
            <span class="si-icon">🕐</span><span class="si-label">Leer después</span>
            <span class="si-badge" id="watchlater-count" style="display:none">0</span>
        </a>
        <a href="#" class="sidebar-item" id="nav-liked">
            <span class="si-icon">👍</span><span class="si-label">Me gusta</span>
            <span class="si-badge" id="liked-count" style="display:none">0</span>
        </a>
    </div>
    <div class="sidebar-section">
        <a href="#" class="sidebar-item" id="nav-sugerencia" onclick="document.getElementById('modal-sugerencia').style.display='flex';return false;">
            <span class="si-icon">💡</span><span class="si-label">Sugerir contenido</span>
        </a>
    </div>
    <div class="sidebar-footer">
        <strong>EduTube</strong> — Plataforma Educativa<br>
        Fuente: Proyecto Gutenberg (dominio público)<br>
        <a href="https://comite.cooperativaliberte.coop/" target="_blank" style="color:inherit;text-decoration:underline;">Comité de Convivencia Mario Juliano</a> &copy; 2026
    </div>
</nav>
<!-- Modal sugerencia -->
<div id="modal-sugerencia" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:#fff;border-radius:16px;padding:2rem;max-width:480px;width:100%;box-shadow:0 8px 30px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom:1rem;font-size:1.1rem;">Sugerir contenido</h3>
        <p style="font-size:0.85rem;color:#888;margin-bottom:1rem;">Sugerí un canal, un tema, contenido, o cualquier mejora para EduTube.</p>
        <div style="margin-bottom:0.75rem;">
            <select id="sug-tipo" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:8px;font-size:0.9rem;font-family:inherit;">
                <option value="canal">Canal de YouTube</option>
                <option value="tema">Tema o materia</option>
                <option value="contenido">Película / Libro / Audiolibro</option>
                <option value="mejora">Mejora de la plataforma</option>
                <option value="otro">Otro</option>
            </select>
        </div>
        <div style="margin-bottom:0.75rem;">
            <textarea id="sug-texto" rows="3" placeholder="Escribí tu sugerencia..." style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:8px;font-size:0.9rem;font-family:inherit;resize:vertical;"></textarea>
        </div>
        <div style="margin-bottom:0.75rem;display:flex;gap:0.5rem;">
            <input type="text" id="sug-nombre" placeholder="Tu nombre (opcional)" style="flex:1;padding:0.5rem;border:1px solid #ddd;border-radius:8px;font-size:0.85rem;font-family:inherit;">
            <input type="email" id="sug-email" placeholder="Email (opcional, para respuesta)" style="flex:1;padding:0.5rem;border:1px solid #ddd;border-radius:8px;font-size:0.85rem;font-family:inherit;">
        </div>
        <div style="display:flex;gap:0.5rem;justify-content:flex-end;">
            <button onclick="document.getElementById('modal-sugerencia').style.display='none';" style="padding:0.5rem 1rem;border:1px solid #ddd;border-radius:8px;background:none;cursor:pointer;font-family:inherit;">Cancelar</button>
            <button id="sug-enviar" onclick="enviarSugerencia()" style="padding:0.5rem 1rem;border:none;border-radius:8px;background:#2e8b47;color:#fff;cursor:pointer;font-family:inherit;font-weight:500;">Enviar</button>
        </div>
        <div id="sug-msg" style="margin-top:0.75rem;font-size:0.85rem;display:none;"></div>
    </div>
</div>
<script>
function enviarSugerencia() {
    var tipo = document.getElementById('sug-tipo').value;
    var texto = document.getElementById('sug-texto').value.trim();
    var msg = document.getElementById('sug-msg');
    var btn = document.getElementById('sug-enviar');
    if (!texto) { msg.style.display=''; msg.style.color='#c00'; msg.textContent='Escribí algo'; return; }
    btn.disabled = true; btn.textContent = 'Enviando...';
    var fd = new FormData();
    fd.append('tipo', tipo);
    fd.append('texto', texto);
    fd.append('nombre', (document.getElementById('sug-nombre').value || '').trim());
    fd.append('email', (document.getElementById('sug-email').value || '').trim());
    fetch('api.php?action=sugerencia', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(d){
            btn.disabled = false; btn.textContent = 'Enviar';
            if (d.error) { msg.style.display=''; msg.style.color='#c00'; msg.textContent=d.error; }
            else {
                msg.style.display=''; msg.style.color='#2e8b47'; msg.textContent='Gracias por tu sugerencia.';
                document.getElementById('sug-texto').value = '';
                setTimeout(function(){ document.getElementById('modal-sugerencia').style.display='none'; msg.style.display='none'; }, 2000);
            }
        })
        .catch(function(){ btn.disabled=false; btn.textContent='Enviar'; msg.style.display=''; msg.style.color='#c00'; msg.textContent='Error al enviar'; });
}
</script>

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
    <a href="libros" class="bottom-nav-item active">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
        Libros
    </a>
</nav>

<div class="toast" id="toast"></div>

<script>
var toastTimer;
function showToast(msg) {
    var t = document.getElementById('toast'); t.textContent = msg; t.classList.add('show');
    clearTimeout(toastTimer); toastTimer = setTimeout(function() { t.classList.remove('show'); }, 2500);
}

// Sidebar
var sidebar = document.getElementById('sidebar');
var backdrop = document.getElementById('sidebar-backdrop');
document.getElementById('menu-toggle').addEventListener('click', function() { sidebar.classList.toggle('open'); backdrop.classList.toggle('open'); });
backdrop.addEventListener('click', function() { sidebar.classList.remove('open'); backdrop.classList.remove('open'); });
function closeSidebar() { sidebar.classList.remove('open'); backdrop.classList.remove('open'); }

// Mobile search
document.getElementById('mobile-search-toggle').addEventListener('click', function() { document.getElementById('mobile-search-overlay').classList.add('open'); document.getElementById('mobile-search-input').focus(); });
document.getElementById('mobile-search-close').addEventListener('click', function() { document.getElementById('mobile-search-overlay').classList.remove('open'); });

// Catalog
var catalogo = [];
var generos = [];

function buildGenreUI() {
    generos = [];
    catalogo.forEach(function(p) { if (p.genero && generos.indexOf(p.genero) === -1) generos.push(p.genero); });
    generos.sort();
    var chipsDiv = document.getElementById('chips');
    chipsDiv.innerHTML = '<button class="chip active" data-genero="todos">Todos</button>';
    generos.forEach(function(g) {
        var btn = document.createElement('button');
        btn.className = 'chip'; btn.setAttribute('data-genero', g); btn.textContent = g;
        chipsDiv.appendChild(btn);
    });
    document.querySelectorAll('.chip').forEach(function(c) {
        c.addEventListener('click', function() { renderGrid(this.getAttribute('data-genero')); });
    });
}

var activeGenero = 'todos';

function cardHTML(p) {
    var thumbUrl = p.url_portada;
    if (!thumbUrl && p.ia_id && p.ia_id.indexOf('gutenberg_') === 0) {
        var gId = p.ia_id.replace('gutenberg_', '');
        thumbUrl = 'https://www.gutenberg.org/cache/epub/' + gId + '/pg' + gId + '.cover.medium.jpg';
    }
    if (!thumbUrl) thumbUrl = 'img/card-libros.jpg';
    return '<div class="video-card">' +
        '<a href="leer?id=' + p.id + '" class="thumb" style="background:#f5f0e8;">' +
            '<img src="' + thumbUrl + '" alt="" loading="lazy" style="object-fit:contain;height:100%;">' +
        '</a>' +
        '<div class="card-info">' +
            '<div class="channel-avatar" style="background:#0077b6;font-size:0.65rem;">📚</div>' +
            '<div class="card-text">' +
                '<a href="leer?id=' + p.id + '" class="card-title">' + p.titulo + '</a>' +
                '<div class="card-channel-static">' + (p.director || '') + '</div>' +
                '<div class="card-stats">' + (p.genero || 'Libro') + (p.year ? ' · ' + p.year : '') + '</div>' +
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
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No se encontraron libros</p>';
    document.querySelectorAll('.chip').forEach(function(c) { c.classList.toggle('active', c.getAttribute('data-genero') === activeGenero); });
}

function doSearch(q) {
    q = q.toLowerCase().trim();
    if (!q) { renderGrid(activeGenero); return; }
    var results = catalogo.filter(function(p) {
        return p.titulo.toLowerCase().indexOf(q) > -1 || (p.director || '').toLowerCase().indexOf(q) > -1 || (p.genero || '').toLowerCase().indexOf(q) > -1;
    });
    var grid = document.getElementById('video-grid');
    grid.style.display = '';
    var html = '';
    results.forEach(function(p) { html += cardHTML(p); });
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">Sin resultados para "' + q + '"</p>';
}

document.getElementById('search-btn').addEventListener('click', function() { doSearch(document.getElementById('search').value); });
document.getElementById('search').addEventListener('keydown', function(e) { if (e.key === 'Enter') doSearch(this.value); });
document.getElementById('mobile-search-input').addEventListener('keydown', function(e) { if (e.key === 'Enter') { doSearch(this.value); document.getElementById('mobile-search-overlay').classList.remove('open'); } });

// Activity
function getStore(key) { try { return JSON.parse(localStorage.getItem('edutube_' + key)) || []; } catch(e) { return []; } }
function updateBadges() {
    var counts = {history: getStore('history').length, watchlater: getStore('watchlater').length, liked: getStore('liked').length};
    ['history','watchlater','liked'].forEach(function(k) {
        var el = document.getElementById(k + '-count');
        if (el) { el.textContent = counts[k]; el.style.display = counts[k] > 0 ? '' : 'none'; }
    });
}
updateBadges();

// Total count
fetch('api.php?action=total_titulos').then(function(r){return r.json();}).then(function(d){
    document.getElementById('total-count').textContent = d.total + ' títulos';
    var ids = {videos:'cnt-videos',cine:'cnt-cine',audiolibros:'cnt-audiolibros',libros:'cnt-libros'};
    for (var k in ids) { var el = document.getElementById(ids[k]); if (el) el.textContent = '(' + (d[k]||0) + ')'; }
}).catch(function(){});

// Load books
fetch('api.php?action=contenido_ia&seccion=libros')
    .then(function(r) { return r.json(); })
    .then(function(data) {
        catalogo = data;
        buildGenreUI();
        renderGrid('todos');
    })
    .catch(function() {
        document.getElementById('video-grid').innerHTML = '<p style="color:var(--text-muted);padding:2rem;text-align:center;">Error al cargar libros</p>';
    });
</script>
</body>
</html>
