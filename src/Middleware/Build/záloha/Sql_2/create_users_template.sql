-- assume that the MySQL root account has the CREATE USER privilege and all privileges that it grants to other accounts.
-- CREATE USER 'gr_upgrader'@'localhost' IDENTIFIED BY 'gr_upgrader';

-- CREATE USER 'gr_upgrader'@'localhost' IDENTIFIED WITH mysql_native_password;   -- upgrader_password plugin mysql_native_password
-- SET old_passwords = 0;
-- SET PASSWORD FOR 'gr_upgrader'@'localhost' = PASSWORD('gr_upgrader');

CREATE USER {{login_user}}@{{host}} IDENTIFIED WITH mysql_native_password;
SET old_passwords = 0;
SET PASSWORD FOR {{login_user}}@{{host}} = PASSWORD("{{login_password}}");
GRANT SELECT ON {{login_database}}.* TO {{login_user}}@{{host}};

CREATE USER {{everyone_user}}@{{host}} IDENTIFIED WITH mysql_native_password;
SET old_passwords = 0;
SET PASSWORD FOR {{everyone_user}}@{{host}} = PASSWORD("{{everyone_password}}");
GRANT SELECT ON {{database}}.* TO {{everyone_user}}@{{host}};

CREATE USER {{authenticated_user}}@{{host}} IDENTIFIED WITH mysql_native_password;
SET old_passwords = 0;
SET PASSWORD FOR {{authenticated_user}}@{{host}} = PASSWORD("{{authenticated_password}}");
GRANT SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON {{database}}.* TO {{authenticated_user}}@{{host}} WITH GRANT OPTION;

CREATE USER {{administrator_user}}@{{host}} IDENTIFIED WITH mysql_native_password;
SET old_passwords = 0;
SET PASSWORD FOR {{administrator_user}}@{{host}} = PASSWORD("{{administrator_password}}");
GRANT ALL PRIVILEGES ON {{database}}.* TO {{administrator_user}}@{{host}} WITH GRANT OPTION;

-- SHOW GRANTS FOR 'gr_upgrader'@'localhost';
