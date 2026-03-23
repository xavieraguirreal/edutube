<?php
$itemId = trim($_GET['id'] ?? '');
$title = 'Leer — EduTube';
$bookData = null;
$gutenbergId = 0;

if ($itemId) {
    try {
        require_once __DIR__ . '/config.php';
        $db = getDB();
        $slug = preg_replace('/^ia:/', '', $itemId);
        $stmt = $db->prepare("SELECT * FROM contenido_ia WHERE slug = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$slug]);
        $bookData = $stmt->fetch();
        if ($bookData) {
            $title = $bookData['titulo'] . ' — EduTube';
            $gutenbergId = intval(str_replace('gutenberg_', '', $bookData['ia_id']));
        }
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        img-src 'self' https://www.gutenberg.org https://*.gutenberg.org https://archive.org https://*.archive.org blob: data:;
        style-src 'self' 'unsafe-inline' https://fonts.googleapis.com blob:;
        font-src 'self' https://fonts.gstatic.com blob: data:;
        script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net;
        connect-src 'self' blob:;
        worker-src blob:;
        child-src blob:;
    ">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ── Book info header ── */
        .reader-layout { max-width:900px; margin:0 auto; padding:1rem; }
        .reader-header { display:flex; gap:1.5rem; margin-bottom:1.5rem; align-items:flex-start; }
        .reader-cover { width:150px; flex-shrink:0; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
        .reader-meta h1 { font-size:1.3rem; margin-bottom:0.3rem; }
        .reader-meta .author { color:var(--text-secondary,#555); font-size:0.95rem; }
        .reader-meta .genre { color:var(--text-muted,#888); font-size:0.85rem; margin-top:0.3rem; }
        .reader-actions { display:flex; gap:0.5rem; margin-top:1rem; flex-wrap:wrap; }
        .reader-actions .btn { padding:0.5rem 1rem; border-radius:8px; font-size:0.88rem; font-weight:500; cursor:pointer; border:none; font-family:inherit; text-decoration:none; display:inline-flex; align-items:center; gap:0.3rem; }
        .btn-read { background:#0077b6; color:#fff; }
        .btn-read:hover { background:#005f8a; }
        .btn-back { background:none; border:1px solid #ddd; color:#555; }
        .btn-back:hover { background:#f5f5f5; }
        .book-desc { color:var(--text-secondary); font-size:0.9rem; line-height:1.6; margin-bottom:1rem; }

        /* ── EPUB Reader fullscreen ── */
        .epub-reader { display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; background:#fff; flex-direction:column; }
        .epub-reader.active { display:flex; }
        .epub-reader.dark { background:#1a1a2e; color:#e0e0e0; }

        /* Toolbar */
        .epub-toolbar {
            display:flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem;
            background:#f8f8f8; border-bottom:1px solid #e0e0e0;
            flex-shrink:0; min-height:48px; flex-wrap:wrap;
        }
        .dark .epub-toolbar { background:#12122a; border-color:#333; }
        .epub-toolbar .tb-btn {
            background:none; border:1px solid #ccc; border-radius:6px; padding:0.35rem 0.65rem;
            cursor:pointer; font-size:0.85rem; color:#333; font-family:inherit; white-space:nowrap;
        }
        .dark .epub-toolbar .tb-btn { border-color:#555; color:#ccc; }
        .epub-toolbar .tb-btn:hover { background:rgba(0,0,0,0.05); }
        .dark .epub-toolbar .tb-btn:hover { background:rgba(255,255,255,0.08); }
        .epub-toolbar .tb-title {
            flex:1; font-size:0.85rem; font-weight:600; overflow:hidden;
            text-overflow:ellipsis; white-space:nowrap; text-align:center;
            min-width:0; color:#333;
        }
        .dark .epub-toolbar .tb-title { color:#e0e0e0; }
        .epub-toolbar .tb-spacer { flex:1; }

        /* TOC sidebar */
        .epub-toc {
            display:none; position:absolute; top:48px; left:0; bottom:48px;
            width:300px; max-width:80vw; background:#fff; border-right:1px solid #e0e0e0;
            overflow-y:auto; z-index:10; box-shadow:2px 0 8px rgba(0,0,0,0.1);
        }
        .epub-toc.open { display:block; }
        .dark .epub-toc { background:#1a1a2e; border-color:#333; }
        .epub-toc h3 { padding:1rem; font-size:0.95rem; border-bottom:1px solid #eee; }
        .dark .epub-toc h3 { border-color:#333; }
        .epub-toc ul { list-style:none; padding:0; margin:0; }
        .epub-toc li a {
            display:block; padding:0.6rem 1rem; font-size:0.85rem; color:#333;
            text-decoration:none; border-bottom:1px solid #f0f0f0;
        }
        .epub-toc li a:hover { background:#f0f7ff; }
        .dark .epub-toc li a { color:#ccc; border-color:#2a2a4a; }
        .dark .epub-toc li a:hover { background:#22224a; }

        /* Reading area */
        .epub-body { flex:1; position:relative; overflow:hidden; }
        #epub-area { width:100%; height:100%; }

        /* Bottom bar */
        .epub-bottom {
            display:flex; align-items:center; justify-content:space-between; padding:0.5rem 1rem;
            background:#f8f8f8; border-top:1px solid #e0e0e0; flex-shrink:0; min-height:48px;
        }
        .dark .epub-bottom { background:#12122a; border-color:#333; }
        .epub-bottom .nav-btn {
            background:#0077b6; color:#fff; border:none; border-radius:6px;
            padding:0.4rem 1rem; cursor:pointer; font-size:0.85rem; font-family:inherit;
        }
        .epub-bottom .nav-btn:hover { background:#005f8a; }
        .epub-bottom .nav-btn:disabled { opacity:0.4; cursor:default; }
        .epub-progress { font-size:0.8rem; color:#888; text-align:center; }
        .dark .epub-progress { color:#999; }

        /* Loading overlay */
        .epub-loading {
            position:absolute; top:0; left:0; right:0; bottom:0;
            display:flex; align-items:center; justify-content:center; flex-direction:column;
            background:rgba(255,255,255,0.95); z-index:5;
        }
        .dark .epub-loading { background:rgba(26,26,46,0.95); }
        .epub-loading .spinner {
            width:36px; height:36px; border:3px solid #ddd; border-top-color:#0077b6;
            border-radius:50%; animation:spin 0.8s linear infinite;
        }
        .epub-loading p { margin-top:1rem; font-size:0.9rem; color:#888; }
        @keyframes spin { to { transform:rotate(360deg); } }

        /* Error state */
        .epub-error { text-align:center; padding:3rem; color:#888; }
        .epub-error a { color:#0077b6; }

        /* Responsive */
        @media (max-width:600px) {
            .reader-header { flex-direction:column; align-items:center; text-align:center; }
            .reader-cover { width:120px; }
            .epub-toolbar { padding:0.4rem 0.5rem; gap:0.3rem; }
            .epub-toolbar .tb-btn { padding:0.3rem 0.5rem; font-size:0.78rem; }
            .epub-toolbar .tb-title { font-size:0.78rem; }
            .epub-bottom .nav-btn { padding:0.35rem 0.75rem; font-size:0.8rem; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <div class="topbar-left">
        <a href="/" class="logo">
            <img src="loguito-edutube.png" alt="EduTube" class="logo-icon">
            <span class="logo-text">EduTube</span>
        </a>
    </div>
</header>

<main class="main" style="padding-top:60px;" id="main-content">
<?php if ($bookData): ?>
    <div class="reader-layout">
        <div class="reader-header">
            <?php if ($bookData['url_portada']): ?>
                <img src="<?= htmlspecialchars($bookData['url_portada']) ?>" class="reader-cover" alt="">
            <?php endif; ?>
            <div class="reader-meta">
                <h1><?= htmlspecialchars($bookData['titulo']) ?></h1>
                <div class="author"><?= htmlspecialchars($bookData['director']) ?></div>
                <?php if ($bookData['genero']): ?>
                    <div class="genre"><?= htmlspecialchars($bookData['genero']) ?></div>
                <?php endif; ?>
                <div class="reader-actions">
                    <?php if ($gutenbergId): ?>
                        <button class="btn btn-read" id="btn-read" onclick="openReader()">Leer ahora</button>
                    <?php endif; ?>
                    <a href="libros" class="btn btn-back">&larr; Volver a Libros</a>
                </div>
            </div>
        </div>
        <?php if ($bookData['descripcion']): ?>
            <div class="book-desc">
                <?= nl2br(htmlspecialchars(mb_substr($bookData['descripcion'], 0, 500))) ?>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div style="padding:4rem;text-align:center;">
        <h2 style="color:var(--text-muted);">Libro no encontrado</h2>
        <a href="libros" class="btn btn-back" style="margin-top:1rem;display:inline-block;padding:0.5rem 1rem;border:1px solid #ddd;border-radius:8px;color:#555;text-decoration:none;">&larr; Volver a Libros</a>
    </div>
<?php endif; ?>
</main>

<?php if ($bookData && $gutenbergId): ?>
<!-- EPUB Reader overlay -->
<div class="epub-reader" id="epub-reader">
    <!-- Toolbar -->
    <div class="epub-toolbar">
        <button class="tb-btn" onclick="closeReader()" title="Cerrar">&larr; Salir</button>
        <button class="tb-btn" id="btn-toc" onclick="toggleTOC()" title="Tabla de contenidos">Indice</button>
        <span class="tb-title" id="epub-title"><?= htmlspecialchars($bookData['titulo']) ?></span>
        <button class="tb-btn" onclick="changeFontSize(-1)" title="Reducir fuente">A-</button>
        <button class="tb-btn" onclick="changeFontSize(1)" title="Aumentar fuente">A+</button>
        <button class="tb-btn" id="btn-theme" onclick="toggleDarkMode()" title="Modo oscuro/claro">Luna</button>
    </div>

    <!-- TOC sidebar -->
    <div class="epub-toc" id="epub-toc">
        <h3>Tabla de contenidos</h3>
        <ul id="toc-list"></ul>
    </div>

    <!-- Reading area -->
    <div class="epub-body">
        <div id="epub-area"></div>
        <div class="epub-loading" id="epub-loading">
            <div class="spinner"></div>
            <p>Cargando libro...</p>
        </div>
    </div>

    <!-- Bottom navigation -->
    <div class="epub-bottom">
        <button class="nav-btn" id="btn-prev" onclick="goPrev()">&larr; Anterior</button>
        <span class="epub-progress" id="epub-progress"></span>
        <button class="nav-btn" id="btn-next" onclick="goNext()">Siguiente &rarr;</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/epubjs/dist/epub.min.js"></script>
<script>
(function() {
    var GUTENBERG_ID = <?= $gutenbergId ?>;
    var book = null;
    var rendition = null;
    var isDark = false;
    var fontSize = 100; // percentage
    var tocOpen = false;

    window.openReader = function() {
        var reader = document.getElementById('epub-reader');
        var main = document.getElementById('main-content');
        reader.classList.add('active');
        main.style.display = 'none';
        document.body.style.overflow = 'hidden';
        loadEPUB();
    };

    window.closeReader = function() {
        var reader = document.getElementById('epub-reader');
        var main = document.getElementById('main-content');
        reader.classList.remove('active');
        main.style.display = '';
        document.body.style.overflow = '';
        if (rendition) { try { rendition.destroy(); } catch(e){} }
        if (book) { try { book.destroy(); } catch(e){} }
        book = null;
        rendition = null;
        // Reset loading state for next open
        document.getElementById('epub-loading').style.display = '';
    };

    function loadEPUB() {
        var loading = document.getElementById('epub-loading');
        loading.style.display = '';

        var epubUrl = 'api.php?action=proxy_gutenberg_epub&id=' + GUTENBERG_ID;

        // Fetch as ArrayBuffer to pass to epub.js
        fetch(epubUrl)
            .then(function(resp) {
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                return resp.arrayBuffer();
            })
            .then(function(data) {
                book = ePub(data);
                rendition = book.renderTo('epub-area', {
                    width: '100%',
                    height: '100%',
                    spread: 'none',
                    flow: 'paginated'
                });

                // Apply initial theme
                applyTheme();

                rendition.display().then(function() {
                    loading.style.display = 'none';
                });

                // Load TOC
                book.loaded.navigation.then(function(nav) {
                    var list = document.getElementById('toc-list');
                    list.innerHTML = '';
                    if (nav.toc && nav.toc.length) {
                        nav.toc.forEach(function(ch) {
                            var li = document.createElement('li');
                            var a = document.createElement('a');
                            a.textContent = ch.label.trim();
                            a.href = '#';
                            a.onclick = function(e) {
                                e.preventDefault();
                                rendition.display(ch.href);
                                closeTOC();
                            };
                            li.appendChild(a);
                            list.appendChild(li);

                            // Sub-items
                            if (ch.subitems && ch.subitems.length) {
                                ch.subitems.forEach(function(sub) {
                                    var sli = document.createElement('li');
                                    var sa = document.createElement('a');
                                    sa.textContent = '  ' + sub.label.trim();
                                    sa.href = '#';
                                    sa.style.paddingLeft = '2rem';
                                    sa.onclick = function(e) {
                                        e.preventDefault();
                                        rendition.display(sub.href);
                                        closeTOC();
                                    };
                                    sli.appendChild(sa);
                                    list.appendChild(sli);
                                });
                            }
                        });
                    } else {
                        list.innerHTML = '<li style="padding:1rem;color:#888;">Sin tabla de contenidos</li>';
                    }
                });

                // Update progress on relocation
                rendition.on('relocated', function(location) {
                    updateProgress(location);
                });

                // Register touch swipe inside epub iframe
                rendition.hooks.content.register(registerSwipeInContent);

                // Keyboard navigation
                rendition.on('keyup', handleKeyboard);
                document.addEventListener('keyup', handleKeyboard);
            })
            .catch(function(err) {
                loading.innerHTML = '<div class="epub-error"><p>No se pudo cargar el libro.</p><p style="font-size:0.8rem;margin-top:0.5rem;">' + err.message + '</p><p style="margin-top:1rem;"><a href="https://www.gutenberg.org/ebooks/' + GUTENBERG_ID + '" target="_blank">Leer en Proyecto Gutenberg &rarr;</a></p></div>';
            });
    }

    function updateProgress(location) {
        var el = document.getElementById('epub-progress');
        if (location && location.start) {
            var pct = book.locations ? book.locations.percentageFromCfi(location.start.cfi) : 0;
            if (pct > 0) {
                el.textContent = Math.round(pct * 100) + '%';
            } else if (location.start.displayed) {
                el.textContent = 'Pag. ' + location.start.displayed.page + ' de ' + location.start.displayed.total;
            } else {
                el.textContent = '';
            }
        }
    }

    window.goNext = function() {
        if (rendition) rendition.next();
    };
    window.goPrev = function() {
        if (rendition) rendition.prev();
    };

    function handleKeyboard(e) {
        if (e.key === 'ArrowRight' || e.key === 'Right') { window.goNext(); }
        if (e.key === 'ArrowLeft' || e.key === 'Left') { window.goPrev(); }
    }

    window.toggleTOC = function() {
        tocOpen = !tocOpen;
        document.getElementById('epub-toc').classList.toggle('open', tocOpen);
    };

    function closeTOC() {
        tocOpen = false;
        document.getElementById('epub-toc').classList.remove('open');
    }

    window.changeFontSize = function(dir) {
        fontSize = Math.max(60, Math.min(180, fontSize + dir * 10));
        if (rendition) {
            rendition.themes.fontSize(fontSize + '%');
        }
    };

    window.toggleDarkMode = function() {
        isDark = !isDark;
        document.getElementById('epub-reader').classList.toggle('dark', isDark);
        document.getElementById('btn-theme').textContent = isDark ? 'Sol' : 'Luna';
        applyTheme();
    };

    function applyTheme() {
        if (!rendition) return;
        if (isDark) {
            rendition.themes.override('color', '#e0e0e0');
            rendition.themes.override('background', '#1a1a2e');
        } else {
            rendition.themes.override('color', '#333333');
            rendition.themes.override('background', '#ffffff');
        }
        rendition.themes.fontSize(fontSize + '%');
    }

    // Touch swipe support (inside epub.js iframe + outside)
    var touchStartX = 0;
    function onTouchStart(e) {
        touchStartX = (e.changedTouches || [{screenX:0}])[0].screenX;
    }
    function onTouchEnd(e) {
        var diff = (e.changedTouches || [{screenX:0}])[0].screenX - touchStartX;
        if (Math.abs(diff) > 40) {
            if (diff < 0) window.goNext();
            else window.goPrev();
        }
    }
    // Register on main document
    document.addEventListener('touchstart', onTouchStart, { passive: true });
    document.addEventListener('touchend', onTouchEnd, { passive: true });

    // Also register inside epub.js iframe content (where touch actually happens)
    function registerSwipeInContent(contents) {
        var doc = contents.document;
        doc.addEventListener('touchstart', onTouchStart, { passive: true });
        doc.addEventListener('touchend', onTouchEnd, { passive: true });
    }

})();
</script>
<?php endif; ?>

</body>
</html>
