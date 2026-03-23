-- ═══════════════════════════════════════════
-- Desactivar contenido importado masivamente (pendiente de revisión)
-- Mantiene activos solo los 21 originales curados manualmente
-- ═══════════════════════════════════════════

USE ulib_edutube;

-- Paso 1: Desactivar todo
UPDATE contenido_ia SET activo = 0;

-- Paso 2: Reactivar los 21 originales (curados manualmente)
UPDATE contenido_ia SET activo = 1 WHERE slug IN (
    'ElPequenoSalvaje', 'Apocalypto', 'MortadeloFilemon', 'Godzilla1954',
    'DelOdioNaceElAmor', 'Libertarias', 'Dementia13Subs', 'LittleShopSubs',
    'SaccoVanzetti', 'BabAziz', 'ElHotelElectrico', 'LaSociedadSemaforo',
    'InfamiaOaxaca', 'TheTake', 'VenezuelaBolivariana', 'LaOtraCuba',
    'NinosPerdidosFranquismo', 'CulturaRadical', 'OaxacaRebelion',
    'PeriodoEspecial', 'ElVientre'
);

-- Verificar
SELECT activo, COUNT(*) AS total FROM contenido_ia GROUP BY activo;
