-- Testovací prostředí: menu „menu role grantor“ (varianta b v docs/vp/testy/A-test-sprava-uctu.md).
--
-- Jen pro lokální a testovací DB, NE pro produkci a NE jako migrace: na produkci zakládá kořen menu a přesouvá
-- položky Petr ručně v redakčním systému (rozhodnutí v A9).
--
-- Co dělá (v DB red, např. najdisi):
--   1. Pokud neexistuje kořen menu „menu role grantor“, založí ho hned za kořenem „menu events admin“
--      (uzel v hierarchy, položka menu_item „MENU ROLE GRANTOR“ ve všech jazycích jako „menu events admin“, řádek v menu_root).
--   2. Přesune pod něj položky se šablonou admin-credentials a admin-role (hledá se podle tabulky static, ne podle názvu).
--      Položky zůstanou aktivní. Položka, která už pod kořenem je, se nepřesouvá.
-- Skript lze spustit opakovaně. Návrat do varianty a: rebuild DB (scripts/db-rebuild.php) nebo ruční přesun v redakčním systému.
-- Běží na MySQL 8 i MariaDB. Spuštění: mysql najdisi < scripts/test-menu-role-grantor.sql

START TRANSACTION;

-- 1. kořen menu role grantor
SET @rootUid := (SELECT uid_fk FROM menu_root WHERE name = 'root');
SET @eventsAdminUid := (SELECT uid_fk FROM menu_root WHERE name = 'menu events admin');
SET @create := (SELECT COUNT(*) = 0 FROM menu_root WHERE name = 'menu role grantor') AND @rootUid IS NOT NULL AND @eventsAdminUid IS NOT NULL;
SET @newUid := 'a9rolegrantor';
SET @pos := (SELECT right_node FROM hierarchy WHERE uid = @eventsAdminUid);

UPDATE hierarchy SET right_node = right_node + 2 WHERE @create AND right_node > @pos;
UPDATE hierarchy SET left_node = left_node + 2 WHERE @create AND left_node > @pos;
INSERT INTO hierarchy (uid, left_node, right_node, parent_uid)
    SELECT @newUid, @pos + 1, @pos + 2, @rootUid FROM DUAL WHERE @create;
INSERT INTO menu_item (lang_code_fk, uid_fk, api_module_fk, api_generator_fk, title, prettyuri, active)
    SELECT lang_code_fk, @newUid, 'red', 'menu', 'MENU ROLE GRANTOR', CONCAT(lang_code_fk, @newUid, '-menu-role-grantor'), active
    FROM menu_item WHERE @create AND uid_fk = @eventsAdminUid;
INSERT INTO menu_root (name, uid_fk) SELECT 'menu role grantor', @newUid FROM DUAL WHERE @create;

SET @targetUid := (SELECT uid_fk FROM menu_root WHERE name = 'menu role grantor');

-- 2a. ADMIN CREDENTIALS jako poslední potomek kořene menu role grantor
SET @srcUid := (SELECT m.uid_fk FROM static s JOIN menu_item m ON m.id = s.menu_item_id_fk
                WHERE s.template = 'admin-credentials' AND m.lang_code_fk = 'cs' ORDER BY s.id LIMIT 1);
SET @move := (SELECT COUNT(*) FROM hierarchy s, hierarchy t
              WHERE s.uid = @srcUid AND t.uid = @targetUid AND NOT (s.left_node > t.left_node AND s.right_node < t.right_node));
SET @sl := (SELECT left_node FROM hierarchy WHERE uid = @srcUid);
SET @sr := (SELECT right_node FROM hierarchy WHERE uid = @srcUid);
SET @w := @sr - @sl + 1;
UPDATE hierarchy SET left_node = 0 - left_node, right_node = 0 - right_node WHERE @move AND left_node BETWEEN @sl AND @sr;
UPDATE hierarchy SET right_node = right_node - @w WHERE @move AND right_node > @sr;
UPDATE hierarchy SET left_node = left_node - @w WHERE @move AND left_node > @sr;
SET @tr := (SELECT right_node FROM hierarchy WHERE uid = @targetUid);
UPDATE hierarchy SET right_node = right_node + @w WHERE @move AND right_node >= @tr;
UPDATE hierarchy SET left_node = left_node + @w WHERE @move AND left_node > @tr;
UPDATE hierarchy SET left_node = 0 - left_node + (@tr - @sl), right_node = 0 - right_node + (@tr - @sl) WHERE @move AND left_node < 0;
UPDATE hierarchy SET parent_uid = @targetUid WHERE @move AND uid = @srcUid;

-- 2b. ADMIN ROLE – stejně
SET @srcUid := (SELECT m.uid_fk FROM static s JOIN menu_item m ON m.id = s.menu_item_id_fk
                WHERE s.template = 'admin-role' AND m.lang_code_fk = 'cs' ORDER BY s.id LIMIT 1);
SET @move := (SELECT COUNT(*) FROM hierarchy s, hierarchy t
              WHERE s.uid = @srcUid AND t.uid = @targetUid AND NOT (s.left_node > t.left_node AND s.right_node < t.right_node));
SET @sl := (SELECT left_node FROM hierarchy WHERE uid = @srcUid);
SET @sr := (SELECT right_node FROM hierarchy WHERE uid = @srcUid);
SET @w := @sr - @sl + 1;
UPDATE hierarchy SET left_node = 0 - left_node, right_node = 0 - right_node WHERE @move AND left_node BETWEEN @sl AND @sr;
UPDATE hierarchy SET right_node = right_node - @w WHERE @move AND right_node > @sr;
UPDATE hierarchy SET left_node = left_node - @w WHERE @move AND left_node > @sr;
SET @tr := (SELECT right_node FROM hierarchy WHERE uid = @targetUid);
UPDATE hierarchy SET right_node = right_node + @w WHERE @move AND right_node >= @tr;
UPDATE hierarchy SET left_node = left_node + @w WHERE @move AND left_node > @tr;
UPDATE hierarchy SET left_node = 0 - left_node + (@tr - @sl), right_node = 0 - right_node + (@tr - @sl) WHERE @move AND left_node < 0;
UPDATE hierarchy SET parent_uid = @targetUid WHERE @move AND uid = @srcUid;

COMMIT;

-- kontrola: kořen a jeho položky
SELECT h.left_node, h.right_node, m.title
FROM hierarchy h
JOIN hierarchy t ON t.uid = (SELECT uid_fk FROM menu_root WHERE name = 'menu role grantor')
LEFT JOIN menu_item m ON m.uid_fk = h.uid AND m.lang_code_fk = 'cs'
WHERE h.left_node BETWEEN t.left_node AND t.right_node
ORDER BY h.left_node;
