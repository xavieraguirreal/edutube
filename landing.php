<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTube — Plataforma Educativa y Cultural</title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">

    <!-- SEO -->
    <meta name="description" content="EduTube es una plataforma educativa y cultural con videos, cine, audiolibros y libros. Contenido curado para contextos con restricciones de acceso a redes sociales. Sin algoritmos, sin publicidad. Solo educación y cultura.">
    <meta name="keywords" content="plataforma educativa, videos educativos, cine de dominio público, audiolibros, libros digitales, educación en contextos de encierro, Comité de Convivencia Mario Juliano">
    <meta name="author" content="Comité de Convivencia Mario Juliano">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="EduTube — Plataforma Educativa y Cultural">
    <meta property="og:description" content="Videos educativos, cine, audiolibros y libros digitales en un entorno seguro y sin distracciones. Contenido curado para la educación y la cultura.">
    <meta property="og:image" content="https://edutube.universidadliberte.org/loguito-edutube.png">
    <meta property="og:url" content="https://edutube.universidadliberte.org">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="EduTube">
    <meta property="og:locale" content="es_AR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="EduTube — Plataforma Educativa y Cultural">
    <meta name="twitter:description" content="Videos, cine, audiolibros y libros digitales curados. Sin algoritmos, sin publicidad. Solo educación y cultura.">
    <meta name="twitter:image" content="https://edutube.universidadliberte.org/loguito-edutube.png">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green: #2e8b47;
            --green-dark: #1f6e34;
            --green-light: #38a555;
            --red: #e63946;
            --purple: #6a4c93;
            --blue: #0077b6;
            --bg: #fafafa;
            --text: #1a1a1a;
            --text-light: #555;
            --card-bg: #fff;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ── Animations ── */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(10px); }
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -20px) rotate(120deg); }
            66% { transform: translate(-20px, 15px) rotate(240deg); }
        }

        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(-25px, 25px) rotate(-120deg); }
            66% { transform: translate(20px, -10px) rotate(-240deg); }
        }

        @keyframes float3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(15px, -30px) scale(1.1); }
        }

        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
        .reveal-delay-5 { transition-delay: 0.5s; }

        /* ── Hero ── */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            background: linear-gradient(-45deg, #f0f7f2, #ffffff, #e8f5ee, #f5faf7, #dff0e6);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        /* Floating shapes */
        .hero-shapes {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .hero-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.07;
        }

        .hero-shape:nth-child(1) {
            width: 400px;
            height: 400px;
            background: var(--green);
            top: -100px;
            right: -100px;
            animation: float1 20s ease-in-out infinite;
        }

        .hero-shape:nth-child(2) {
            width: 300px;
            height: 300px;
            background: var(--blue);
            bottom: -80px;
            left: -80px;
            animation: float2 18s ease-in-out infinite;
        }

        .hero-shape:nth-child(3) {
            width: 200px;
            height: 200px;
            background: var(--purple);
            top: 40%;
            left: 10%;
            animation: float3 22s ease-in-out infinite;
        }

        .hero-shape:nth-child(4) {
            width: 150px;
            height: 150px;
            background: var(--red);
            top: 20%;
            right: 15%;
            animation: float2 25s ease-in-out infinite reverse;
        }

        .hero-shape:nth-child(5) {
            width: 250px;
            height: 250px;
            background: var(--green-light);
            bottom: 10%;
            right: -50px;
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: float1 30s ease-in-out infinite reverse;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            animation: fadeInUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .hero-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
        }

        .hero-logo img {
            width: 72px;
            height: auto;
        }

        .hero-logo span {
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -1.5px;
            color: var(--green);
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1.5rem;
            letter-spacing: -0.5px;
        }

        .hero h1 em {
            font-style: normal;
            background: linear-gradient(135deg, var(--green), var(--blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.2rem;
            color: var(--text-light);
            max-width: 620px;
            margin: 0 auto 3rem;
            line-height: 1.8;
        }

        .hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1.1rem 2.8rem;
            background: var(--green);
            color: #fff;
            font-size: 1.15rem;
            font-weight: 700;
            border-radius: 50px;
            text-decoration: none;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        background 0.3s;
            box-shadow: 0 4px 20px rgba(46, 139, 71, 0.3);
        }

        .hero-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 35px rgba(46, 139, 71, 0.4);
            background: var(--green-dark);
        }

        .hero-scroll {
            position: absolute;
            bottom: 2.5rem;
            z-index: 1;
            color: var(--text-light);
            font-size: 0.8rem;
            font-weight: 500;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            animation: fadeIn 1s 1.2s both, bounce 2.5s 2s infinite;
            cursor: pointer;
            text-decoration: none;
        }

        .scroll-arrow {
            width: 24px;
            height: 24px;
            border-right: 2px solid var(--text-light);
            border-bottom: 2px solid var(--text-light);
            transform: rotate(45deg);
            opacity: 0.5;
        }

        /* ── Sections ── */
        .section-wrap {
            padding: 6rem 2rem;
            max-width: 1140px;
            margin: 0 auto;
        }

        .section-alt {
            background: #f5f9f6;
        }

        .section-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--green);
            margin-bottom: 0.75rem;
        }

        .section-wrap h2 {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
            letter-spacing: -0.3px;
        }

        .section-wrap .subtitle {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 700px;
            margin-bottom: 3.5rem;
            line-height: 1.8;
        }

        /* ── Features grid ── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid #e8e8e8;
            border-radius: 16px;
            padding: 2rem;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--green), var(--green-light));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .feature-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: var(--text-light);
            line-height: 1.7;
        }

        /* ── Content cards ── */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.75rem;
        }

        .content-card {
            border-radius: 20px;
            overflow: hidden;
            background: var(--card-bg);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .content-card:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 16px 50px rgba(0, 0, 0, 0.12);
        }

        .content-card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .content-card-body {
            padding: 1.75rem;
        }

        .content-card-tag {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            color: #fff;
            margin-bottom: 0.75rem;
        }

        .content-card-body h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .content-card-body p {
            font-size: 0.9rem;
            color: var(--text-light);
            line-height: 1.7;
        }

        /* ── Comparison ── */
        .comparison {
            background: #fff;
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid #e0e8e2;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }

        .comp-col h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .comp-col:first-child h3 {
            color: #c0392b;
        }

        .comp-col:last-child h3 {
            color: var(--green);
        }

        .comp-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .comp-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            font-size: 0.9rem;
            color: var(--text-light);
            line-height: 1.6;
        }

        .comp-list li .icon {
            flex-shrink: 0;
            font-size: 0.85rem;
            font-weight: 700;
            margin-top: 2px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .comp-col:first-child .icon {
            background: #fdeaea;
            color: #c0392b;
        }

        .comp-col:last-child .icon {
            background: #e8f5ee;
            color: var(--green);
        }

        /* ── Sources ── */
        .sources-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .source-card {
            background: var(--card-bg);
            border: 1px solid #e8e8e8;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .source-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
        }

        .source-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .source-card h4 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .source-card p {
            font-size: 0.85rem;
            color: var(--text-light);
            line-height: 1.7;
        }

        .source-badge {
            display: inline-block;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.25rem 0.6rem;
            border-radius: 50px;
            margin-top: 0.75rem;
        }

        /* ── Footer ── */
        .landing-footer {
            background: #111;
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            padding: 4rem 2rem;
        }

        .landing-footer .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .landing-footer .footer-logo img {
            width: 32px;
            filter: brightness(0) invert(1) opacity(0.6);
        }

        .landing-footer .footer-logo span {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--green);
        }

        .landing-footer p {
            font-size: 0.85rem;
            line-height: 1.8;
            max-width: 500px;
            margin: 0 auto;
        }

        .landing-footer a {
            color: var(--green-light);
            text-decoration: none;
        }

        .footer-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.8rem 2.2rem;
            background: var(--green);
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 50px;
            text-decoration: none;
            margin-top: 2rem;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        background 0.3s;
            box-shadow: 0 4px 20px rgba(46, 139, 71, 0.3);
        }

        .footer-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(46, 139, 71, 0.4);
            background: var(--green-light);
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .sources-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            .hero-logo span { font-size: 2.5rem; }
            .hero-logo img { width: 52px; }
            .hero p { font-size: 1.05rem; }

            .section-wrap { padding: 4rem 1.25rem; }
            .section-wrap h2 { font-size: 1.65rem; }
            .section-wrap .subtitle { font-size: 1rem; margin-bottom: 2.5rem; }

            .features-grid { grid-template-columns: 1fr; }
            .content-grid { grid-template-columns: 1fr; }
            .sources-grid { grid-template-columns: 1fr; }

            .comparison { padding: 1.75rem; }
            .comparison-grid { grid-template-columns: 1fr; gap: 2rem; }

            .content-card-img { height: 180px; }
        }

        @media (max-width: 480px) {
            .hero { padding: 1.5rem; }
            .hero h1 { font-size: 1.65rem; }
            .hero-logo span { font-size: 2rem; }
            .hero-logo img { width: 44px; }
            .hero p { font-size: 0.95rem; }
            .hero-cta { padding: 0.9rem 2rem; font-size: 1rem; }

            .section-wrap { padding: 3rem 1rem; }
            .section-wrap h2 { font-size: 1.4rem; }

            .feature-card { padding: 1.5rem; }
            .content-card-body { padding: 1.25rem; }
            .comparison { padding: 1.25rem; }
        }
    </style>
