-- Migración: mostrar_en_portada en categorías (NO en canales)
USE ulib_edutube;

-- Si ya se agregó en canales, quitar
ALTER TABLE canales DROP COLUMN IF EXISTS mostrar_en_portada;

-- Agregar en categorías
ALTER TABLE categorias
    ADD COLUMN mostrar_en_portada TINYINT(1) NOT NULL DEFAULT 0 AFTER activa;
