# EduTube — Roadmap

## Completado
- [x] Prototipo inicial con 3 videos hardcodeados (EduVisual)
- [x] Embed de YouTube con youtube-nocookie.com
- [x] Diagnóstico y fix de Error 153 (referrerpolicy era el problema)
- [x] Escudos CSS sobre branding de YouTube
- [x] sandbox en iframe (bloquea popups/navegación a YouTube)
- [x] CSP meta tags
- [x] Controles propios con YouTube IFrame Player API (controls=0)
- [x] Play/pausa, volumen, mute, barra de progreso, fullscreen
- [x] Renombre a EduTube + logo verde personalizado + favicon
- [x] Rediseño completo estilo YouTube (dark mode)
- [x] Topbar fijo con búsqueda
- [x] Sidebar con categorías, canales, actividad del usuario
- [x] Cards de video con avatar de canal, vistas, tiempo relativo, duración
- [x] Filtro por categoría (chips + sidebar)
- [x] Filtro por canal
- [x] Búsqueda por título + descripción + tags + canal
- [x] Ver después (localStorage)
- [x] Historial automático (localStorage)
- [x] Me gusta (localStorage)
- [x] Bottom navigation mobile
- [x] Búsqueda mobile con overlay
- [x] Toast notifications
- [x] Descripción expandible en player
- [x] Botones Like y Guardar en player
- [x] Videos relacionados (mismo canal primero)
- [x] 40 videos hardcodeados (Liberté, Infobae, Aterciopelados, A24)
- [x] Datos reales de YouTube (vistas, fechas, duraciones via yt-dlp)
- [x] Títulos exactos de YouTube
- [x] Archivo videos.js compartido entre index y ver
- [x] Repo Git configurado + push a GitHub
- [x] .gitignore para archivos sensibles

## Probado y verificado
- Embed funciona correctamente sin referrerpolicy
- sandbox bloquea navegación a YouTube desde iframe
- CSP no causa Error 153 (el error era solo referrerpolicy)
- Controles propios funcionan con IFrame API
- Las reproducciones en EduTube SÍ cuentan como vistas en YouTube

## Pendiente — Próximos pasos
- [x] Backend PHP + MySQL
  - [x] config.php (conexión BD + OpenAI + funciones auxiliares)
  - [x] setup.sql (esquema completo: videos, canales, categorías, playlists, embeddings, vistas, búsquedas)
  - [x] admin.php (panel de administración completo)
  - [x] CRUD de videos, categorías, canales
  - [x] Autenticación admin (bcrypt, sesiones, CSRF)
  - [x] Rate limiting en login
- [x] Importación de videos
  - [x] Agregar video individual por URL (con autocompletado via yt-dlp)
  - [x] Importar canal completo (RSS feed + yt-dlp metadata)
  - [ ] Importar playlist de YouTube
  - [ ] Obtener metadatos automáticos (YouTube Data API v3)
- [ ] Listas de reproducción propias
  - [ ] Crear/editar playlists
  - [ ] Reproducción continua
- [ ] Shorts (videos verticales con layout 9:16)
- [ ] Página de canal (como YouTube: banner, videos del canal)
- [ ] Estadísticas en admin (videos más vistos, categorías populares)
- [ ] Paginación / infinite scroll
- [ ] Contador de vistas propio en BD

## Descartado
- Descarga offline (requiere yt-dlp server-side + mucho espacio en disco)
