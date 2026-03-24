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
    <title>EduTube — Plataforma Educativa</title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">

    <!-- SEO -->
    <meta name="description" content="EduTube: plataforma de contenido educativo curado. Videos, cine y audiolibros. Sin comentarios, sin algoritmos. Solo educación.">
    <meta name="keywords" content="videos educativos, plataforma educativa, educación en contextos de encierro, Comité de Convivencia Mario Juliano">
    <meta name="author" content="Comité de Convivencia Mario Juliano">
    <link rel="canonical" href="https://edutube.universidadliberte.org/">

    <!-- Open Graph -->
    <meta property="og:title" content="EduTube — Plataforma Educativa">
    <meta property="og:description" content="Contenido audiovisual educativo curado. Videos, cine y audiolibros. Sin comentarios, sin algoritmos.">
    <meta property="og:image" content="https://edutube.universidadliberte.org/loguito-edutube.png">
    <meta property="og:url" content="https://edutube.universidadliberte.org">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="EduTube">
    <meta property="og:locale" content="es_AR">

    <link rel="stylesheet" href="style.css">
</head>
<body>
<script>
// Redirect first-time visitors to landing page
if (!localStorage.getItem('edutube_welcomed')) {
    window.location.replace('landing');
}
</script>

<style>
/* ── Portal section cards ── */
.portal-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.portal-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    height: 180px;
    cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    text-decoration: none;
    display: block;
}
.portal-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,.4);
}
.portal-card-bg {
    position: absolute; inset: 0;
    background-size: cover;
    background-position: center;
}
.portal-card-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(transparent 40%, rgba(0,0,0,0.8));
}
.portal-card-body {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 1.2rem;
    color: #fff;
}
.portal-card-name {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: .25rem;
}
.portal-card-count {
    display: inline-block;
    background: rgba(255,255,255,.2);
    padding: 2px 8px;
    border-radius: 10px;
    font-size: .75rem;
    font-weight: 600;
    margin-bottom: .3rem;
}
.portal-card-desc {
    font-size: .82rem;
    opacity: .85;
    line-height: 1.3;
}

/* ── Section separator ── */
.section-separator {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin: 1.5rem 0 1rem;
    padding: 0 .25rem;
}
.section-separator h2 {
    font-size: 1.15rem;
    font-weight: 600;
    color: var(--text-primary, #fff);
    margin: 0;
}
.section-separator::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border, rgba(255,255,255,.1));
}

/* ── Latest rows ── */
.latest-row {
    margin-bottom: 1.5rem;
}
.latest-row-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .75rem;
    padding: 0 .25rem;
}
.latest-row-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary, #fff);
}
.latest-row-link {
    font-size: .82rem;
    color: var(--accent, #3ea6ff);
    text-decoration: none;
}
.latest-row-link:hover {
    text-decoration: underline;
}
.latest-row-scroll {
    display: flex;
    gap: .75rem;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    padding-bottom: .5rem;
    -webkit-overflow-scrolling: touch;
}
.latest-row-scroll::-webkit-scrollbar { height: 6px; }
.latest-row-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 3px; }

