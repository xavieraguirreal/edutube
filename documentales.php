<?php
$title = 'Documentales — EduTube';
$description = 'Documentales educativos de dominio público en EduTube.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        img-src 'self' https://archive.org https://*.us.archive.org https://*.archive.org;
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
        <a href="documentales" class="logo">
            <img src="loguito-edutube.png" alt="EduTube" class="logo-icon">
            <span class="logo-text">EduTube</span>
            <span class="logo-count" id="movie-count"></span>
        </a>
    </div>
    <div class="topbar-center">
        <div class="search-form">
            <input type="text" class="search-input" id="search" placeholder="Buscar documentales...">
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
    <input type="text" class="search-input" id="mobile-search-input" placeholder="Buscar documentales...">
</div>

<!-- ── SIDEBAR ── -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-section">
        <a href="index.php" class="sidebar-item">
            <span class="si-icon">📺</span><span class="si-label">Videos</span>
        </a>
        <a href="peliculas" class="sidebar-item">
            <span class="si-icon">🎬</span><span class="si-label">Películas</span>
        </a>
        <a href="documentales" class="sidebar-item active">
            <span class="si-icon">🎞️</span><span class="si-label">Documentales</span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Temas</div>
        <div id="sidebar-generos"></div>
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
    <a href="documentales" class="bottom-nav-item active">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
        Documentales
    </a>
    <a href="index.php" class="bottom-nav-item">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        Videos
    </a>
    <a href="peliculas" class="bottom-nav-item">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
        Películas
    </a>
</nav>

<div class="toast" id="toast"></div>

<script>
var toastTimer;
function showToast(msg) {
    var t = document.getElementById('toast'); t.textContent = msg; t.classList.add('show');
    clearTimeout(toastTimer); toastTimer = setTimeout(function() { t.classList.remove('show'); }, 2500);
}

