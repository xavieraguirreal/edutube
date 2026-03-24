-- Sugerencias v2: nombre, email, respuesta, tipo mejora
USE ulib_edutube;
ALTER TABLE sugerencias ADD COLUMN nombre VARCHAR(100) DEFAULT '' AFTER ip_hash;
ALTER TABLE sugerencias ADD COLUMN email VARCHAR(200) DEFAULT '' AFTER nombre;
ALTER TABLE sugerencias ADD COLUMN respuesta TEXT AFTER estado;
ALTER TABLE sugerencias ADD COLUMN respondido_at TIMESTAMP NULL AFTER respuesta;
ALTER TABLE sugerencias MODIFY COLUMN tipo ENUM('canal','tema','contenido','mejora','otro') NOT NULL DEFAULT 'otro';
