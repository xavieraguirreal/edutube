-- Eliminar videos privados/eliminados (sin título)
DELETE pv FROM playlist_videos pv
JOIN videos v ON pv.video_id = v.id
WHERE v.titulo = 'Sin título';

DELETE FROM videos WHERE titulo = 'Sin título';
