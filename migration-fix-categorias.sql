-- Asignar categoria_id a videos que no tienen, usando la default del canal
USE ulib_edutube;

UPDATE videos v
JOIN canales c ON v.canal_id = c.id
SET v.categoria_id = c.default_categoria_id
WHERE v.categoria_id IS NULL
AND c.default_categoria_id IS NOT NULL;
