-- =============================================================================
-- Repair: OA/VP static path+template after migration to NajdiSi layout
-- Old sites stored folder in `path`; new resolver uses WEB_STATIC + path + template/.
-- Safe to re-run.
-- =============================================================================

-- Prefer old path as template folder when template empty
UPDATE `static`
SET
  `template` = TRIM(BOTH '/' FROM REPLACE(`path`, '\\', '/')),
  `path` = NULL
WHERE (`template` IS NULL OR `template` = '')
  AND `path` IS NOT NULL
  AND TRIM(`path`) <> '';

-- If both were filled (doubled folders), keep template and clear path
UPDATE `static`
SET `path` = NULL
WHERE `path` IS NOT NULL
  AND TRIM(`path`) <> ''
  AND `template` IS NOT NULL
  AND TRIM(`template`) <> '';

-- VERIFY:
-- SELECT id, menu_item_id_fk, path, template FROM static;
