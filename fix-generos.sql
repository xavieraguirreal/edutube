-- ═══════════════════════════════════════════
-- Fix: Limpiar géneros importados de Internet Archive
-- Aplica auto-detección de género basada en keywords del campo actual
-- Los que no se detectan quedan vacíos para asignar manualmente
-- ═══════════════════════════════════════════

USE ulib_edutube;

-- Primero: los que tienen género basura (más de 30 chars = probablemente tags de IA)
-- O los que tienen géneros que no están en nuestra lista predefinida

-- Paso 1: Auto-detectar desde el campo genero actual
UPDATE contenido_ia SET genero = 'Drama' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%drama%' OR LOWER(genero) LIKE '%tragic%');

UPDATE contenido_ia SET genero = 'Comedia' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%comedy%' OR LOWER(genero) LIKE '%comedia%' OR LOWER(genero) LIKE '%humor%');

UPDATE contenido_ia SET genero = 'Terror' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%horror%' OR LOWER(genero) LIKE '%terror%');

UPDATE contenido_ia SET genero = 'Ciencia ficción' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%sci-fi%' OR LOWER(genero) LIKE '%science fiction%' OR LOWER(genero) LIKE '%ciencia ficci%');

UPDATE contenido_ia SET genero = 'Aventura' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%adventure%' OR LOWER(genero) LIKE '%aventura%');

UPDATE contenido_ia SET genero = 'Acción' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%action%' OR LOWER(genero) LIKE '%acción%');

UPDATE contenido_ia SET genero = 'Suspenso' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%thriller%' OR LOWER(genero) LIKE '%suspens%' OR LOWER(genero) LIKE '%mystery%');

UPDATE contenido_ia SET genero = 'Film Noir' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND LOWER(genero) LIKE '%noir%';

UPDATE contenido_ia SET genero = 'Animación' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%animat%' OR LOWER(genero) LIKE '%cartoon%');

UPDATE contenido_ia SET genero = 'Documental' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%documentary%' OR LOWER(genero) LIKE '%documental%');

UPDATE contenido_ia SET genero = 'Historia' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%history%' OR LOWER(genero) LIKE '%historia%' OR LOWER(genero) LIKE '%historical%');

UPDATE contenido_ia SET genero = 'Musical' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND LOWER(genero) LIKE '%musical%';

UPDATE contenido_ia SET genero = 'Romance' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%romance%' OR LOWER(genero) LIKE '%romantic%');

UPDATE contenido_ia SET genero = 'Western' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND LOWER(genero) LIKE '%western%';

UPDATE contenido_ia SET genero = 'Bélico' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%war%' OR LOWER(genero) LIKE '%militar%' OR LOWER(genero) LIKE '%guerra%');

UPDATE contenido_ia SET genero = 'Cine mudo' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo') AND (LOWER(genero) LIKE '%silent%' OR LOWER(genero) LIKE '%cine mudo%');

-- Paso 2: Los que siguen sin género válido → vaciar
UPDATE contenido_ia SET genero = '' WHERE genero NOT IN ('Drama','Comedia','Terror','Ciencia ficción','Aventura','Acción','Suspenso','Film Noir','Animación','Documental','Historia','Sociedad','Musical','Romance','Western','Bélico','Cine mudo','');

-- Verificar resultado
SELECT genero, COUNT(*) as total FROM contenido_ia GROUP BY genero ORDER BY total DESC;
