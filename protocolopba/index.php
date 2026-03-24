<?php
$title = 'Protocolo de Uso de Celulares — EduTube';
$description = 'Protocolo para el uso de dispositivos celulares por parte de personas privadas de la libertad en la Provincia de Buenos Aires.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="icon" type="image/png" href="../loguito-edutube.png">
    <meta name="description" content="<?= $description ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --primary: #1e3a8a;
            --primary-light: #3b82f6;
            --accent: #2e8b47;
            --bg: #fafafa;
            --text: #1a1a1a;
            --text-light: #555;
            --card-bg: #fff;
        }
        body { font-family:'Inter',-apple-system,sans-serif; background:var(--bg); color:var(--text); line-height:1.7; }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
            color:#fff; padding:4rem 2rem; text-align:center; position:relative; overflow:hidden;
        }
        .hero::before {
            content:''; position:absolute; top:-50%; left:-50%; width:200%; height:200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }
        @keyframes pulse { 0%,100%{transform:scale(1);} 50%{transform:scale(1.1);} }
        .hero-content { position:relative; z-index:1; max-width:800px; margin:0 auto; }
        .hero-badge { display:inline-block; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); border-radius:50px; padding:0.4rem 1.2rem; font-size:0.8rem; font-weight:600; letter-spacing:1px; text-transform:uppercase; margin-bottom:1.5rem; }
        .hero h1 { font-size:2.2rem; font-weight:800; line-height:1.2; margin-bottom:1rem; }
        .hero p { font-size:1.05rem; opacity:0.9; max-width:600px; margin:0 auto 2rem; }
        .hero-actions { display:flex; gap:1rem; justify-content:center; flex-wrap:wrap; }
        .btn { display:inline-flex; align-items:center; gap:0.4rem; padding:0.75rem 1.5rem; border-radius:50px; font-weight:600; font-size:0.95rem; text-decoration:none; transition:transform 0.2s, box-shadow 0.2s; border:none; cursor:pointer; font-family:inherit; }
        .btn:hover { transform:translateY(-2px); }
        .btn-white { background:#fff; color:var(--primary); box-shadow:0 4px 15px rgba(0,0,0,0.2); }
        .btn-outline { background:none; border:2px solid rgba(255,255,255,0.5); color:#fff; }
        .btn-outline:hover { border-color:#fff; background:rgba(255,255,255,0.1); }
        .btn-green { background:var(--accent); color:#fff; box-shadow:0 4px 15px rgba(0,0,0,0.2); }

        /* Sections */
        .section { padding:4rem 2rem; max-width:900px; margin:0 auto; }
        .section-alt { background:#f0f4ff; }
        .section-label { font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:var(--primary-light); margin-bottom:0.5rem; }
        .section h2 { font-size:1.8rem; font-weight:700; margin-bottom:1rem; color:var(--primary); }
        .section .subtitle { font-size:1rem; color:var(--text-light); margin-bottom:2rem; }

        /* Articles */
        .article-card {
            background:var(--card-bg); border:1px solid #e0e0e0; border-radius:16px;
            padding:1.75rem; margin-bottom:1.25rem; transition:transform 0.2s, box-shadow 0.2s;
        }
        .article-card:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(0,0,0,0.08); }
        .article-num {
            display:inline-block; background:var(--primary); color:#fff; width:36px; height:36px;
            border-radius:50%; text-align:center; line-height:36px; font-weight:700; font-size:0.85rem;
            margin-right:0.75rem; flex-shrink:0;
        }
        .article-header { display:flex; align-items:center; margin-bottom:0.75rem; }
        .article-title { font-weight:700; font-size:1.05rem; color:var(--primary); }
        .article-body { font-size:0.92rem; color:var(--text-light); line-height:1.8; }
        .article-body ul { margin:0.5rem 0 0.5rem 1.5rem; }
        .article-body li { margin-bottom:0.4rem; }

        /* Key points */
        .key-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1.25rem; margin-bottom:2rem; }
        .key-card { background:var(--card-bg); border-radius:12px; padding:1.25rem; border:1px solid #e0e0e0; text-align:center; }
        .key-icon { font-size:2rem; margin-bottom:0.5rem; }
        .key-card h3 { font-size:0.95rem; font-weight:600; margin-bottom:0.3rem; }
        .key-card p { font-size:0.82rem; color:var(--text-light); }

        /* Important box */
        .important-box {
            background:linear-gradient(135deg, #fef3c7, #fde68a); border:1px solid #f59e0b;
            border-radius:12px; padding:1.5rem; margin:2rem 0;
        }
        .important-box h3 { color:#92400e; font-size:1rem; margin-bottom:0.5rem; }
        .important-box p { color:#78350f; font-size:0.9rem; }

        /* Footer */
        .page-footer { background:#111; color:rgba(255,255,255,0.6); text-align:center; padding:2.5rem 2rem; }
        .page-footer a { color:var(--accent); text-decoration:none; }
        .page-footer .footer-logo { display:flex; align-items:center; justify-content:center; gap:0.5rem; margin-bottom:0.75rem; }
        .page-footer .footer-logo img { width:24px; filter:brightness(0) invert(1) opacity(0.6); }
        .page-footer .footer-logo span { font-weight:700; color:var(--accent); }

        /* Nav bar */
        .topnav { background:#fff; border-bottom:1px solid #e0e0e0; padding:0.75rem 2rem; display:flex; align-items:center; gap:1rem; position:sticky; top:0; z-index:100; }
        .topnav a { text-decoration:none; color:var(--text); font-size:0.88rem; }
        .topnav .logo { display:flex; align-items:center; gap:0.4rem; font-weight:700; color:var(--accent); }
        .topnav .logo img { width:24px; }
        .topnav .sep { color:#ccc; }

        /* Responsive */
        @media (max-width:768px) {
            .hero h1 { font-size:1.6rem; }
            .hero p { font-size:0.95rem; }
            .section { padding:2.5rem 1.25rem; }
            .section h2 { font-size:1.4rem; }
            .key-grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>

<nav class="topnav">
    <a href="/" class="logo">
        <img src="../loguito-edutube.png" alt="">
        <span>EduTube</span>
    </a>
    <span class="sep">|</span>
    <a href="/">Inicio</a>
</nav>

<!-- Hero -->
<div class="hero">
    <div class="hero-content">
        <div class="hero-badge">Provincia de Buenos Aires</div>
        <h1>Protocolo de Uso de Dispositivos Celulares</h1>
        <p>Normativa para el uso de teléfonos celulares por parte de personas privadas de la libertad en el ámbito del Servicio Penitenciario Bonaerense.</p>
        <div class="hero-actions">
            <a href="Protocolo%20de%20Celulares.pdf" target="_blank" class="btn btn-white">Descargar PDF</a>
            <a href="#articulos" class="btn btn-outline">Ver artículos ↓</a>
            <a href="/" class="btn btn-outline">Ir a EduTube</a>
        </div>
    </div>
</div>

<!-- Puntos clave -->
<div class="section">
    <div class="section-label">Resumen</div>
    <h2>Puntos clave del protocolo</h2>
    <p class="subtitle">Lo que necesitás saber sobre el uso autorizado de celulares en contextos de encierro.</p>

    <div class="key-grid">
        <div class="key-card">
            <div class="key-icon">📋</div>
            <h3>Registro obligatorio</h3>
            <p>Cada dispositivo debe ser registrado con marca, modelo, IMEI, chip SIM y datos del titular.</p>
        </div>
        <div class="key-card">
            <div class="key-icon">🏠</div>
            <h3>Solo en alojamiento</h3>
            <p>Las comunicaciones solo pueden realizarse en los recintos de alojamiento.</p>
        </div>
        <div class="key-card">
            <div class="key-icon">🕐</div>
            <h3>A cualquier hora</h3>
            <p>Las comunicaciones a través de celulares pueden tener lugar a cualquier hora del día.</p>
        </div>
        <div class="key-card">
            <div class="key-icon">🚫</div>
            <h3>Sin redes sociales (excepto WhatsApp)</h3>
            <p>Se prohíbe el uso de redes sociales, con excepción de la aplicación WhatsApp.</p>
        </div>
        <div class="key-card">
            <div class="key-icon">🌐</div>
            <h3>Internet con fines educativos</h3>
            <p>El acceso a internet debe limitarse a contenidos educativos, de contacto familiar y situación procesal.</p>
        </div>
        <div class="key-card">
            <div class="key-icon">💻</div>
            <h3>Notebooks y tablets también</h3>
            <p>Se autoriza el ingreso de notebooks, netbooks y tabletas con las mismas condiciones.</p>
        </div>
    </div>

    <div class="important-box">
        <h3>¿Por qué EduTube cumple con el protocolo?</h3>
        <p>El Artículo 7 establece que el acceso a internet debe limitarse a contenidos relacionados con los objetivos del protocolo: contacto familiar, desarrollo educativo y cultural, e información procesal. EduTube es una plataforma de contenido educativo curado que no es una red social — no tiene comentarios, perfiles, mensajería ni algoritmos. Cumple con las restricciones del protocolo al ofrecer únicamente contenido educativo y cultural.</p>
    </div>
</div>

<!-- Artículos -->
<div class="section-alt">
    <div class="section" id="articulos" style="padding-top:3rem;">
        <div class="section-label">Texto completo</div>
        <h2>Artículos del protocolo</h2>
        <p class="subtitle">Subsecretaría de Política Criminal — Dirección Provincial contra el Delito Complejo.</p>

        <div class="article-card">
            <div class="article-header">
                <span class="article-num">1</span>
                <span class="article-title">Ámbito de aplicación</span>
            </div>
            <div class="article-body">
                Se autoriza a la población en contexto de encierro del Servicio Penitenciario Bonaerense (SPB) a mantener comunicaciones a través de teléfonos celulares y al uso de otros dispositivos tecnológicos, de la forma y con los alcances previstos en el presente Protocolo.
            </div>
        </div>

        <div class="article-card">
            <div class="article-header">
                <span class="article-num">2</span>
                <span class="article-title">Vigencia y objetivos</span>
            </div>
            <div class="article-body">
                <p>La habilitación de las comunicaciones a través de dispositivos móviles en el ámbito del SPB regirá mientras dure el aislamiento social preventivo y obligatorio (DNU 297/20) o cualquier otra restricción a la libre circulación de las personas con motivo de la pandemia Covid-19. Los resultados de la puesta en práctica de este Protocolo permitirán evaluar la extensión de su vigencia, así como la modificación y/o ampliación de su contenido.</p>
                <p style="margin-top:0.75rem;">Este Protocolo tiene como objetivo esencial facilitarle a la población privada de la libertad:</p>
                <ul>
                    <li>El contacto con sus familiares y afectos</li>
                    <li>Su desarrollo educativo y cultural</li>
                    <li>El acceso a información relativa a su situación procesal</li>
                </ul>
                <p style="margin-top:0.75rem;">La implementación de este Protocolo debe llevarse adelante con criterios de razonabilidad que permitan un adecuado ejercicio de los derechos reconocidos en el presente y que garanticen los aspectos de seguridad propios de su ámbito de aplicación.</p>
            </div>
        </div>

        <div class="article-card">
            <div class="article-header">
                <span class="article-num">3</span>
                <span class="article-title">Habilitación de dispositivos móviles</span>
            </div>
            <div class="article-body">
                <p><strong>a) Ingreso con celulares:</strong> toda persona privada de la libertad que ingrese a cualquiera de las dependencias del SPB y cuente con un teléfono celular deberá declararlo ante el personal a cargo de su recepción, quien registrará la marca, el modelo, el número de identificación internacional de equipo móvil (IMEI) y el número de tarjeta de módulo de identificación de abonado (Chip o tarjeta SIM). Se registrarán también los datos de la persona que sea propietaria del dispositivo móvil y de la línea telefónica. Quien ingrese el dispositivo deberá expresar que éste no es producto de la comisión de un ilícito. Tras la registración, el dispositivo móvil le será devuelto a la persona detenida, quien no podrá utilizarlo hasta que se le haya brindado alojamiento provisorio o definitivo.</p>
                <p style="margin-top:0.75rem;">El dispositivo móvil quedará registrado a nombre de la persona que lo ingresó al establecimiento, quien será responsable de todas las comunicaciones realizadas desde ese teléfono celular.</p>
                <p style="margin-top:0.75rem;"><strong>b) Provisión de celulares por terceras personas:</strong> las personas privadas de la libertad en el ámbito del SPB que no cuenten con celulares podrán solicitar por escrito a familiares y personas allegadas que le suministren un dispositivo móvil. Una vez tramitado el pedido, la persona que provea el celular podrá acercarlo a la dependencia del SPB que corresponda. Tras su registración, efectuada en idénticos términos que los establecidos en el párrafo anterior, se hará entrega del dispositivo móvil a la persona que lo haya requerido y su uso se regirá por las disposiciones de este Protocolo.</p>
                <p style="margin-top:0.75rem;">El dispositivo móvil quedará registrado a nombre de la persona que lo requirió, quien será también responsable de todas las comunicaciones realizadas desde ese teléfono celular.</p>
                <p style="margin-top:0.75rem;">En caso de traslados a otra dependencia del SPB, la autoridad penitenciaria deberá asegurar que el dispositivo móvil de la persona trasladada sea recibido y registrado en el lugar de recepción de acuerdo con los requisitos de este artículo. En los supuestos de traslados al Hospital y de traslados por requerimiento de autoridad judicial el dispositivo será entregado al personal a cargo de la medida, quien lo apagará y lo restituirá a su responsable al regreso al establecimiento.</p>
                <p style="margin-top:0.75rem;">La administración de las alcaidías y unidades del SPB mantendrá actualizada la lista de dispositivos móviles habilitados en el establecimiento, en la que se dejará constancia de los datos del teléfono celular y de las personas responsables. Esa información se encontrará a disposición permanente de las autoridades judiciales que pudieran requerirla.</p>
            </div>
        </div>

        <div class="article-card">
            <div class="article-header">
                <span class="article-num">4</span>
                <span class="article-title">Condiciones de uso</span>
            </div>
            <div class="article-body">
                <ol type="a">
                    <li>Las comunicaciones mediante teléfono celular sólo podrán realizarse en los recintos de alojamiento. En consecuencia, queda prohibida la utilización de dispositivos móviles en los pabellones, pasillos, espacios educativos, laborales y cualquier ámbito que no sea de alojamiento.</li>
                    <li>Las personas privadas de la libertad que cuenten con teléfonos celulares podrán utilizar también las líneas de telefonía fija del establecimiento penitenciario.</li>
                    <li>En ningún caso el SPB se hará cargo del costo de las comunicaciones.</li>
                    <li>Las comunicaciones a través de teléfonos celulares podrán tener lugar a cualquier hora del día.</li>
                    <li>En caso de situaciones que puedan afectar la seguridad del establecimiento o si la utilización de los teléfonos celulares impidiera el normal desarrollo de actividades o procedimientos en dependencias del SPB, la autoridad penitenciaria podrá solicitar la interrupción de las comunicaciones. Su restablecimiento será inmediatamente posterior a finalizada la acción o el evento que motivó la interrupción.</li>
                    <li>Los dispositivos móviles no podrán contar con memoria extraíble.</li>
                    <li>Ante el extravío o sustracción del dispositivo celular, la persona privada de la libertad responsable deberá denunciar de inmediato esta circunstancia a la autoridad penitenciaria.</li>
                </ol>
            </div>
        </div>

        <div class="article-card">
            <div class="article-header">
                <span class="article-num">5</span>
                <span class="article-title">Incautación. Actuación disciplinaria. Denuncia penal</span>
            </div>
            <div class="article-body">
                La inobservancia reiterada de lo dispuesto en el presente reglamento o la posible participación en un delito penal mediante la utilización del dispositivo móvil dará lugar a la inmediata incautación del teléfono celular y al labrado de las actuaciones administrativas y/o denuncia penal correspondientes. El teléfono incautado será remitido a la Oficina de Instrucción de expedientes Disciplinarios o a la Fiscalía Interviniente, según corresponda.
            </div>
        </div>

        <div class="article-card">
            <div class="article-header">
                <span class="article-num">6</span>
                <span class="article-title">Otros dispositivos tecnológicos</span>
            </div>
            <div class="article-body">
                Se encuentra autorizado el ingreso de ordenadores portátiles (notebook/netbook) y tabletas a las dependencias del SPB. Respecto de estos dispositivos rigen las mismas condiciones de registración y de uso que para los dispositivos celulares.
            </div>
        </div>

        <div class="article-card">
            <div class="article-header">
                <span class="article-num">7</span>
                <span class="article-title">Acceso a internet</span>
            </div>
            <div class="article-body">
                <p>El acceso a internet a través de cualquiera de los dispositivos tecnológicos autorizados deberá limitarse estrictamente a contenidos que guarden relación con los objetivos de este Protocolo, establecidos en su artículo 2.</p>
                <p style="margin-top:0.75rem;">Se encuentra prohibido el uso de redes sociales, con excepción de la aplicación WhatsApp.</p>
                <p style="margin-top:0.75rem;">Si la persona detenida estuviera procesada o condenada por hechos cometidos a través de redes sociales o mediante el uso de dispositivos telefónicos, la autoridad penitenciaria podrá inhabilitarle la cámara al dispositivo móvil del que resulte responsable o establecer condiciones de uso específicas para el caso en concreto.</p>
            </div>
        </div>

        <div class="article-card">
            <div class="article-header">
                <span class="article-num">8</span>
                <span class="article-title">Extravío, desgaste o destrucción</span>
            </div>
            <div class="article-body">
                El aparato de telefonía celular será considerado parte del <em>equipo celdario</em> de la persona detenida. En caso de pérdida, desgaste que imposibilite su uso, extravío o destrucción podrá solicitarse —en los términos del artículo 3, inciso b de este Reglamento— la provisión de un nuevo equipo o del accesorio que permita restablecer su funcionamiento o su envío a través de un familiar o visita debidamente autorizada, a un lugar especializado de reparación.
            </div>
        </div>

        <div class="article-card">
            <div class="article-header">
                <span class="article-num">9</span>
                <span class="article-title">Regímenes abierto y con salidas. Franquicias</span>
            </div>
            <div class="article-body">
                <p>Las personas privadas de la libertad que gocen de cualquiera de estos regímenes deberán observar lo dispuesto en el artículo 4 a), pero podrán portar el teléfono celular fuera del recinto de alojamiento.</p>
                <p style="margin-top:0.75rem;">Las personas privadas de la libertad que tengan salidas autorizadas deberán exhibir el dispositivo móvil a la autoridad penitenciaria en cada egreso y reingreso al establecimiento.</p>
            </div>
        </div>

        <div class="article-card">
            <div class="article-header">
                <span class="article-num">10</span>
                <span class="article-title">Disposición Transitoria</span>
            </div>
            <div class="article-body">
                Las personas privadas de la libertad que actualmente cuenten con teléfonos celulares podrán regularizar su tenencia y utilización. Para ello deberán entregar los dispositivos a las autoridades penitenciarias para su habilitación en los términos del artículo 3 y respetar las condiciones de uso que establece este Protocolo.
            </div>
        </div>
    </div>
</div>

<!-- CTA -->
<div class="section" style="text-align:center;">
    <h2 style="color:var(--text);">EduTube cumple con el protocolo</h2>
    <p class="subtitle" style="max-width:600px;margin:0 auto 2rem;">
        Nuestra plataforma fue diseñada desde cero para respetar cada artículo de este protocolo.
        Contenido educativo y cultural sin redes sociales, sin cámaras, sin algoritmos.
    </p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
        <a href="/" class="btn btn-green">Explorar EduTube</a>
        <a href="Protocolo%20de%20Celulares.pdf" target="_blank" class="btn" style="background:#1e3a8a;color:#fff;">Descargar PDF completo</a>
    </div>
</div>

<!-- Footer -->
<footer class="page-footer">
    <div class="footer-logo">
        <img src="../loguito-edutube.png" alt="">
        <span>EduTube</span>
    </div>
    <p style="font-size:0.82rem;">
        Plataforma desarrollada por <strong>VERUMax</strong> para el
        <a href="https://comite.cooperativaliberte.coop/" target="_blank">Comité de Convivencia Mario Juliano</a>.<br>
        Provincia de Buenos Aires, Argentina.
    </p>
</footer>

</body>
</html>
