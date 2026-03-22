<?php
$title = 'Películas — EduTube';
$description = 'Películas clásicas de dominio público en EduTube.';
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
        <a href="peliculas" class="logo">
            <img src="loguito-edutube.png" alt="EduTube" class="logo-icon">
            <span class="logo-text">EduTube</span>
            <span class="logo-count" id="movie-count"></span>
        </a>
    </div>
    <div class="topbar-center">
        <div class="search-form">
            <input type="text" class="search-input" id="search" placeholder="Buscar películas...">
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
    <input type="text" class="search-input" id="mobile-search-input" placeholder="Buscar películas...">
</div>

<!-- ── SIDEBAR ── -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-section">
        <a href="peliculas" class="sidebar-item active">
            <span class="si-icon">🎬</span><span class="si-label">Películas</span>
        </a>
        <a href="index.php" class="sidebar-item">
            <span class="si-icon">📺</span><span class="si-label">Videos</span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-title">Géneros</div>
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
        <button class="chip active" data-genero="todos">Todas</button>
    </div>
    <div class="video-grid" id="video-grid" style="display:block;"></div>
</main>

<!-- ── BOTTOM NAV (mobile) ── -->
<nav class="bottom-nav">
    <a href="peliculas" class="bottom-nav-item active">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
        Películas
    </a>
    <a href="index.php" class="bottom-nav-item">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        Videos
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

// ── Catálogo de películas ──
var peliculas = [
    { id:'ia:TheGreatDictator', ia_id:'OGrandeDitadorTheGreatDictatorCharlieChaplin1940', titulo:'The Great Dictator (1940)', director:'Charlie Chaplin', year:1940, duracion:'2:04:37', descargas:72797, genero:'Comedia' },
    { id:'ia:Nosferatu', ia_id:'Nosferatu_most_complete_version_93_mins', titulo:'Nosferatu (1922)', director:'F.W. Murnau', year:1922, duracion:'1:33:00', descargas:424808, genero:'Terror' },
    { id:'ia:PhantomOpera', ia_id:'ThePhantomoftheOpera', titulo:'The Phantom of the Opera (1925)', director:'Rupert Julian', year:1925, duracion:'1:33:00', descargas:615942, genero:'Terror' },
    { id:'ia:BattleshipPotemkin', ia_id:'BattleshipPotemkin', titulo:'El acorazado Potemkin (1925)', director:'Sergei Eisenstein', year:1925, duracion:'1:15:00', descargas:440634, genero:'Drama' },
    { id:'ia:Caligari', ia_id:'DasKabinettdesDoktorCaligariTheCabinetofDrCaligari', titulo:'El gabinete del Dr. Caligari (1919)', director:'Robert Wiene', year:1919, duracion:'1:16:00', descargas:525087, genero:'Terror' },
    { id:'ia:CyranoDBergerac', ia_id:'Cyrano_DeBergerac', titulo:'Cyrano de Bergerac (1950)', director:'Michael Gordon', year:1950, duracion:'1:52:00', descargas:487906, genero:'Drama' },
    { id:'ia:Frankenstein1910', ia_id:'FrankensteinfullMovie', titulo:'Frankenstein (1910)', director:'J. Searle Dawley', year:1910, duracion:'0:16:00', descargas:370404, genero:'Terror' },
    { id:'ia:GreatExpectations', ia_id:'GreatExpectations1946', titulo:'Great Expectations (1946)', director:'David Lean', year:1946, duracion:'1:58:00', descargas:432948, genero:'Drama' },
    { id:'ia:Scrooge1935', ia_id:'Scrooge_1935', titulo:'Scrooge (1935)', director:'Henry Edwards', year:1935, duracion:'1:18:00', descargas:301187, genero:'Drama' },
    { id:'ia:MarkOfZorro', ia_id:'markofzorro-1920', titulo:'The Mark of Zorro (1920)', director:'Fred Niblo', year:1920, duracion:'1:30:00', descargas:333047, genero:'Aventura' },
    { id:'ia:HisGirlFriday', ia_id:'his_girl_friday', titulo:'His Girl Friday (1940)', director:'Howard Hawks', year:1940, duracion:'1:32:00', descargas:1288432, genero:'Comedia' },
    { id:'ia:SherlockHolmes', ia_id:'secret_weapon', titulo:'Sherlock Holmes and the Secret Weapon (1943)', director:'Roy William Neill', year:1943, duracion:'1:08:00', descargas:440894, genero:'Misterio' }
];

// Extract genres
var generos = [];
peliculas.forEach(function(p) {
    if (generos.indexOf(p.genero) === -1) generos.push(p.genero);
});
generos.sort();

// Build sidebar genres
var sidebarGen = document.getElementById('sidebar-generos');
var sidebarHtml = '';
generos.forEach(function(g) {
    var count = peliculas.filter(function(p) { return p.genero === g; }).length;
    sidebarHtml += '<a href="#" class="sidebar-item sidebar-genero" data-genero="' + g + '">' +
        '<span class="si-icon">•</span><span class="si-label">' + g + '</span>' +
        '<span class="si-badge">' + count + '</span>' +
    '</a>';
});
sidebarGen.innerHTML = sidebarHtml;

// Build chips for genres
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
            '<span class="duration-badge">' + p.duracion + '</span>' +
        '</a>' +
        '<div class="card-info">' +
            '<div class="channel-avatar" style="background:#e63946;font-size:0.65rem;">🎬</div>' +
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
    var filtered = activeGenero === 'todos' ? peliculas : peliculas.filter(function(p) { return p.genero === activeGenero; });
    var grid = document.getElementById('video-grid');
    grid.style.display = '';
    var html = '';
    filtered.forEach(function(p) { html += movieCardHTML(p); });
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No se encontraron películas</p>';

    // Update count
    document.getElementById('movie-count').textContent = filtered.length + ' películas';

    // Update active states
    document.querySelectorAll('.chip').forEach(function(c) {
        c.classList.toggle('active', c.getAttribute('data-genero') === activeGenero);
    });
    document.querySelectorAll('.sidebar-genero').forEach(function(s) {
        s.classList.toggle('active', s.getAttribute('data-genero') === activeGenero);
    });
    var todosChip = document.querySelector('.chip[data-genero="todos"]');
    if (todosChip) todosChip.classList.toggle('active', activeGenero === 'todos');
}

// Bind chips
document.querySelectorAll('.chip').forEach(function(c) {
    c.addEventListener('click', function() {
        renderMovies(this.getAttribute('data-genero'));
    });
});

// Bind sidebar genres
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
    var results = peliculas.filter(function(p) {
        return p.titulo.toLowerCase().indexOf(q) > -1 ||
               p.director.toLowerCase().indexOf(q) > -1 ||
               p.genero.toLowerCase().indexOf(q) > -1;
    });
    var grid = document.getElementById('video-grid');
    grid.style.display = '';
    var html = '';
    results.forEach(function(p) { html += movieCardHTML(p); });
    grid.innerHTML = html || '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No se encontraron películas para "' + q + '"</p>';
    document.getElementById('movie-count').textContent = results.length + ' resultados';
}

document.getElementById('search-btn').addEventListener('click', function() { searchMovies(document.getElementById('search').value); });
document.getElementById('search').addEventListener('keydown', function(e) { if (e.key === 'Enter') searchMovies(this.value); });
document.getElementById('mobile-search-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { searchMovies(this.value); document.getElementById('mobile-search-overlay').classList.remove('open'); }
});

// Initial render
renderMovies('todos');
</script>

</body>
</html>
