-- MySQL 5.6 neumí DROP USER IF EXISTS - až 5.7
DROP USER {{authenticated_user}}@{{host}}, {{administrator_user}}@{{host}};
