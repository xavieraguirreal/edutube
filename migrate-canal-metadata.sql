-- Migration: Add channel metadata fields
-- Run once on the server: mysql -u root -p edutube < migrate-canal-metadata.sql

ALTER TABLE canales
    ADD COLUMN thumbnail_url VARCHAR(500) DEFAULT NULL AFTER descripcion,
    ADD COLUMN banner_url VARCHAR(500) DEFAULT NULL AFTER thumbnail_url,
    ADD COLUMN subscriber_count INT NOT NULL DEFAULT 0 AFTER banner_url,
    ADD COLUMN total_views BIGINT NOT NULL DEFAULT 0 AFTER subscriber_count,
    ADD COLUMN video_count_yt INT NOT NULL DEFAULT 0 AFTER total_views,
    ADD COLUMN country VARCHAR(5) DEFAULT NULL AFTER video_count_yt,
    ADD COLUMN custom_url VARCHAR(100) DEFAULT NULL AFTER country,
    ADD COLUMN youtube_created_at DATE DEFAULT NULL AFTER custom_url,
    ADD COLUMN nota_interna TEXT DEFAULT NULL AFTER youtube_created_at,
    ADD COLUMN metadata_updated_at TIMESTAMP NULL DEFAULT NULL AFTER nota_interna;
