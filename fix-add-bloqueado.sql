-- Agregar campo bloqueado a contenido_ia
-- Bloqueado = no se muestra en el portal y no se re-importa
USE ulib_edutube;
ALTER TABLE contenido_ia ADD COLUMN bloqueado TINYINT(1) NOT NULL DEFAULT 0 AFTER activo;
