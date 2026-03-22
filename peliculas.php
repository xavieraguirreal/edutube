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

<header class="topbar">
    <div class="topbar-left">
        <a href="index.php" class="logo">
            <img src="loguito-edutube.png" alt="EduTube" class="logo-icon">
            <span class="logo-text">EduTube</span>
        </a>
    </div>
    <div class="topbar-center">
        <div class="search-form">
            <input type="text" class="search-input" placeholder="Buscar videos educativos..." id="search-top">
            <button class="search-btn" title="Buscar" id="search-top-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            </button>
        </div>
    </div>
    <div class="topbar-right"></div>
</header>

<main class="main canal-page" id="peliculas-page">

<div class="canal-banner canal-banner-gradient" style="background:linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%)"></div>

<div class="canal-profile-header">
    <div class="canal-profile-left">
        <div class="canal-profile-avatar" style="background:#e63946;font-size:1.8rem;">🎬</div>
        <div class="canal-profile-info">
            <h1 class="canal-profile-name">Películas</h1>
            <div class="canal-profile-meta">Cine clásico de dominio público · Fuente: Internet Archive</div>
        </div>
    </div>
</div>

<div class="canal-description-card">
    <div class="canal-description-text">Películas clásicas disponibles en dominio público, provenientes del catálogo de Internet Archive. Sin restricciones de uso, sin publicidad, sin tracking.</div>
</div>

<div class="canal-section">
    <div class="canal-section-header">
        <h2 class="canal-section-title">Catálogo</h2>
    </div>
    <div class="canal-all-videos-grid" id="peliculas-grid"></div>
</div>

</main>

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

// ── Catálogo de películas (hardcodeado por ahora) ──
var peliculas = [
    {
        id: 'ia:TheGreatDictator',
        ia_id: 'OGrandeDitadorTheGreatDictatorCharlieChaplin1940',
        titulo: 'The Great Dictator (1940)',
        director: 'Charlie Chaplin',
        year: 1940,
        duracion: '2:04:37',
        descargas: 72797,
        genero: 'Comedia / Drama'
    },
    {
        id: 'ia:Nosferatu',
        ia_id: 'Nosferatu_most_complete_version_93_mins',
        titulo: 'Nosferatu (1922)',
        director: 'F.W. Murnau',
        year: 1922,
        duracion: '1:33:00',
        descargas: 424808,
        genero: 'Terror / Expresionismo'
    },
    {
        id: 'ia:PhantomOpera',
        ia_id: 'ThePhantomoftheOpera',
        titulo: 'The Phantom of the Opera (1925)',
        director: 'Rupert Julian',
        year: 1925,
        duracion: '1:33:00',
        descargas: 615942,
        genero: 'Terror / Drama'
    },
    {
        id: 'ia:BattleshipPotemkin',
        ia_id: 'BattleshipPotemkin',
        titulo: 'El acorazado Potemkin (1925)',
        director: 'Sergei Eisenstein',
        year: 1925,
        duracion: '1:15:00',
        descargas: 440634,
        genero: 'Drama / Historia'
    },
    {
        id: 'ia:Caligari',
        ia_id: 'DasKabinettdesDoktorCaligariTheCabinetofDrCaligari',
        titulo: 'El gabinete del Dr. Caligari (1919)',
        director: 'Robert Wiene',
        year: 1919,
        duracion: '1:16:00',
        descargas: 525087,
        genero: 'Terror / Expresionismo'
    },
    {
        id: 'ia:CyranoDBergerac',
        ia_id: 'Cyrano_DeBergerac',
        titulo: 'Cyrano de Bergerac (1950)',
        director: 'Michael Gordon',
        year: 1950,
        duracion: '1:52:00',
        descargas: 487906,
        genero: 'Drama / Romance'
    },
    {
        id: 'ia:Frankenstein1910',
        ia_id: 'FrankensteinfullMovie',
        titulo: 'Frankenstein (1910)',
        director: 'J. Searle Dawley',
        year: 1910,
        duracion: '0:16:00',
        descargas: 370404,
        genero: 'Terror / Ciencia ficción'
    },
    {
        id: 'ia:GreatExpectations',
        ia_id: 'GreatExpectations1946',
        titulo: 'Great Expectations (1946)',
        director: 'David Lean',
        year: 1946,
        duracion: '1:58:00',
        descargas: 432948,
        genero: 'Drama'
    },
    {
        id: 'ia:Scrooge1935',
        ia_id: 'Scrooge_1935',
        titulo: 'Scrooge (1935)',
        director: 'Henry Edwards',
        year: 1935,
        duracion: '1:18:00',
        descargas: 301187,
        genero: 'Drama / Navidad'
    },
    {
        id: 'ia:MarkOfZorro',
        ia_id: 'markofzorro-1920',
        titulo: 'The Mark of Zorro (1920)',
        director: 'Fred Niblo',
        year: 1920,
        duracion: '1:30:00',
        descargas: 333047,
        genero: 'Aventura / Acción'
    },
    {
        id: 'ia:HisGirlFriday',
        ia_id: 'his_girl_friday',
        titulo: 'His Girl Friday (1940)',
        director: 'Howard Hawks',
        year: 1940,
        duracion: '1:32:00',
        descargas: 1288432,
        genero: 'Comedia / Romance'
    },
    {
        id: 'ia:SherlockHolmes',
        ia_id: 'secret_weapon',
        titulo: 'Sherlock Holmes and the Secret Weapon (1943)',
        director: 'Roy William Neill',
        year: 1943,
        duracion: '1:08:00',
        descargas: 440894,
        genero: 'Misterio / Aventura'
    }
];

function renderPeliculas() {
    var grid = document.getElementById('peliculas-grid');
    var html = '';
    peliculas.forEach(function(p) {
        var thumbUrl = 'https://archive.org/download/' + p.ia_id + '/__ia_thumb.jpg';
        html += '<div class="video-card">' +
            '<a href="watch?v=' + p.id + '" class="thumb">' +
                '<img src="' + thumbUrl + '" alt="" loading="lazy">' +
                '<span class="duration-badge">' + p.duracion + '</span>' +
            '</a>' +
            '<div class="card-info">' +
                '<div class="channel-avatar" style="background:#e63946;font-size:0.7rem;">🎬</div>' +
                '<div class="card-text">' +
                    '<a href="watch?v=' + p.id + '" class="card-title">' + p.titulo + '</a>' +
                    '<div class="card-channel-static">' + p.director + '</div>' +
                    '<div class="card-stats">' + formatViews(p.descargas) + ' descargas · ' + p.genero + '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    });
    grid.innerHTML = html;
}

renderPeliculas();

// Search redirect
document.getElementById('search-top-btn').addEventListener('click', function() {
    var q = document.getElementById('search-top').value.trim();
    if (q) window.location.href = 'index.php?q=' + encodeURIComponent(q);
});
document.getElementById('search-top').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        var q = this.value.trim();
        if (q) window.location.href = 'index.php?q=' + encodeURIComponent(q);
    }
});
</script>

</body>
</html>
