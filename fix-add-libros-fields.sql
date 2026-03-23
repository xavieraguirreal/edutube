-- Agregar campos para libros de Gutenberg
USE ulib_edutube;
ALTER TABLE contenido_ia ADD COLUMN url_portada VARCHAR(500) DEFAULT '' AFTER seccion;
ALTER TABLE contenido_ia ADD COLUMN url_contenido VARCHAR(500) DEFAULT '' AFTER url_portada;
ALTER TABLE contenido_ia ADD COLUMN fuente VARCHAR(50) NOT NULL DEFAULT 'archive.org' AFTER url_contenido;