</head>
<body>

<!-- ════════════ HERO ════════════ -->
<div class="hero">
    <div class="hero-shapes">
        <div class="hero-shape"></div>
        <div class="hero-shape"></div>
        <div class="hero-shape"></div>
        <div class="hero-shape"></div>
        <div class="hero-shape"></div>
    </div>
    <div class="hero-content">
        <div class="hero-logo">
            <img src="loguito-edutube.png" alt="EduTube">
            <span>EduTube</span>
        </div>
        <h1>Educacion y cultura <em>sin barreras</em></h1>
        <p>Videos educativos, peliculas, audiolibros y libros digitales en un entorno seguro y sin distracciones. Contenido curado para aprender, descubrir y crecer.</p>
        <a href="/" class="hero-cta" id="enter-btn">
            Explorar EduTube &rarr;
        </a>
    </div>
    <a href="#que-es" class="hero-scroll">
        <span>Conoce mas</span>
        <div class="scroll-arrow"></div>
    </a>
</div>

<!-- ════════════ QUE ES ════════════ -->
<section id="que-es">
    <div class="section-wrap">
        <div class="reveal">
            <div class="section-label">Plataforma</div>
            <h2>Que es EduTube?</h2>
            <p class="subtitle">Una plataforma educativa y cultural que reune videos, cine, audiolibros y libros en un entorno controlado. Cada contenido es seleccionado por equipos pedagogicos, sin algoritmos ni publicidad.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card reveal reveal-delay-1">
                <div class="feature-icon">&#127891;</div>
                <h3>Contenido curado</h3>
                <p>Cada video, pelicula, audiolibro y libro es revisado y aprobado por administradores antes de estar disponible. No hay contenido aleatorio.</p>
            </div>
            <div class="feature-card reveal reveal-delay-2">
                <div class="feature-icon">&#128274;</div>
                <h3>Entorno seguro</h3>
                <p>Los usuarios solo acceden al catalogo aprobado. No hay navegacion libre, ni enlaces externos, ni funciones de red social.</p>
            </div>
            <div class="feature-card reveal reveal-delay-3">
                <div class="feature-icon">&#127916;</div>
                <h3>Multiformato</h3>
                <p>Videos educativos, peliculas y documentales, audiolibros narrados y libros digitales. Cuatro tipos de contenido en una sola plataforma.</p>
            </div>
            <div class="feature-card reveal reveal-delay-1">
                <div class="feature-icon">&#128241;</div>
                <h3>Cualquier dispositivo</h3>
                <p>Funciona en celulares, tablets y computadoras. Diseno adaptable que optimiza la experiencia en cada pantalla.</p>
            </div>
            <div class="feature-card reveal reveal-delay-2">
                <div class="feature-icon">&#128683;</div>
                <h3>Sin publicidad ni algoritmos</h3>
                <p>No hay anuncios, no hay recomendaciones algoritmicas, no hay tracking. La experiencia es limpia y enfocada en el contenido.</p>
            </div>
            <div class="feature-card reveal reveal-delay-3">
                <div class="feature-icon">&#128218;</div>
                <h3>Organizacion pedagogica</h3>
                <p>Contenido organizado por categorias, canales y colecciones tematicas. Estructura pensada para el acompanamiento educativo.</p>
            </div>
        </div>
    </div>
