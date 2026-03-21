-- ═══════════════════════════════════════════
-- Migración: auto_sync + default_categoria_id en canales
-- Ejecutar en MySQL 5.7+ / MariaDB
-- ═══════════════════════════════════════════

USE ulib_edutube;

-- Nuevos campos en canales
ALTER TABLE canales
    ADD COLUMN auto_sync TINYINT(1) NOT NULL DEFAULT 0 AFTER activo,
    ADD COLUMN default_categoria_id INT DEFAULT NULL AFTER auto_sync,
    ADD CONSTRAINT fk_canales_categoria FOREIGN KEY (default_categoria_id) REFERENCES categorias(id) ON DELETE SET NULL;

-- Log de sincronizaciones automáticas
CREATE TABLE IF NOT EXISTS sync_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    canal_id INT,
    videos_importados INT NOT NULL DEFAULT 0,
    playlists_importadas INT NOT NULL DEFAULT 0,
    errores TEXT,
    ejecutado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (canal_id) REFERENCES canales(id) ON DELETE SET NULL,
    INDEX idx_sync_log_fecha (ejecutado_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
