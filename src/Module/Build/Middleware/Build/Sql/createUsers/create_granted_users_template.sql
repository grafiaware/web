-- assume that the MySQL root account has the CREATE USER privilege and all privileges that it grants to other accounts.
-- CREATE USER 'gr_upgrader'@'localhost' IDENTIFIED BY 'gr_upgrader';

-- CREATE USER 'gr_upgrader'@'localhost' IDENTIFIED WITH mysql_native_password;   -- upgrader_password plugin mysql_native_password
-- SET old_passwords = 0;
-- SET PASSWORD FOR 'gr_upgrader'@'localhost' = PASSWORD('gr_upgrader');

CREATE USER {{authenticated_user}}@{{host}} IDENTIFIED WITH mysql_native_password;
SET old_passwords = 0;
SET PASSWORD FOR {{authenticated_user}}@{{host}} = PASSWORD({{authenticated_password}});
GRANT SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON {{database}}.* TO {{authenticated_user}}@{{host}} WITH GRANT OPTION;

CREATE USER {{administrator_user}}@{{host}} IDENTIFIED WITH mysql_native_password;
SET old_passwords = 0;
SET PASSWORD FOR {{administrator_user}}@{{host}} = PASSWORD({{administrator_password}});
GRANT ALL PRIVILEGES ON {{database}}.* TO {{administrator_user}}@{{host}} WITH GRANT OPTION;

-- SHOW GRANTS FOR 'gr_upgrader'@'localhost';
