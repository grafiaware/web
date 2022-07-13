SET @database = 'otevreneateliery_upgrade';
SET @user = 'gr_upgrader';
SET @host = 'localhost';
SET @upgrader_password = 'gr_upgrader';


SET @stmt_create_db = CONCAT('DROP DATABASE IF EXISTS ', @database);
PREPARE stmt_create_db FROM @stmt_create_db;
EXECUTE stmt_create_db;
DEALLOCATE PREPARE stmt_create_db;

SET @stmt_create_db = CONCAT('CREATE DATABASE IF NOT EXISTS ', @database, ' CHARACTER SET utf8 COLLATE utf8_general_ci');
PREPARE stmt_create_db FROM @stmt_create_db;
EXECUTE stmt_create_db;
DEALLOCATE PREPARE stmt_create_db;

SET @stmt_drop_user = CONCAT('DROP USER ', @user, '@', @host);
PREPARE stmt_drop_user FROM @stmt_drop_user;
EXECUTE stmt_drop_user;
DEALLOCATE PREPARE stmt_drop_user;

-- assume that the MySQL root account has the CREATE USER privilege and all privileges that it grants to other accounts.
-- CREATE USER 'gr_upgrader'@'localhost' IDENTIFIED BY 'gr_upgrader';

-- CREATE USER 'gr_upgrader'@'localhost' IDENTIFIED WITH mysql_native_password;   -- upgrader_password plugin mysql_native_password
-- SET old_passwords = 0;
-- SET PASSWORD FOR 'gr_upgrader'@'localhost' = PASSWORD('gr_upgrader');

SET @stmt_create_user = CONCAT('CREATE USER ', @user, '@', @host, ' IDENTIFIED WITH mysql_native_password');
PREPARE stmt_create_user FROM @stmt_create_user;
EXECUTE stmt_create_user;
DEALLOCATE PREPARE stmt_create_user;

SET old_passwords = 0;

SET @stmt_set_upgrader_password = CONCAT('SET PASSWORD FOR ', @user, '@', @host, ' = PASSWORD("', @upgrader_password, '")');
PREPARE stmt_set_upgrader_password FROM @stmt_set_upgrader_password;
EXECUTE stmt_set_upgrader_password;
DEALLOCATE PREPARE stmt_set_upgrader_password;

-- SHOW GRANTS FOR 'gr_upgrader'@'localhost';

SET @stmt_grant = CONCAT('GRANT ALL PRIVILEGES ON ', @database, '.* TO ', @user, '@', @host, ' WITH GRANT OPTION');
PREPARE stmt_grant FROM @stmt_grant;
EXECUTE stmt_grant;
DEALLOCATE PREPARE stmt_grant;