</section>

<!-- ════════════ CONTENIDO ════════════ -->
<section class="section-alt">
    <div class="section-wrap">
        <div class="reveal">
            <div class="section-label">Catalogo</div>
            <h2>Nuestro contenido</h2>
            <p class="subtitle">Cuatro secciones con contenido educativo y cultural de fuentes confiables, seleccionado para enriquecer la experiencia de aprendizaje.</p>
        </div>

        <div class="content-grid">
            <a href="/videos.php" class="content-card reveal reveal-delay-1">
                <img class="content-card-img" src="img/card-videos.jpg" alt="Videos educativos">
                <div class="content-card-body">
                    <span class="content-card-tag" style="background: var(--green);">Videos</span>
                    <h3>Videos educativos seleccionados</h3>
                    <p>Canales de YouTube curados con contenido educativo, cultural y formativo. Reproducidos en un entorno seguro con controles propios.</p>
                </div>
            </a>
            <a href="/ia.php" class="content-card reveal reveal-delay-2">
                <img class="content-card-img" src="img/card-cine.jpg" alt="Cine y documentales">
                <div class="content-card-body">
                    <span class="content-card-tag" style="background: var(--red);">Cine</span>
                    <h3>Peliculas y documentales de dominio publico</h3>
                    <p>Cine clasico, documentales historicos y cortometrajes del Internet Archive. Disponibles de forma libre y gratuita.</p>
                </div>
            </a>
            <a href="/audiolibros.php" class="content-card reveal reveal-delay-3">
                <img class="content-card-img" src="img/card-audiolibros.jpg" alt="Audiolibros">
                <div class="content-card-body">
                    <span class="content-card-tag" style="background: var(--purple);">Audiolibros</span>
                    <h3>Audiolibros clasicos narrados</h3>
                    <p>Obras literarias narradas por voluntarios de LibriVox e Internet Archive. Escucha clasicos de la literatura universal.</p>
                </div>
            </a>
            <a href="/libros.php" class="content-card reveal reveal-delay-4">
                <img class="content-card-img" src="img/card-libros.jpg" alt="Libros digitales">
                <div class="content-card-body">
                    <span class="content-card-tag" style="background: var(--blue);">Libros</span>
                    <h3>Biblioteca digital de obras clasicas</h3>
                    <p>Libros electronicos del Proyecto Gutenberg. Miles de obras de dominio publico para leer directamente en la plataforma.</p>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- ════════════ NO ES RED SOCIAL ════════════ -->
