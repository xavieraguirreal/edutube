-- Migración: mostrar_en_portada en canales
USE ulib_edutube;

ALTER TABLE canales
    ADD COLUMN mostrar_en_portada TINYINT(1) NOT NULL DEFAULT 0 AFTER activo;
