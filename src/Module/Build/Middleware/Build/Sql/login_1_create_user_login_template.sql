-- CREATE USER vp_login@localhost IDENTIFIED WITH mysql_native_password;
-- SET old_passwords = 0;
-- SET PASSWORD FOR vp_login@localhost = PASSWORD('vp_login');
-- GRANT SELECT ON gr_pracovni.* TO vp_login@localhost WITH GRANT OPTION;

CREATE USER {{authenticated_user}}@{{host}} IDENTIFIED WITH mysql_native_password;
SET old_passwords = 0;
SET PASSWORD FOR {{authenticated_user}}@{{host}} = PASSWORD("{{authenticated_password}}");
GRANT SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON {{database}}.* TO {{authenticated_user}}@{{host}} WITH GRANT OPTION;