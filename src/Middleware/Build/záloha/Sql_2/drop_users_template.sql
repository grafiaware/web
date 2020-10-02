-- MySQL 5.6 neumí DROP USER IF EXISTS - až 5.7
DROP USER {{login_user}}@{{host}}, {{everyone_user}}@{{host}}, {{authenticated_user}}@{{host}}, {{administrator_user}}@{{host}};
