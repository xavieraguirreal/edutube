-- Tabla de sugerencias de usuarios
USE ulib_edutube;
CREATE TABLE IF NOT EXISTS sugerencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('canal','tema','contenido','otro') NOT NULL DEFAULT 'otro',
    texto TEXT NOT NULL,
    ip_hash VARCHAR(64),
    leida TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_leida (leida)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
