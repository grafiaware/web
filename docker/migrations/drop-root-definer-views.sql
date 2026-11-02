-- Jednorázová obnova po importu jako root / mysqldump s DEFINER=root.
-- Build DBA (na_upgrader) NEMŮŽE DROP VIEW, pokud DEFINER má SYSTEM_USER (root) → SQLSTATE 1227.
--
-- Spuštění (NajdiSi / docker-compose.migrations.yml):
--   docker compose -f docker-compose.migrations.yml exec -T mysql mysql -uroot -proot < docker/migrations/drop-root-definer-views.sql
--   PowerShell:
--     Get-Content docker/migrations/drop-root-definer-views.sql -Raw |
--       docker compose -f docker-compose.migrations.yml exec -T mysql mysql -uroot -proot
--
-- Poté: php scripts/db-rebuild.php --target=all --yes

DROP VIEW IF EXISTS `najdisi`.`fulltree`;
DROP VIEW IF EXISTS `najdisi`.`hierarchy_view`;
DROP VIEW IF EXISTS `events`.`pocet_pozic`;
