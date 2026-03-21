<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTube — Plataforma de Videos Educativos</title>
    <link rel="icon" type="image/png" href="loguito-edutube.png">

    <!-- SEO -->
    <meta name="description" content="EduTube es una plataforma de videos educativos diseñada para contextos con restricciones de acceso a redes sociales. Contenido curado, sin comentarios, sin algoritmos. Solo educación.">
    <meta name="keywords" content="videos educativos, plataforma educativa, educación en contextos de encierro, Universidad Liberté, videos curados">
    <meta name="author" content="Universidad Liberté">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="EduTube — Plataforma de Videos Educativos">
    <meta property="og:description" content="Contenido audiovisual educativo curado para contextos con restricciones de acceso a redes sociales. Sin comentarios, sin algoritmos. Solo educación.">
    <meta property="og:image" content="https://edutube.universidadliberte.org/loguito-edutube.png">
    <meta property="og:url" content="https://edutube.universidadliberte.org">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="EduTube">
    <meta property="og:locale" content="es_AR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="EduTube — Plataforma de Videos Educativos">
    <meta name="twitter:description" content="Contenido audiovisual educativo curado. Sin comentarios, sin algoritmos. Solo educación.">
    <meta name="twitter:image" content="https://edutube.universidadliberte.org/loguito-edutube.png">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green: #2e8b47;
            --green-dark: #1f6e34;
            --green-light: #38a555;
            --bg: #fafafa;
            --text: #1a1a1a;
            --text-light: #555;
            --card-bg: #fff;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ── Hero ── */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            background: #fff;
            color: var(--text);
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
        }

        .hero-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .hero-logo img {
            width: 64px;
            height: auto;
        }

        .hero-logo span {
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--green);
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.25rem;
        }

        .hero h1 em {
            font-style: normal;
            color: var(--text-light);
        }

        .hero p {
            font-size: 1.15rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
        }

        .hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2.5rem;
            background: var(--green);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 50px;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .hero-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }

        .hero-scroll {
            position: absolute;
            bottom: 2rem;
            z-index: 1;
            color: var(--text-light);
            font-size: 0.8rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(8px); }
        }

        /* ── Sections ── */
        section {
            padding: 5rem 2rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--green);
            margin-bottom: 0.75rem;
        }

        section h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        section .subtitle {
            font-size: 1.05rem;
            color: var(--text-light);
            max-width: 700px;
            margin-bottom: 3rem;
            line-height: 1.7;
        }

        /* ── Features grid ── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid #e8e8e8;
            border-radius: 16px;
            padding: 2rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--green), var(--green-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .feature-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: var(--text-light);
            line-height: 1.6;
        }

        /* ── Not a social network ── */
        .comparison {
            background: #f0f7f2;
            border-radius: 20px;
            padding: 3rem;
            margin-top: 2rem;
        }

        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .comp-col h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .comp-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .comp-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-light);
            line-height: 1.5;
        }

        .comp-list li .icon {
            flex-shrink: 0;
            font-size: 1rem;
            margin-top: 1px;
        }

        /* ── Compliance ── */
        .compliance-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .compliance-card {
            background: var(--card-bg);
            border: 1px solid #e8e8e8;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
        }

        .compliance-card .cc-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .compliance-card h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .compliance-card p {
            font-size: 0.82rem;
            color: var(--text-light);
            line-height: 1.5;
        }

        /* ── Footer ── */
        .landing-footer {
            background: #111;
            color: rgba(255,255,255,0.6);
            text-align: center;
            padding: 3rem 2rem;
        }

        .landing-footer .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .landing-footer .footer-logo img {
            width: 28px;
            filter: brightness(0) invert(1) opacity(0.6);
        }

        .landing-footer .footer-logo span {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--green);
        }

        .landing-footer p {
            font-size: 0.8rem;
            line-height: 1.7;
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
            padding: 0.7rem 2rem;
            background: var(--green);
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            margin-top: 1.5rem;
            transition: background 0.2s;
        }

        .footer-cta:hover { background: var(--green-light); }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .hero h1 { font-size: 1.75rem; }
            .hero-logo span { font-size: 2rem; }
            .hero-logo img { width: 44px; }
            .hero p { font-size: 1rem; }
            section { padding: 3rem 1.25rem; }
            section h2 { font-size: 1.5rem; }
            .comparison { padding: 1.5rem; }
            .comparison-grid { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="hero">
    <div class="hero-content">
        <div class="hero-logo">
            <img src="loguito-edutube.png" alt="EduTube">
            <span>EduTube</span>
        </div>
        <h1>Educación audiovisual <em>sin barreras</em></h1>
        <p>Una plataforma de videos educativos curados, diseñada para contextos donde el acceso a redes sociales está restringido. Sin comentarios, sin algoritmos, sin distracciones. Solo contenido que transforma.</p>
        <a href="/" class="hero-cta" id="enter-btn">
            Ingresar a EduTube →
        </a>
    </div>
    <div class="hero-scroll">
        Conocé más ↓
    </div>
</div>

<section>
    <div class="section-label">¿Qué es EduTube?</div>
    <h2>Contenido educativo curado en un entorno seguro</h2>
    <p class="subtitle">EduTube permite acceder a videos educativos y culturales seleccionados por equipos pedagógicos, sin exponer a los usuarios a las funcionalidades de red social de las plataformas de origen.</p>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🎓</div>
            <h3>Contenido seleccionado</h3>
            <p>Cada canal o lista de videos es revisada y aprobada por administradores/as antes de estar disponible. No hay contenido aleatorio ni recomendaciones algorítmicas.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Entorno seguro</h3>
            <p>Los usuarios solo pueden ver los videos aprobados. No pueden navegar libremente, buscar fuera del catálogo ni acceder a contenido externo.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📱</div>
            <h3>Acceso desde cualquier dispositivo</h3>
            <p>Funciona en celulares, tablets y computadoras. Diseño adaptable que optimiza la experiencia en cada pantalla.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🚫</div>
            <h3>Sin interacción social</h3>
            <p>No hay comentarios, no hay likes públicos, no hay mensajes entre usuarios/as. La plataforma es unidireccional: solo reproducción de contenido.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>Reproductor propio</h3>
            <p>Controles de reproducción diseñados por nosotros/as. Sin enlaces a plataformas externas, sin branding de terceros/as, sin redirecciones.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Organización pedagógica</h3>
            <p>Videos organizados por categorías, canales y listas de reproducción temáticas. Estructura pensada para el acompañamiento educativo.</p>
        </div>
    </div>
</section>

<section>
    <div class="section-label">Aclaración normativa</div>
    <h2>EduTube no es una red social</h2>
    <p class="subtitle">En contextos donde las redes sociales están restringidas, es importante clarificar por qué EduTube no se encuadra en esa categoría.</p>

    <div class="comparison">
        <div class="comparison-grid">
            <div class="comp-col">
                <h3>🚫 Lo que NO tiene EduTube</h3>
                <ul class="comp-list">
                    <li><span class="icon">✕</span> No tiene registro de usuarios/as ni perfiles públicos</li>
                    <li><span class="icon">✕</span> No tiene comentarios ni sistema de mensajería</li>
                    <li><span class="icon">✕</span> No permite subir contenido por parte de los/as usuarios/as</li>
                    <li><span class="icon">✕</span> No tiene sistema de seguidores/as ni suscripciones sociales</li>
                    <li><span class="icon">✕</span> No tiene feed algorítmico ni recomendaciones personalizadas</li>
                    <li><span class="icon">✕</span> No tiene likes, shares ni reacciones visibles a otros/as</li>
                    <li><span class="icon">✕</span> No permite interacción entre usuarios/as de ningún tipo</li>
                </ul>
            </div>
            <div class="comp-col">
                <h3>✅ Lo que SÍ tiene EduTube</h3>
                <ul class="comp-list">
                    <li><span class="icon">✓</span> Catálogo seleccionado por administradores/as</li>
                    <li><span class="icon">✓</span> Reproductor integrado con controles propios</li>
                    <li><span class="icon">✓</span> Organización por categorías y canales temáticos</li>
                    <li><span class="icon">✓</span> Búsqueda dentro del catálogo aprobado</li>
                    <li><span class="icon">✓</span> Listas de reproducción pedagógicas</li>
                    <li><span class="icon">✓</span> Funciones locales de uso personal (historial, favoritos)</li>
                    <li><span class="icon">✓</span> Regulación por protocolo institucional</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="section-label">Cumplimiento normativo</div>
    <h2>Respeto a las políticas de YouTube</h2>
    <p class="subtitle">EduTube utiliza exclusivamente los mecanismos oficiales provistos por YouTube, respetando todas sus políticas de uso.</p>

    <div class="compliance-cards">
        <div class="compliance-card">
            <div class="cc-icon">📜</div>
            <h4>Términos de Servicio</h4>
            <p>Se utiliza el mecanismo oficial de YouTube (API), permitido explícitamente en sus Términos de Servicio.</p>
        </div>
        <div class="compliance-card">
            <div class="cc-icon">🔐</div>
            <h4>Privacidad mejorada</h4>
            <p>Se usa la versión privada mejorada de dominio que YouTube ofrece para reproducciones sin tracking.</p>
        </div>
        <div class="compliance-card">
            <div class="cc-icon">📈</div>
            <h4>Vistas contabilizadas</h4>
            <p>Las reproducciones en EduTube se contabilizan como vistas legítimas en YouTube, beneficiando a los/as creadores/as de contenido.</p>
        </div>
        <div class="compliance-card">
            <div class="cc-icon">🚫</div>
            <h4>Sin descarga ni redistribución</h4>
            <p>No se descargan ni almacenan videos. Todo el contenido se reproduce en tiempo real desde los servidores de YouTube.</p>
        </div>
    </div>
</section>

<footer class="landing-footer">
    <div class="footer-logo">
        <img src="loguito-edutube.png" alt="">
        <span>EduTube</span>
    </div>
    <p>
        Plataforma desarrollada por <strong>VERUMax</strong> para la Universidad Liberté, solicitado por el Comité de Convivencia Mario Juliano.<br>
        Provincia de Buenos Aires, Argentina.
    </p>
    <a href="/" class="footer-cta" id="enter-btn-footer">Ingresar a EduTube →</a>
</footer>

<script>
// Marcar como visitado al hacer clic en "Ingresar"
document.getElementById('enter-btn').addEventListener('click', function() {
    localStorage.setItem('edutube_welcomed', '1');
});
document.getElementById('enter-btn-footer').addEventListener('click', function() {
    localStorage.setItem('edutube_welcomed', '1');
});
</script>

</body>
</html>
