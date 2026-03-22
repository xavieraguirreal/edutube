-- ═══════════════════════════════════════════
-- EduTube — Migración: Contenido Internet Archive
-- Crear tabla contenido_ia + datos iniciales (12 películas + 9 documentales)
-- ═══════════════════════════════════════════

USE ulib_edutube;

CREATE TABLE IF NOT EXISTS contenido_ia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    ia_id VARCHAR(200) NOT NULL,
    tipo ENUM('pelicula','documental') NOT NULL,
    titulo VARCHAR(500) NOT NULL,
    director VARCHAR(200) DEFAULT '',
    year INT DEFAULT NULL,
    duracion VARCHAR(15) DEFAULT '',
    genero VARCHAR(100) DEFAULT '',
    descripcion TEXT,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    orden INT NOT NULL DEFAULT 0,
    agregado_por VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Películas (12) ──
INSERT INTO contenido_ia (slug, ia_id, tipo, titulo, director, year, duracion, genero, orden, agregado_por) VALUES
('ElPequenoSalvaje', 'Truffaut1969', 'pelicula', 'El pequeño salvaje (1969)', 'François Truffaut', 1969, '1:23:00', 'Drama', 1, 'admin'),
('Apocalypto', 'apocalypto-2006-online-latino-castellano-y-subtitulada', 'pelicula', 'Apocalypto (2006)', 'Mel Gibson', 2006, '2:18:00', 'Aventura', 2, 'admin'),
('MortadeloFilemon', 'mortadelo-y-filemon-contra-jimmy-el-cachondo-m-1080p', 'pelicula', 'Mortadelo y Filemón contra Jimmy el Cachondo (2014)', 'Javier Fesser', 2014, '1:30:00', 'Comedia', 3, 'admin'),
('Godzilla1954', 'GodzillaJaponBajoElTerrorDelMonstruo1954Espanol', 'pelicula', 'Godzilla (1954) — Doblada español', 'Ishirō Honda', 1954, '1:36:00', 'Ciencia ficción', 4, 'admin'),
('DelOdioNaceElAmor', 'TheTorch', 'pelicula', 'Del odio nace el amor (1950)', 'Emilio Fernández', 1950, '1:23:00', 'Drama', 5, 'admin'),
('Libertarias', 'libertarias_1996', 'pelicula', 'Libertarias (1996)', 'Vicente Aranda', 1996, '2:04:00', 'Drama', 6, 'admin'),
('Dementia13Subs', 'Dementia13withSpanishSubtitles', 'pelicula', 'Dementia 13 (1963) — Subs español', 'Francis Ford Coppola', 1963, '1:15:00', 'Terror', 7, 'admin'),
('LittleShopSubs', 'TheLittleShopOfHorrorswithSpanishSubtitles', 'pelicula', 'La tiendita de los horrores (1960) — Subs español', 'Roger Corman', 1960, '1:12:00', 'Terror', 8, 'admin'),
('SaccoVanzetti', 'sacco.and.vanzetti.1971', 'pelicula', 'Sacco y Vanzetti (1971)', 'Giuliano Montaldo', 1971, '2:01:00', 'Drama', 9, 'admin'),
('BabAziz', 'BabAziz2005_201704', 'pelicula', 'Bab''Aziz — El príncipe que contemplaba su alma (2005)', 'Nacer Khemir', 2005, '1:36:00', 'Drama', 10, 'admin'),
('ElHotelElectrico', 'ElHotelElectrico', 'pelicula', 'El hotel eléctrico (1908)', 'Segundo de Chomón', 1908, '0:09:00', 'Ciencia ficción', 11, 'admin'),
('LaSociedadSemaforo', 'LaSociedadDelSemaforoRubenMendoza2010', 'pelicula', 'La sociedad del semáforo (2010)', 'Rubén Mendoza', 2010, '1:45:00', 'Drama', 12, 'admin');

-- ── Documentales (9) ──
INSERT INTO contenido_ia (slug, ia_id, tipo, titulo, director, year, duracion, genero, orden, agregado_por) VALUES
('InfamiaOaxaca', 'infamia_en_oaxaca', 'documental', 'Infamia en Oaxaca (2006)', 'Mal de Ojo TV', 2006, '1:00:00', 'Sociedad', 1, 'admin'),
('TheTake', 'the-take-2004', 'documental', 'The Take — La toma (2004)', 'Avi Lewis & Naomi Klein', 2004, '1:27:00', 'Sociedad', 2, 'admin'),
('VenezuelaBolivariana', 'Venezuela_Bolivariana_VEN_2004', 'documental', 'Venezuela Bolivariana (2004)', 'Varios', 2004, '1:16:00', 'Historia', 3, 'admin'),
('LaOtraCuba', 'TheOtherCuba', 'documental', 'La otra Cuba (1984)', 'Orlando Jiménez Leal', 1984, '1:00:00', 'Historia', 4, 'admin'),
('NinosPerdidosFranquismo', 'losninosperdidosdelfranquismo', 'documental', 'Los niños perdidos del franquismo', 'Montse Armengou & Ricard Belis', 2002, '1:30:00', 'Historia', 5, 'admin'),
('CulturaRadical', 'CulturaRadical', 'documental', 'Cultura radical (2017)', 'Varios', 2017, '0:52:00', 'Sociedad', 6, 'admin'),
('OaxacaRebelion', 'Oaxaca_rebelion-popular_2006', 'documental', 'Oaxaca: Rebelión popular (2006)', 'Mal de Ojo TV', 2006, '0:45:00', 'Sociedad', 7, 'admin'),
('PeriodoEspecial', 'PERIODICO', 'documental', 'El período especial — Cuba 1993', 'Varios', 1996, '0:55:00', 'Historia', 8, 'admin'),
('ElVientre', 'ElVientre', 'documental', 'El vientre', 'Varios', 2010, '1:20:00', 'Sociedad', 9, 'admin');
