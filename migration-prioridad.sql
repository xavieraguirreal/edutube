-- Migración: prioridad de portada en canales
USE ulib_edutube;

ALTER TABLE canales
    ADD COLUMN prioridad_portada INT NOT NULL DEFAULT 0 AFTER activo;