/* ── Latest item cards ── */
.latest-card {
    flex: 0 0 160px;
    min-width: 160px;
    scroll-snap-align: start;
    text-decoration: none;
    color: inherit;
}
.latest-card-thumb {
    width: 100%;
    aspect-ratio: 16/9;
    border-radius: 8px;
    object-fit: cover;
    background: var(--surface, #1a1a1a);
    display: block;
}
.latest-card-title {
    font-size: .8rem;
    font-weight: 500;
    color: var(--text-primary, #fff);
    margin-top: .4rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.latest-card-sub {
    font-size: .72rem;
    color: var(--text-muted, #aaa);
    margin-top: .15rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

@media(max-width: 600px) {
    .portal-cards {
        grid-template-columns: 1fr;
    }
    .latest-card {
        flex: 0 0 140px;
        min-width: 140px;
    }
}
</style>

<!-- ── TOPBAR ── -->
<header class="topbar">
    <div class="topbar-left">
        <button class="icon-btn" id="menu-toggle" title="Menu">
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
            <input type="text" class="search-input" id="search" placeholder="Buscar en EduTube...">
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
    <input type="text" class="search-input" id="mobile-search-input" placeholder="Buscar en EduTube...">
</div>

<!-- ── SIDEBAR ── -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-section">
        <a href="/" class="sidebar-item active">
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
        <a href="libros" class="sidebar-item">
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
            <span class="si-icon">🕐</span><span class="si-label">Reproducir después</span>
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
    <!-- Slogan -->
    <div style="text-align:center;padding:1.5rem 1rem 0.5rem;">
        <div style="font-size:1.15rem;font-weight:600;color:var(--text-primary);">Plataforma de contenido educativo y cultural</div>
        <div style="font-size:0.85rem;color:var(--text-muted);margin-top:0.3rem;">Sin algoritmos, sin publicidad. Solo educación.</div>
    </div>

    <!-- Section cards -->
    <div class="portal-cards" id="portal-cards"></div>

    <!-- Novedades separator -->
    <div style="display:flex;align-items:center;gap:1rem;margin:1.5rem 0 1rem;padding:0 0.5rem;">
        <div style="flex:1;height:1px;background:var(--border-color,#e0e0e0);"></div>
        <h2 style="font-size:1.1rem;font-weight:600;color:var(--text-secondary,#555);white-space:nowrap;margin:0;">Novedades</h2>
        <div style="flex:1;height:1px;background:var(--border-color,#e0e0e0);"></div>
    </div>

    <!-- Latest content rows -->
    <div id="latest-rows"></div>

    <!-- Search results (hidden by default) -->
    <div id="search-results" style="display:none;"></div>
</main>

<!-- ── BOTTOM NAV (mobile) ── -->
<nav class="bottom-nav">
    <a href="/" class="bottom-nav-item active">
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
    <a href="audiolibros" class="bottom-nav-item">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
        Audiolibros
    </a>
    <a href="libros" class="bottom-nav-item">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
        Libros
    </a>
</nav>

<div class="toast" id="toast"></div>

<script>
// ── Helpers ──
function getStore(key) { try { return JSON.parse(localStorage.getItem('edutube_' + key)) || []; } catch(e) { return []; } }

function timeAgo(dateStr) {
    if (!dateStr) return '';
    var diff = Math.floor((new Date() - new Date(dateStr)) / 1000);
    if (diff < 60) return 'hace un momento';
    if (diff < 3600) return 'hace ' + Math.floor(diff/60) + ' min';
    if (diff < 86400) return 'hace ' + Math.floor(diff/3600) + ' h';
    var d = Math.floor(diff / 86400);
    if (d < 7) return 'hace ' + d + (d===1?' dia':' dias');
    if (d < 30) return 'hace ' + Math.floor(d/7) + (Math.floor(d/7)===1?' semana':' semanas');
    if (d < 365) return 'hace ' + Math.floor(d/30) + (Math.floor(d/30)===1?' mes':' meses');
    return 'hace ' + Math.floor(d/365) + (Math.floor(d/365)===1?' anio':' anios');
}

var toastTimer;
function showToast(msg) {
    var t = document.getElementById('toast'); t.textContent = msg; t.classList.add('show');
    clearTimeout(toastTimer); toastTimer = setTimeout(function() { t.classList.remove('show'); }, 2500);
}

// ── Activity badges ──
function updateBadges() {
    ['history','watchlater','liked'].forEach(function(key) {
        var n = getStore(key).length;
        var el = document.getElementById(key + '-count');
        if (el) {
            if (n > 0) { el.textContent = n; el.style.display = ''; } else { el.style.display = 'none'; }
        }
    });
}

// ── Build portal cards ──
function buildPortalCards(counts) {
    var sections = [
        { name: 'Videos', href: 'videos', img: 'img/card-videos.jpg', color: '#2e8b47', key: 'videos', desc: 'Videos educativos curados de YouTube' },
        { name: 'Cine', href: 'cine', img: 'img/card-cine.jpg', color: '#e63946', key: 'cine', desc: 'Peliculas y documentales de Internet Archive' },
        { name: 'Audiolibros', href: 'audiolibros', img: 'img/card-audiolibros.jpg', color: '#6a4c93', key: 'audiolibros', desc: 'Audiolibros completos de Internet Archive' },
        { name: 'Libros', href: 'libros', img: 'img/card-libros.jpg', color: '#0077b6', key: 'libros', desc: 'Biblioteca digital de obras clásicas' }
    ];
    var html = '';
    sections.forEach(function(s) {
        var count = counts[s.key] || 0;
        html += '<a href="' + s.href + '" class="portal-card">' +
            '<div class="portal-card-bg" style="background-image:url(\'' + s.img + '\');background-color:' + s.color + ';"></div>' +
            '<div class="portal-card-overlay"></div>' +
            '<div class="portal-card-body">' +
                '<div class="portal-card-name">' + s.name + '</div>' +
                (count ? '<span class="portal-card-count">' + count + ' titulos</span>' : '') +
                '<div class="portal-card-desc">' + s.desc + '</div>' +
            '</div>' +
        '</a>';
    });
    document.getElementById('portal-cards').innerHTML = html;
}

// ── Build latest rows ──
function buildLatestRows() {
    var container = document.getElementById('latest-rows');
    container.innerHTML =
        '<div class="latest-row" id="row-videos"><div class="latest-row-header"><span class="latest-row-title">Ultimos videos</span><a href="videos" class="latest-row-link">Ver todo &rarr;</a></div><div class="latest-row-scroll" id="scroll-videos"></div></div>' +
        '<div class="latest-row" id="row-cine" style="display:none;"><div class="latest-row-header"><span class="latest-row-title">Ultimo en Cine</span><a href="cine" class="latest-row-link">Ver todo &rarr;</a></div><div class="latest-row-scroll" id="scroll-cine"></div></div>' +
        '<div class="latest-row" id="row-audiolibros" style="display:none;"><div class="latest-row-header"><span class="latest-row-title">Ultimos audiolibros</span><a href="audiolibros" class="latest-row-link">Ver todo &rarr;</a></div><div class="latest-row-scroll" id="scroll-audiolibros"></div></div>' +
        '<div class="latest-row" id="row-libros" style="display:none;"><div class="latest-row-header"><span class="latest-row-title">Ultimos libros</span><a href="libros" class="latest-row-link">Ver todo &rarr;</a></div><div class="latest-row-scroll" id="scroll-libros"></div></div>';

    // Fetch latest videos
    fetch('api.php?action=videos')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var videos = (data.videos || []).slice(0, 6);
            var html = '';
            videos.forEach(function(v) {
                html += '<a href="watch?v=' + v.youtube_id + '" class="latest-card">' +
                    '<img class="latest-card-thumb" src="https://img.youtube.com/vi/' + v.youtube_id + '/mqdefault.jpg" alt="" loading="lazy">' +
                    '<div class="latest-card-title">' + v.titulo + '</div>' +
                    '<div class="latest-card-sub">' + (v.canal_nombre || '') + '</div>' +
                '</a>';
            });
            document.getElementById('scroll-videos').innerHTML = html;
        }).catch(function(){});

    // Fetch latest cine
    fetch('api.php?action=contenido_ia&seccion=cine')
        .then(function(r) { return r.json(); })
        .then(function(items) {
            var latest = items.slice(0, 6);
            if (latest.length === 0) return;
            var html = '';
            latest.forEach(function(item) {
                var thumbUrl = 'https://archive.org/download/' + item.ia_id + '/__ia_thumb.jpg';
                html += '<a href="watch?v=' + item.id + '" class="latest-card">' +
                    '<img class="latest-card-thumb" src="' + thumbUrl + '" alt="" loading="lazy">' +
                    '<div class="latest-card-title">' + item.titulo + '</div>' +
                    '<div class="latest-card-sub">' + (item.director || '') + '</div>' +
                '</a>';
            });
            document.getElementById('scroll-cine').innerHTML = html;
            document.getElementById('row-cine').style.display = '';
        }).catch(function(){});

    // Fetch latest audiolibros
    fetch('api.php?action=contenido_ia&seccion=audiolibros')
        .then(function(r) { return r.json(); })
        .then(function(items) {
            var latest = items.slice(0, 6);
            if (latest.length === 0) return;
            var html = '';
            latest.forEach(function(item) {
                var thumbUrl = 'https://archive.org/download/' + item.ia_id + '/__ia_thumb.jpg';
                html += '<a href="watch?v=' + item.id + '" class="latest-card">' +
                    '<img class="latest-card-thumb" src="' + thumbUrl + '" alt="" loading="lazy">' +
                    '<div class="latest-card-title">' + item.titulo + '</div>' +
                    '<div class="latest-card-sub">' + (item.autor || '') + '</div>' +
                '</a>';
            });
            document.getElementById('scroll-audiolibros').innerHTML = html;
            document.getElementById('row-audiolibros').style.display = '';
        }).catch(function(){});

    // Fetch latest libros
    fetch('api.php?action=contenido_ia&seccion=libros')
        .then(function(r) { return r.json(); })
        .then(function(items) {
            var latest = items.slice(0, 6);
            if (latest.length === 0) return;
            var html = '';
            latest.forEach(function(item) {
                var thumbUrl = item.url_portada || 'img/card-libros.jpg';
                html += '<a href="leer?id=' + item.id + '" class="latest-card">' +
                    '<img class="latest-card-thumb" src="' + thumbUrl + '" alt="" loading="lazy" style="object-fit:contain;background:#f5f0e8;">' +
                    '<div class="latest-card-title">' + item.titulo + '</div>' +
                    '<div class="latest-card-sub">' + (item.director || '') + '</div>' +
                '</a>';
            });
            document.getElementById('scroll-libros').innerHTML = html;
            document.getElementById('row-libros').style.display = '';
        }).catch(function(){});
}

// ── Init ──
updateBadges();

// Load counts and build cards
fetch('api.php?action=total_titulos')
    .then(function(r) { return r.json(); })
    .then(function(d) {
        document.getElementById('video-count').textContent = d.total + ' titulos';
        document.getElementById('cnt-videos').textContent = '(' + d.videos + ')';
        document.getElementById('cnt-cine').textContent = '(' + d.cine + ')';
        document.getElementById('cnt-audiolibros').textContent = '(' + d.audiolibros + ')';
        var cntLibros = document.getElementById('cnt-libros');
        if (cntLibros) cntLibros.textContent = '(' + (d.libros || 0) + ')';
        buildPortalCards({ videos: d.videos, cine: d.cine, audiolibros: d.audiolibros, libros: d.libros || 0 });
    });

buildLatestRows();

// Activity sidebar links → go to videos page with filter
document.getElementById('nav-history').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = 'videos';
});
document.getElementById('nav-watchlater').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = 'videos';
});
document.getElementById('nav-liked').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = 'videos';
});

