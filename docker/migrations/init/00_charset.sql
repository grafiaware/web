-- Session charset pro skripty v docker-entrypoint-initdb.d (běží před CREATE DATABASE).
-- Server default je utf8mb4 (viz docker-compose.migrations.yml); SET NAMES pojistí i CLI session.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
