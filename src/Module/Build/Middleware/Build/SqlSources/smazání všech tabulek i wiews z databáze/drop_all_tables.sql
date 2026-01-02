/* 
 * Smazání všech tabulek z databáze před následných importem tabulek s daty exportovaných např. pomocí Workbench.
 * 
 * Cíl je: nechat databázi (schema) i oprávnění být a odstranit jen všechny tabulky.
 */
/**
 * Author:  pes2704
 * Created: 1. 1. 2026
 *
 * maže: table, views
 * nesmaže: procedury, funkce, trigerry
 *
 *
 */

    /* =========================
       VIEW se musí mazat před TABLE, pokud závisí na tabulkách
       ========================= */
       
DELIMITER $$

CREATE PROCEDURE drop_all_tables_and_views(IN db_name VARCHAR(64))
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE obj_name VARCHAR(64);

    /* cursory – MUSÍ BÝT NA ZAČÁTKU */
    DECLARE cur_views CURSOR FOR
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = db_name
          AND table_type = 'VIEW';

    DECLARE cur_tables CURSOR FOR
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = db_name
          AND table_type = 'BASE TABLE';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    /* =========================
       FK OFF
       ========================= */
    SET @old_fk_checks = @@FOREIGN_KEY_CHECKS;
    SET FOREIGN_KEY_CHECKS = 0;

    /* =========================
       1) SMAZÁNÍ VIEW
       ========================= */
    SET done = 0;
    OPEN cur_views;

    view_loop: LOOP
        FETCH cur_views INTO obj_name;
        IF done THEN
            LEAVE view_loop;
        END IF;

        SET @sql = CONCAT('DROP VIEW `', db_name, '`.`', obj_name, '`');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END LOOP;

    CLOSE cur_views;

    /* =========================
       2) SMAZÁNÍ TABULEK
       ========================= */
    SET done = 0;
    OPEN cur_tables;

    table_loop: LOOP
        FETCH cur_tables INTO obj_name;
        IF done THEN
            LEAVE table_loop;
        END IF;

        SET @sql = CONCAT('DROP TABLE `', db_name, '`.`', obj_name, '`');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END LOOP;

    CLOSE cur_tables;

    /* =========================
       FK ON
       ========================= */
    SET FOREIGN_KEY_CHECKS = @old_fk_checks;
END$$

DELIMITER ;



CALL drop_all_tables_and_views('najdisi');

DROP PROCEDURE drop_all_tables_and_views;