-- assume that the MySQL root account has the CREATE USER privilege and all privileges that it grants to other accounts.
-- CREATE USER 'gr_upgrader'@'localhost' IDENTIFIED BY 'gr_upgrader';

-- CREATE USER 'gr_upgrader'@'localhost' IDENTIFIED WITH mysql_native_password;   -- upgrader_password plugin mysql_native_password
-- SET old_passwords = 0;
-- SET PASSWORD FOR 'gr_upgrader'@'localhost' = PASSWORD('gr_upgrader');

CREATE USER {{everyone_user}}@{{host}} IDENTIFIED WITH mysql_native_password;
SET old_passwords = 0;
SET PASSWORD FOR {{everyone_user}}@{{host}} = PASSWORD("{{everyone_password}}");
GRANT SELECT ON {{database}}.* TO {{everyone_user}}@{{host}};

-- SHOW GRANTS FOR 'gr_upgrader'@'localhost';