// Search → redirect to videos page
// Cache IA content for instant search
var cachedIA = [];
fetch('api.php?action=contenido_ia').then(function(r){return r.json();}).then(function(d){ cachedIA = d; });

var searchTimer = null;

function showPortal() {
    var main = document.getElementById('main-content');
    for (var i = 0; i < main.children.length; i++) {
        main.children[i].style.display = '';
    }
    document.getElementById('search-results').style.display = 'none';
}

function hidePortal() {
    document.getElementById('portal-cards').style.display = 'none';
    document.getElementById('latest-rows').style.display = 'none';
    // Hide slogan and separator
    var main = document.getElementById('main-content');
    for (var i = 0; i < main.children.length; i++) {
        if (main.children[i].id !== 'search-results') main.children[i].style.display = 'none';
    }
    document.getElementById('search-results').style.display = '';
}

function doSearch(q) {
    q = q.trim();
    if (!q) { showPortal(); return; }
    hidePortal();

    var resultsDiv = document.getElementById('search-results');
    var lq = q.toLowerCase();

    // Instant: search cached IA content
    var cineResults = cachedIA.filter(function(item) {
        return item.section === 'Cine' && (
            item.titulo.toLowerCase().indexOf(lq) > -1 ||
            (item.director || '').toLowerCase().indexOf(lq) > -1 ||
            (item.genero || '').toLowerCase().indexOf(lq) > -1);
    }).slice(0, 10);
    var audioResults = cachedIA.filter(function(item) {
        return item.section === 'Audiolibro' && (
            item.titulo.toLowerCase().indexOf(lq) > -1 ||
            (item.director || '').toLowerCase().indexOf(lq) > -1 ||
            (item.genero || '').toLowerCase().indexOf(lq) > -1);
    }).slice(0, 10);
    var librosResults = cachedIA.filter(function(item) {
        return item.section === 'Libro' && (
            item.titulo.toLowerCase().indexOf(lq) > -1 ||
            (item.director || '').toLowerCase().indexOf(lq) > -1 ||
            (item.genero || '').toLowerCase().indexOf(lq) > -1);
    }).slice(0, 10);

    // Show instant results immediately, then fetch videos
    renderSearchResults(q, [], cineResults, audioResults, librosResults, true);

    // Fetch videos from API (slightly slower)
    fetch('api.php?action=search&q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var videos = (res.videos || []).slice(0, 10);
            renderSearchResults(q, videos, cineResults, audioResults, librosResults, false);
        })
        .catch(function() {
            renderSearchResults(q, [], cineResults, audioResults, librosResults, false);
        });
}

