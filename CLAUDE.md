# EduTube — Documentación Técnica

## Descripción
Plataforma de videos educativos que embebe videos de YouTube en un entorno controlado.
Diseñada para uso en establecimientos penitenciarios (Universidad Liberté, Buenos Aires).
URL: edutube.universidadliberte.org

## Stack actual (prototipo)
- Frontend puro: HTML5, CSS3, JavaScript vanilla
- Sin backend ni BD (videos hardcodeados en videos.js)
- Despliegue: GitHub → servidor via git pull. Archivos sensibles (config.php) via FileZilla.

## Stack objetivo (backend)
- Backend: PHP 7.4+ nativo con PDO
- BD: MySQL 5.7+ / MariaDB (InnoDB, utf8mb4)
- YouTube Data API v3 para importar metadatos
- Apache (XAMPP o similar)

## Seguridad del embed
- Dominio: youtube-nocookie.com (sin cookies de tracking)
- Parámetros: rel=0, modestbranding=1, iv_load_policy=3, controls=0
- sandbox="allow-scripts allow-same-origin allow-presentation" (sin allow-popups)
- CSP meta tag (frame-src, img-src, script-src restringidos)
- Escudos CSS sobre branding de YouTube (top + bottom gradients)
- Controles propios via YouTube IFrame Player API
- IMPORTANTE: NO usar referrerpolicy="no-referrer" (causa Error 153)

## Indexación de videos
### Actual (hardcodeado)
Los videos están en videos.js con estructura: id, titulo, descripcion, canal, categoria, duracion, vistas, fecha, tags.

### Con yt-dlp (para obtener metadatos)
```bash
yt-dlp --print "%(id)s|%(title)s|%(view_count)s|%(upload_date)s|%(duration_string)s" --skip-download VIDEO_ID
```

### Con YouTube RSS (para listar videos de un canal)
```
https://www.youtube.com/feeds/videos.xml?channel_id=CHANNEL_ID
```
Para obtener el channel_id:
```bash
curl -s "https://www.youtube.com/@HANDLE" | grep -o 'UC[A-Za-z0-9_-]\{22\}' | head -1
```

### Con YouTube Data API v3 (futuro, con backend)
```
GET https://www.googleapis.com/youtube/v3/videos?id=VIDEO_ID&part=statistics,contentDetails,snippet&key=API_KEY
```
Cuota gratuita: 10.000 unidades/día.

## Flujo de trabajo
1. Desarrollar localmente en E:\appedutube
2. git commit + git push a https://github.com/xavieraguirreal/edutube.git
3. En servidor: git pull para sincronizar
4. Archivos sensibles (config.php, .env): subir manualmente con FileZilla
5. .gitignore excluye: config.php, *.env, capturas, docx

## Canales actuales
- Cooperativa Liberté (UCvMdqdMXxcj8TdfRezzAf8g)
- Infobae (UCvsU0EGXN7Su7MfNqcTGNHg)
- Aterciopelados (UCGaqHkWSf7izAZDX5mVTowg)
- A24 (UCR9120YBAqMfntqgRTKmkjQ)
