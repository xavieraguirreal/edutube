-- ═══════════════════════════════════════════
-- EduTube — Esquema de Base de Datos
-- Ejecutar en MySQL 5.7+ / MariaDB
-- ═══════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS ulib_edutube
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ulib_edutube;

-- ── Canales ──
CREATE TABLE canales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    youtube_channel_id VARCHAR(30) UNIQUE,
    codigo VARCHAR(10) NOT NULL DEFAULT 'CH',
    color VARCHAR(10) NOT NULL DEFAULT '#2e8b47',
    descripcion TEXT,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    prioridad_portada INT NOT NULL DEFAULT 0,
    auto_sync TINYINT(1) NOT NULL DEFAULT 0,
    default_categoria_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (default_categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Categorías ──
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    icono VARCHAR(50) DEFAULT '',
    orden INT NOT NULL DEFAULT 0,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    mostrar_en_portada TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ── Videos ──
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    youtube_id VARCHAR(20) NOT NULL UNIQUE,
    titulo VARCHAR(500) NOT NULL,
    descripcion TEXT,
    canal_id INT,
    categoria_id INT,
    duracion VARCHAR(15) DEFAULT '',
    vistas_yt INT NOT NULL DEFAULT 0,
    fecha_yt DATE,
    tags TEXT,
    embedding JSON,
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    agregado_por VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (canal_id) REFERENCES canales(id) ON DELETE SET NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    FULLTEXT INDEX ft_videos (titulo, descripcion, tags)
) ENGINE=InnoDB;

-- ── Playlists ──
CREATE TABLE playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    canal_id INT,
    youtube_playlist_id VARCHAR(50),
    orden INT NOT NULL DEFAULT 0,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (canal_id) REFERENCES canales(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Playlist ↔ Videos ──
CREATE TABLE playlist_videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    playlist_id INT NOT NULL,
    video_id INT NOT NULL,
    orden INT NOT NULL DEFAULT 0,
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    UNIQUE KEY uq_playlist_video (playlist_id, video_id)
) ENGINE=InnoDB;

-- ── Videos relacionados (pre-computados) ──
CREATE TABLE videos_relacionados (
    video_id INT NOT NULL,
    related_id INT NOT NULL,
    score FLOAT NOT NULL DEFAULT 0,
    PRIMARY KEY (video_id, related_id),
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (related_id) REFERENCES videos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Administradores ──
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Registro de vistas ──
CREATE TABLE registro_vistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    ip_hash VARCHAR(64),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    INDEX idx_vistas_video (video_id),
    INDEX idx_vistas_fecha (fecha)
) ENGINE=InnoDB;

-- ── Log de búsquedas (analytics) ──
CREATE TABLE busquedas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    termino VARCHAR(255) NOT NULL,
    resultados INT NOT NULL DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_busquedas_fecha (fecha)
) ENGINE=InnoDB;

-- ── Log de sincronizaciones ──
CREATE TABLE sync_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    canal_id INT,
    videos_importados INT NOT NULL DEFAULT 0,
    playlists_importadas INT NOT NULL DEFAULT 0,
    errores TEXT,
    ejecutado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (canal_id) REFERENCES canales(id) ON DELETE SET NULL,
    INDEX idx_sync_log_fecha (ejecutado_at)
) ENGINE=InnoDB;

-- ── Contenido Internet Archive (películas/documentales) ──
CREATE TABLE contenido_ia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    ia_id VARCHAR(200) NOT NULL,
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
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Login attempts (rate limiting) ──
CREATE TABLE login_intentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_intentos_ip_fecha (ip, fecha)
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════
-- Datos iniciales
-- ═══════════════════════════════════════════

-- Admin por defecto (cambiar contraseña inmediatamente)
-- Password: EduTube2026! (hasheado con bcrypt)
INSERT INTO admins (usuario, password_hash, nombre) VALUES
('admin', '$2y$10$8K1p/a0dR1xqM8K7uOQ./.6EtOkGjI1vS0F3.Nv2hJhGqbQE2t6Ky', 'Administrador');

-- Categorías iniciales
INSERT INTO categorias (nombre, icono, orden) VALUES
('Cursos', '📖', 1),
('Encuentros', '🎓', 2),
('Noticias', '📰', 3),
('Música', '🎵', 4),
('Entrevistas', '🎤', 5),
('Programas', '📺', 6);

-- Canales iniciales
INSERT INTO canales (nombre, youtube_channel_id, codigo, color) VALUES
('Cooperativa Liberté', 'UCvMdqdMXxcj8TdfRezzAf8g', 'CL', '#2e8b47'),
('Infobae', 'UCvsU0EGXN7Su7MfNqcTGNHg', 'IB', '#e63946'),
('Aterciopelados', 'UCGaqHkWSf7izAZDX5mVTowg', 'AT', '#9b5de5'),
('A24', 'UCR9120YBAqMfntqgRTKmkjQ', 'A24', '#f77f00');
