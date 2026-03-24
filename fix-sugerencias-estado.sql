-- Cambiar sugerencias de leida (0/1) a estado (nueva/pendiente/realizada)
USE ulib_edutube;
ALTER TABLE sugerencias ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'nueva' AFTER leida;
UPDATE sugerencias SET estado = 'nueva' WHERE leida = 0;
UPDATE sugerencias SET estado = 'realizada' WHERE leida = 1;
