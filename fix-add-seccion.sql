-- Agregar campo seccion a contenido_ia
USE ulib_edutube;
ALTER TABLE contenido_ia ADD COLUMN seccion VARCHAR(20) NOT NULL DEFAULT 'cine' AFTER bloqueado;
ALTER TABLE contenido_ia ADD INDEX idx_seccion (seccion);