function renderSearchResults(q, videos, cineResults, audioResults, librosResults, loading) {
    var resultsDiv = document.getElementById('search-results');
    var total = videos.length + cineResults.length + audioResults.length + librosResults.length;

    if (!total && !loading) {
        resultsDiv.innerHTML = '<p style="color:var(--text-muted);padding:2rem;text-align:center;">No se encontraron resultados para "' + q + '"</p>';
        return;
    }

    var html = '<div style="padding:0.5rem 0;">';
    html += '<div style="font-size:0.9rem;color:var(--text-muted);margin-bottom:1rem;">' + (loading ? 'Buscando' : total + ' resultados para') + ' "' + q + '"' + (loading ? '...' : '') + '</div>';

    if (videos.length) {
        html += '<h3 style="font-size:1rem;margin:1rem 0 0.5rem;"><a href="videos?q=' + encodeURIComponent(q) + '" style="color:var(--text-primary);text-decoration:none;">Videos (' + videos.length + ') →</a></h3>';
        html += '<div class="video-grid" style="display:grid;">';
        videos.forEach(function(v) {
            html += '<div class="video-card"><a href="watch?v=' + v.youtube_id + '" class="thumb"><img src="https://img.youtube.com/vi/' + v.youtube_id + '/mqdefault.jpg" alt="" loading="lazy">' +
                (v.duracion ? '<span class="duration-badge">' + v.duracion + '</span>' : '') +
                '</a><div class="card-info"><div class="channel-avatar" style="background:' + (v.canal_color || '#2e8b47') + '">' + (v.canal_codigo || '▶') + '</div>' +
                '<div class="card-text"><a href="watch?v=' + v.youtube_id + '" class="card-title">' + v.titulo + '</a>' +
                '<div class="card-channel-static">' + (v.canal_nombre || '') + '</div></div></div></div>';
        });
        html += '</div>';
    } else if (loading) {
        html += '<h3 style="font-size:1rem;margin:1rem 0 0.5rem;color:var(--text-muted);">Videos — buscando...</h3>';
    }

    if (cineResults.length) {
        html += '<h3 style="font-size:1rem;margin:1.5rem 0 0.5rem;"><a href="cine" style="color:var(--text-primary);text-decoration:none;">Cine (' + cineResults.length + ') →</a></h3>';
        html += '<div class="video-grid" style="display:grid;">';
        cineResults.forEach(function(p) {
            html += '<div class="video-card"><a href="watch?v=' + p.id + '" class="thumb"><img src="https://archive.org/download/' + p.ia_id + '/__ia_thumb.jpg" alt="" loading="lazy">' +
                (p.duracion ? '<span class="duration-badge">' + p.duracion + '</span>' : '') +
                '</a><div class="card-info"><div class="channel-avatar" style="background:#e63946;font-size:0.65rem;">🎬</div>' +
                '<div class="card-text"><a href="watch?v=' + p.id + '" class="card-title">' + p.titulo + '</a>' +
                '<div class="card-channel-static">' + (p.director || '') + '</div></div></div></div>';
        });
        html += '</div>';
    }

    if (audioResults.length) {
        html += '<h3 style="font-size:1rem;margin:1.5rem 0 0.5rem;"><a href="audiolibros" style="color:var(--text-primary);text-decoration:none;">Audiolibros (' + audioResults.length + ') →</a></h3>';
        html += '<div class="video-grid" style="display:grid;">';
        audioResults.forEach(function(p) {
            html += '<div class="video-card"><a href="watch?v=' + p.id + '" class="thumb"><img src="https://archive.org/download/' + p.ia_id + '/__ia_thumb.jpg" alt="" loading="lazy">' +
                '</a><div class="card-info"><div class="channel-avatar" style="background:#6a4c93;font-size:0.65rem;">📖</div>' +
                '<div class="card-text"><a href="watch?v=' + p.id + '" class="card-title">' + p.titulo + '</a>' +
                '<div class="card-channel-static">' + (p.director || '') + '</div></div></div></div>';
        });
        html += '</div>';
    }

    if (librosResults.length) {
        html += '<h3 style="font-size:1rem;margin:1.5rem 0 0.5rem;"><a href="libros" style="color:var(--text-primary);text-decoration:none;">Libros (' + librosResults.length + ') →</a></h3>';
        html += '<div class="video-grid" style="display:grid;">';
        librosResults.forEach(function(p) {
            var thumbUrl = p.url_portada || 'img/card-libros.jpg';
            html += '<div class="video-card"><a href="leer?id=' + p.id + '" class="thumb" style="background:#f5f0e8;"><img src="' + thumbUrl + '" alt="" loading="lazy" style="object-fit:contain;">' +
                '</a><div class="card-info"><div class="channel-avatar" style="background:#0077b6;font-size:0.65rem;">📚</div>' +
                '<div class="card-text"><a href="leer?id=' + p.id + '" class="card-title">' + p.titulo + '</a>' +
                '<div class="card-channel-static">' + (p.director || '') + '</div></div></div></div>';
        });
        html += '</div>';
    }

    html += '</div>';
    resultsDiv.innerHTML = html;
}

document.getElementById('search').addEventListener('input', function() {
    var val = this.value;
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() { doSearch(val); }, 300);
});
document.getElementById('search').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { clearTimeout(searchTimer); doSearch(this.value); }
});
document.getElementById('search-btn').addEventListener('click', function() {
    clearTimeout(searchTimer);
    doSearch(document.getElementById('search').value);
});

// Mobile search
document.getElementById('mobile-search-toggle').addEventListener('click', function() {
    document.getElementById('mobile-search-overlay').classList.add('open');
    document.getElementById('mobile-search-input').focus();
});
document.getElementById('mobile-search-close').addEventListener('click', function() {
    document.getElementById('mobile-search-overlay').classList.remove('open');
    document.getElementById('mobile-search-input').value = '';
});
document.getElementById('mobile-search-input').addEventListener('input', function() {
    var val = this.value;
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() { doSearch(val); }, 300);
});
document.getElementById('mobile-search-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { clearTimeout(searchTimer); doSearch(this.value); }
});

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
