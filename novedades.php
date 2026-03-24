<?php
$title = 'Novedades — EduTube';
$description = 'Últimas incorporaciones a EduTube.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">
    <link rel="stylesheet" href="style.css">
    <style>
        .nov-feed { max-width:800px; }
        .nov-item { display:flex; gap:1rem; padding:1rem 0; border-bottom:1px solid var(--border-color,#eee); }
        .nov-item:last-child { border-bottom:none; }
        .nov-avatar { width:48px; height:48px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; color:#fff; object-fit:cover; }
        .nov-body { flex:1; min-width:0; }
        .nov-title { font-weight:600; font-size:0.95rem; margin-bottom:0.2rem; }
        .nov-detail { font-size:0.85rem; color:var(--text-secondary,#555); line-height:1.5; }
        .nov-meta { display:flex; gap:0.75rem; margin-top:0.3rem; font-size:0.78rem; color:var(--text-muted,#888); }
        .nov-badge { display:inline-block; padding:1px 8px; border-radius:10px; font-size:0.72rem; font-weight:500; }
        .nov-tipo-canal { background:#eef7f0; color:#2e8b47; }
        .nov-tipo-cine { background:#fde8ea; color:#e63946; }
        .nov-tipo-audiolibros { background:#ede7f6; color:#6a4c93; }
        .nov-tipo-libros { background:#e3f2fd; color:#0077b6; }
        .nov-date-group { font-size:0.85rem; font-weight:600; color:var(--text-secondary,#555); padding:1rem 0 0.5rem; border-bottom:2px solid var(--border-color,#e0e0e0); margin-top:0.5rem; }
    </style>
</head>
<body>

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
</header>

<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-section">
        <a href="/" class="sidebar-item">
            <span class="si-icon">🏠</span><span class="si-label">Inicio</span>
        </a>
        <a href="videos" class="sidebar-item">
            <span class="si-icon">📺</span><span class="si-label">Videos</span>
        </a>
        <a href="cine" class="sidebar-item">
            <span class="si-icon">🎬</span><span class="si-label">Cine</span>
        </a>
        <a href="audiolibros" class="sidebar-item">
            <span class="si-icon">📖</span><span class="si-label">Audiolibros</span>
        </a>
        <a href="libros" class="sidebar-item">
            <span class="si-icon">📚</span><span class="si-label">Libros</span>
        </a>
        <a href="novedades" class="sidebar-item active">
            <span class="si-icon">🆕</span><span class="si-label">Novedades</span>
        </a>
    </div>
    <div class="sidebar-footer">
        <strong>EduTube</strong> — Plataforma Educativa y Cultural<br>
        <a href="https://comite.cooperativaliberte.coop/" target="_blank" style="color:inherit;text-decoration:underline;">Comité de Convivencia Mario Juliano</a> &copy; 2026<br>
        <a href="landing" style="color:inherit;font-size:0.75rem;">Acerca de EduTube</a>
    </div>
</nav>

<main class="main" id="main-content">
    <h1 style="font-size:1.3rem;margin-bottom:0.5rem;">Novedades</h1>
    <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:1.5rem;">Últimas incorporaciones a la plataforma</p>
    <div class="nov-feed" id="nov-feed">
        <p style="color:var(--text-muted);padding:2rem;text-align:center;">Cargando...</p>
    </div>
</main>

<nav class="bottom-nav">
    <a href="/" class="bottom-nav-item">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        Inicio
    </a>
    <a href="novedades" class="bottom-nav-item active">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12zM6 10h2v2H6v-2zm0 4h8v2H6v-2zm10 0h2v2h-2v-2zm-6-4h8v2h-8v-2z"/></svg>
        Novedades
    </a>
</nav>

<script>
// Sidebar
document.getElementById('menu-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebar-backdrop').classList.toggle('open');
});
document.getElementById('sidebar-backdrop').addEventListener('click', function() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-backdrop').classList.remove('open');
});

// Total
fetch('api.php?action=total_titulos').then(function(r){return r.json();}).then(function(d){
    document.getElementById('total-count').textContent = d.total + ' títulos';
});

// Load novedades
fetch('api.php?action=novedades')
    .then(function(r) { return r.json(); })
    .then(function(items) {
        if (!items.length) {
            document.getElementById('nov-feed').innerHTML = '<p style="color:var(--text-muted);text-align:center;">No hay novedades todavía.</p>';
            return;
        }

        var tipoIcons = { canal:'📺', cine:'🎬', audiolibros:'📖', libros:'📚' };
        var tipoLabels = { canal:'Nuevo canal', cine:'Cine', audiolibros:'Audiolibros', libros:'Libros' };
        var tipoClasses = { canal:'nov-tipo-canal', cine:'nov-tipo-cine', audiolibros:'nov-tipo-audiolibros', libros:'nov-tipo-libros' };

        var html = '';
        var lastDate = '';
        items.forEach(function(item) {
            var fecha = new Date(item.fecha);
            var dateStr = fecha.toLocaleDateString('es-AR', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
            if (dateStr !== lastDate) {
                html += '<div class="nov-date-group">' + dateStr.charAt(0).toUpperCase() + dateStr.slice(1) + '</div>';
                lastDate = dateStr;
            }

            var avatar = '';
            if (item.thumbnail) {
                avatar = '<img src="' + item.thumbnail + '" class="nov-avatar">';
            } else {
                var bg = item.color || '#888';
                avatar = '<div class="nov-avatar" style="background:' + bg + ';">' + (tipoIcons[item.tipo] || '📌') + '</div>';
            }

            var timeStr = fecha.toLocaleTimeString('es-AR', { hour:'2-digit', minute:'2-digit' });

            html += '<div class="nov-item">' +
                avatar +
                '<div class="nov-body">' +
                    '<div class="nov-title">' + item.titulo + '</div>' +
                    (item.detalle ? '<div class="nov-detail">' + item.detalle + '</div>' : '') +
                    '<div class="nov-meta">' +
                        '<span class="nov-badge ' + (tipoClasses[item.tipo] || '') + '">' + (tipoLabels[item.tipo] || item.tipo) + '</span>' +
                        (item.categoria ? '<span>' + item.categoria + '</span>' : '') +
                        (item.extra ? '<span>' + item.extra + '</span>' : '') +
                        '<span>' + timeStr + '</span>' +
                    '</div>' +
                '</div>' +
            '</div>';
        });

        document.getElementById('nov-feed').innerHTML = html;
    })
    .catch(function() {
        document.getElementById('nov-feed').innerHTML = '<p style="color:#c00;text-align:center;">Error al cargar novedades</p>';
    });
</script>
</body>
</html>
