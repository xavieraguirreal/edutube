<?php
$itemId = trim($_GET['id'] ?? '');
$title = 'Leer — EduTube';
$bookData = null;

if ($itemId) {
    try {
        require_once __DIR__ . '/config.php';
        $db = getDB();
        // id comes as 'ia:gutenberg_XXXX' format
        $slug = preg_replace('/^ia:/', '', $itemId);
        $stmt = $db->prepare("SELECT * FROM contenido_ia WHERE slug = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$slug]);
        $bookData = $stmt->fetch();
        if ($bookData) {
            $title = $bookData['titulo'] . ' — EduTube';
        }
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        img-src 'self' https://www.gutenberg.org https://*.gutenberg.org https://archive.org https://*.archive.org;
        style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
        font-src https://fonts.gstatic.com;
        script-src 'self' 'unsafe-inline';
        connect-src 'self';
    ">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">
    <link rel="stylesheet" href="style.css">
    <style>
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
        .reader-frame { width:100%; height:80vh; border:1px solid #e0e0e0; border-radius:8px; margin-top:1rem; }
        @media (max-width:600px) {
            .reader-header { flex-direction:column; align-items:center; text-align:center; }
            .reader-cover { width:120px; }
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

<main class="main" style="padding-top:60px;">
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
                    <?php if ($bookData['url_contenido']): ?>
                        <button class="btn btn-read" id="btn-read" onclick="loadBook()">Leer ahora</button>
                    <?php endif; ?>
                    <a href="libros" class="btn btn-back">← Volver a Libros</a>
                </div>
            </div>
        </div>
        <?php if ($bookData['descripcion']): ?>
            <div style="color:var(--text-secondary);font-size:0.9rem;line-height:1.6;margin-bottom:1rem;">
                <?= nl2br(htmlspecialchars(mb_substr($bookData['descripcion'], 0, 500))) ?>
            </div>
        <?php endif; ?>
        <?php if ($bookData['url_contenido']):
            // Fetch book content via proxy (Gutenberg blocks iframes)
            $bookUrl = $bookData['url_contenido'];
            // Prefer text/plain URL for cleaner reading
            $gutenbergId = intval(str_replace('gutenberg_', '', $bookData['ia_id']));
            $textUrl = 'https://www.gutenberg.org/ebooks/' . $gutenbergId . '.txt.utf-8';
            $htmlUrl = str_replace('http://', 'https://', $bookUrl);
        ?>
            <div id="book-content" style="display:none;background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:2rem;margin-top:1rem;max-height:80vh;overflow-y:auto;font-family:Georgia,serif;font-size:1.05rem;line-height:1.8;color:#333;">
                <div style="text-align:center;padding:2rem;color:#888;">Cargando libro...</div>
            </div>
            <script>
            function loadBook() {
                var contentDiv = document.getElementById('book-content');
                var btn = document.getElementById('btn-read');
                contentDiv.style.display = '';
                btn.textContent = 'Cargando...';
                btn.disabled = true;
                fetch('api.php?action=proxy_gutenberg&id=<?= $gutenbergId ?>')
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        btn.style.display = 'none';
                        if (data.error) {
                            contentDiv.innerHTML = '<p style="text-align:center;color:#888;">' + data.error + '<br><a href="<?= htmlspecialchars($htmlUrl) ?>" target="_blank" style="color:#0077b6;">Leer en Proyecto Gutenberg →</a></p>';
                            return;
                        }
                        contentDiv.innerHTML = data.html;
                        contentDiv.scrollTop = 0;
                    })
                    .catch(function() {
                        btn.style.display = 'none';
                        contentDiv.innerHTML = '<p style="text-align:center;color:#888;">Error al cargar. <a href="<?= htmlspecialchars($htmlUrl) ?>" target="_blank" style="color:#0077b6;">Leer en Proyecto Gutenberg →</a></p>';
                    });
            }
            </script>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div style="padding:4rem;text-align:center;">
        <h2 style="color:var(--text-muted);">Libro no encontrado</h2>
        <a href="libros" class="btn btn-back" style="margin-top:1rem;display:inline-block;padding:0.5rem 1rem;border:1px solid #ddd;border-radius:8px;color:#555;text-decoration:none;">← Volver a Libros</a>
    </div>
<?php endif; ?>
</main>

</body>
</html>