function formatViews(n) {
    if (n >= 1000000) return (n/1000000).toFixed(1).replace('.0','') + ' M';
    if (n >= 1000) return (n/1000).toFixed(1).replace('.0','') + ' K';
    return n;
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

// ── Catálogo de documentales ──
var documentales = [
    { id:'ia:TheInternetOwnBoy', ia_id:'TheInternetsOwnBoyTheStoryOfAaronSwartz', titulo:"The Internet's Own Boy: Aaron Swartz (2014)", director:'Brian Knappenberger', year:2014, duracion:'1:45:00', descargas:656208, genero:'Tecnología' },
    { id:'ia:Connections', ia_id:'ConnectionsByJamesBurke', titulo:'Connections (1978) — James Burke', director:'James Burke', year:1978, duracion:'', descargas:409892, genero:'Ciencia' },
    { id:'ia:ManWithMovieCamera', ia_id:'ChelovekskinoapparatomManWithAMovieCamera', titulo:'Man With a Movie Camera (1929)', director:'Dziga Vertov', year:1929, duracion:'1:08:00', descargas:256827, genero:'Arte' },
    { id:'ia:MemphisBelle', ia_id:'TheMemphisBelleAStoryofaFlyingFortress', titulo:'The Memphis Belle (1944)', director:'William Wyler', year:1944, duracion:'0:45:00', descargas:217076, genero:'Historia' },
    { id:'ia:TheLastBomb', ia_id:'TheLastBomb1945', titulo:'The Last Bomb (1945)', director:'Frank Lloyd', year:1945, duracion:'0:36:00', descargas:1129876, genero:'Historia' },
    { id:'ia:WorldAtWar', ia_id:'the-world-at-war-1973-thames-television-world-war-two', titulo:'The World at War (1973)', director:'Thames Television', year:1973, duracion:'', descargas:169449, genero:'Historia' },
    { id:'ia:GeniusPhotography', ia_id:'tGoPhoto', titulo:'The Genius of Photography (2007)', director:'BBC', year:2007, duracion:'', descargas:136122, genero:'Arte' },
    { id:'ia:TheCorporation', ia_id:'The_Corporation_', titulo:'The Corporation (2003)', director:'Mark Achbar & Jennifer Abbott', year:2003, duracion:'2:25:00', descargas:117378, genero:'Sociedad' },
    { id:'ia:WarPhotographer', ia_id:'wphoto', titulo:'War Photographer (2001) — James Nachtwey', director:'Christian Frei', year:2001, duracion:'1:36:00', descargas:110125, genero:'Arte' },
    { id:'ia:WalkingWithDinos', ia_id:'walking-with-dinosaurs', titulo:'Walking with Dinosaurs (1999)', director:'BBC', year:1999, duracion:'', descargas:89511, genero:'Ciencia' },
    { id:'ia:BattleOfMidway', ia_id:'the_battle_of_midway', titulo:'The Battle of Midway (1942)', director:'John Ford', year:1942, duracion:'0:18:00', descargas:87350, genero:'Historia' },
    { id:'ia:ManufacturingConsent', ia_id:'manufacturing_consent', titulo:'Manufacturing Consent: Noam Chomsky (1993)', director:'Mark Achbar & Peter Wintonick', year:1993, duracion:'2:47:00', descargas:147490, genero:'Sociedad' },
    { id:'ia:DeathMills', ia_id:'DeathMills', titulo:'Death Mills (1945)', director:'Hanuš Burger', year:1945, duracion:'0:22:00', descargas:76274, genero:'Historia' },
    { id:'ia:BBSDocumentary', ia_id:'BBS.The.Documentary', titulo:'BBS: The Documentary (2005)', director:'Jason Scott', year:2005, duracion:'', descargas:42635, genero:'Tecnología' }
];

// Extract genres
var generos = [];
documentales.forEach(function(p) {
    if (generos.indexOf(p.genero) === -1) generos.push(p.genero);
});
generos.sort();

// Build sidebar genres
var sidebarGen = document.getElementById('sidebar-generos');
var sidebarHtml = '';
generos.forEach(function(g) {
    var count = documentales.filter(function(p) { return p.genero === g; }).length;
    sidebarHtml += '<a href="#" class="sidebar-item sidebar-genero" data-genero="' + g + '">' +
        '<span class="si-icon">•</span><span class="si-label">' + g + '</span>' +
        '<span class="si-badge">' + count + '</span>' +
    '</a>';
});
sidebarGen.innerHTML = sidebarHtml;

// Build chips
var chipsDiv = document.getElementById('chips');
generos.forEach(function(g) {
    var btn = document.createElement('button');
    btn.className = 'chip';
    btn.setAttribute('data-genero', g);
    btn.textContent = g;
    chipsDiv.appendChild(btn);
});

// Render
var activeGenero = 'todos';

function movieCardHTML(p) {
    var thumbUrl = 'https://archive.org/download/' + p.ia_id + '/__ia_thumb.jpg';
    return '<div class="video-card">' +
        '<a href="watch?v=' + p.id + '" class="thumb">' +
            '<img src="' + thumbUrl + '" alt="" loading="lazy">' +
            (p.duracion ? '<span class="duration-badge">' + p.duracion + '</span>' : '') +
        '</a>' +
        '<div class="card-info">' +
            '<div class="channel-avatar" style="background:#0f3460;font-size:0.65rem;">🎞️</div>' +
            '<div class="card-text">' +
                '<a href="watch?v=' + p.id + '" class="card-title">' + p.titulo + '</a>' +
                '<div class="card-channel-static">' + p.director + '</div>' +
                '<div class="card-stats">' + formatViews(p.descargas) + ' descargas · ' + p.genero + ' · ' + p.year + '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
}

function renderMovies(genero) {
    activeGenero = genero || 'todos';
    var filtered = activeGenero === 'todos' ? documentales : documentales.filter(function(p) { return p.genero === activeGenero; });
    var grid = document.getElementById('video-grid');
    grid.style.display = '';
    var html = '';
    filtered.forEach(function(p) { html += movieCardHTML(p); });
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No se encontraron documentales</p>';
    document.getElementById('movie-count').textContent = filtered.length + ' documentales';

    document.querySelectorAll('.chip').forEach(function(c) {
        c.classList.toggle('active', c.getAttribute('data-genero') === activeGenero);
    });
    document.querySelectorAll('.sidebar-genero').forEach(function(s) {
        s.classList.toggle('active', s.getAttribute('data-genero') === activeGenero);
    });
}

// Bind chips
document.querySelectorAll('.chip').forEach(function(c) {
    c.addEventListener('click', function() { renderMovies(this.getAttribute('data-genero')); });
});

// Bind sidebar
document.querySelectorAll('.sidebar-genero').forEach(function(s) {
    s.addEventListener('click', function(e) {
        e.preventDefault();
        renderMovies(this.getAttribute('data-genero'));
        closeSidebar();
    });
});

// Search
function searchMovies(q) {
    q = q.toLowerCase().trim();
    if (!q) { renderMovies(activeGenero); return; }
    var results = documentales.filter(function(p) {
        return p.titulo.toLowerCase().indexOf(q) > -1 ||
               p.director.toLowerCase().indexOf(q) > -1 ||
               p.genero.toLowerCase().indexOf(q) > -1;
    });
    var grid = document.getElementById('video-grid');
    grid.style.display = '';
    var html = '';
    results.forEach(function(p) { html += movieCardHTML(p); });
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No se encontraron documentales para "' + q + '"</p>';
    document.getElementById('movie-count').textContent = results.length + ' resultados';
}

document.getElementById('search-btn').addEventListener('click', function() { searchMovies(document.getElementById('search').value); });
document.getElementById('search').addEventListener('keydown', function(e) { if (e.key === 'Enter') searchMovies(this.value); });
document.getElementById('mobile-search-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { searchMovies(this.value); document.getElementById('mobile-search-overlay').classList.remove('open'); }
});

renderMovies('todos');
</script>

</body>
</html>