<section>
    <div class="section-wrap">
        <div class="reveal">
            <div class="section-label">Aclaracion normativa</div>
            <h2>EduTube no es una red social</h2>
            <p class="subtitle">En contextos donde las redes sociales estan restringidas, es importante clarificar por que EduTube no se encuadra en esa categoria.</p>
        </div>

        <div class="comparison reveal">
            <div class="comparison-grid">
                <div class="comp-col">
                    <h3>Lo que NO tiene EduTube</h3>
                    <ul class="comp-list">
                        <li><span class="icon">&times;</span> No tiene registro de usuarios ni perfiles publicos</li>
                        <li><span class="icon">&times;</span> No tiene comentarios ni sistema de mensajeria</li>
                        <li><span class="icon">&times;</span> No permite subir contenido por parte de los usuarios</li>
                        <li><span class="icon">&times;</span> No tiene sistema de seguidores ni suscripciones sociales</li>
                        <li><span class="icon">&times;</span> No tiene feed algoritmico ni recomendaciones personalizadas</li>
                        <li><span class="icon">&times;</span> No tiene likes, shares ni reacciones visibles a otros</li>
                        <li><span class="icon">&times;</span> No permite interaccion entre usuarios de ningun tipo</li>
                    </ul>
                </div>
                <div class="comp-col">
                    <h3>Lo que SI tiene EduTube</h3>
                    <ul class="comp-list">
                        <li><span class="icon">&#10003;</span> Catalogo seleccionado por administradores</li>
                        <li><span class="icon">&#10003;</span> Reproductor integrado con controles propios</li>
                        <li><span class="icon">&#10003;</span> Organizacion por categorias y colecciones tematicas</li>
                        <li><span class="icon">&#10003;</span> Busqueda dentro del catalogo aprobado</li>
                        <li><span class="icon">&#10003;</span> Cuatro formatos: videos, cine, audiolibros y libros</li>
                        <li><span class="icon">&#10003;</span> Funciones locales de uso personal (historial, favoritos)</li>
                        <li><span class="icon">&#10003;</span> Regulacion por protocolo institucional</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════════════ FUENTES ════════════ -->
