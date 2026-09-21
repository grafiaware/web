-- Test databases + users for local / CI PHPUnit integration runs.
-- Compose maps host port 3307 -> container 3306.

CREATE DATABASE IF NOT EXISTS web_red_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS events_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS auth_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Unified CI / Docker user (set TEST_DB_USER=webtest)
CREATE USER IF NOT EXISTS 'webtest'@'%' IDENTIFIED BY 'webtest';
GRANT ALL PRIVILEGES ON web_red_test.* TO 'webtest'@'%';
GRANT ALL PRIVILEGES ON events_test.* TO 'webtest'@'%';
GRANT ALL PRIVILEGES ON auth_test.* TO 'webtest'@'%';

-- Users matching default NajdiSi local account names (when TEST_DB_USER is unset)
CREATE USER IF NOT EXISTS 'na_admin'@'%' IDENTIFIED BY 'na_admin';
CREATE USER IF NOT EXISTS 'na_everyone'@'%' IDENTIFIED BY 'na_everyone';
CREATE USER IF NOT EXISTS 'na_auth'@'%' IDENTIFIED BY 'na_auth';
GRANT ALL PRIVILEGES ON web_red_test.* TO 'na_admin'@'%';
GRANT ALL PRIVILEGES ON web_red_test.* TO 'na_everyone'@'%';
GRANT ALL PRIVILEGES ON web_red_test.* TO 'na_auth'@'%';

CREATE USER IF NOT EXISTS 'events_everyone'@'%' IDENTIFIED BY 'events_everyone';
GRANT ALL PRIVILEGES ON events_test.* TO 'events_everyone'@'%';

CREATE USER IF NOT EXISTS 'single_login'@'%' IDENTIFIED BY 'single_login';
GRANT ALL PRIVILEGES ON auth_test.* TO 'single_login'@'%';

FLUSH PRIVILEGES;