<section class="section-alt">
    <div class="section-wrap">
        <div class="reveal">
            <div class="section-label">Fuentes</div>
            <h2>Fuentes de contenido</h2>
            <p class="subtitle">Todo el contenido de EduTube proviene de fuentes reconocidas y es curado por administradores antes de publicarse.</p>
        </div>

        <div class="sources-grid">
            <div class="source-card reveal reveal-delay-1">
                <div class="source-icon">&#9654;&#65039;</div>
                <h4>YouTube</h4>
                <p>Videos educativos reproducidos a traves de la API oficial de YouTube en modo de privacidad mejorada.</p>
                <span class="source-badge" style="background: #e8f5ee; color: var(--green);">Videos educativos</span>
            </div>
            <div class="source-card reveal reveal-delay-2">
                <div class="source-icon">&#127902;&#65039;</div>
                <h4>Internet Archive</h4>
                <p>Peliculas, documentales y audiolibros de dominio publico. La biblioteca digital mas grande del mundo.</p>
                <span class="source-badge" style="background: #fce4e6; color: var(--red);">Cine y audiolibros</span>
            </div>
            <div class="source-card reveal reveal-delay-3">
                <div class="source-icon">&#128214;</div>
                <h4>Proyecto Gutenberg</h4>
                <p>Mas de 70.000 libros electronicos de dominio publico. Obras clasicas de la literatura universal disponibles de forma gratuita.</p>
                <span class="source-badge" style="background: #e0f0ff; color: var(--blue);">Libros digitales</span>
            </div>
        </div>
    </div>
</section>

<!-- ════════════ FOOTER ════════════ -->
<footer class="landing-footer">
    <div class="footer-logo">
        <img src="loguito-edutube.png" alt="">
        <span>EduTube</span>
    </div>
    <p>
        Desarrollado por <strong>VERUMax</strong> para el Comite de Convivencia Mario Juliano.<br>
        Provincia de Buenos Aires, Argentina.
    </p>
    <a href="/" class="footer-cta" id="enter-btn-footer">Explorar EduTube &rarr;</a>
</footer>

<script>
// ── Scroll-triggered reveal animations ──
(function() {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px'
    });

    document.querySelectorAll('.reveal').forEach(function(el) {
        observer.observe(el);
    });
})();

// ── Welcome flag ──
document.getElementById('enter-btn').addEventListener('click', function() {
    localStorage.setItem('edutube_welcomed', '1');
});
document.getElementById('enter-btn-footer').addEventListener('click', function() {
    localStorage.setItem('edutube_welcomed', '1');
});
</script>

</body>
</html>
