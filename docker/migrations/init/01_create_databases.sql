-- Inicializace při prvním startu volume (docker-entrypoint-initdb.d).
-- Jména DB a účtů odpovídají lokální konfiguraci site NajdiSi
-- (ConfigurationRed / Auth / Events / Build — PES_RUNNING_ON_PRODUCTION_HOST = false).

CREATE DATABASE IF NOT EXISTS `najdisi` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `single_login` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `events` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Účty red / web (ConfigurationRed::api / web)
CREATE USER IF NOT EXISTS 'na_everyone'@'%' IDENTIFIED BY 'na_everyone';
CREATE USER IF NOT EXISTS 'na_auth'@'%' IDENTIFIED BY 'na_auth';
CREATE USER IF NOT EXISTS 'na_admin'@'%' IDENTIFIED BY 'na_admin';
CREATE USER IF NOT EXISTS 'na_upgrader'@'%' IDENTIFIED BY 'na_upgrader';

-- Auth DB (ConfigurationAuth::login) — účet i DB jméno single_login
CREATE USER IF NOT EXISTS 'single_login'@'%' IDENTIFIED BY 'single_login';

-- Events DB (EventsModelContainerConfigurator) — events_everyone
CREATE USER IF NOT EXISTS 'events_everyone'@'%' IDENTIFIED BY 'events_everyone';

GRANT ALL PRIVILEGES ON `najdisi`.* TO 'na_everyone'@'%';
GRANT ALL PRIVILEGES ON `najdisi`.* TO 'na_auth'@'%';
GRANT ALL PRIVILEGES ON `najdisi`.* TO 'na_admin'@'%';
GRANT ALL PRIVILEGES ON `najdisi`.* TO 'na_upgrader'@'%';

GRANT ALL PRIVILEGES ON `single_login`.* TO 'single_login'@'%';
GRANT ALL PRIVILEGES ON `single_login`.* TO 'na_upgrader'@'%';

GRANT ALL PRIVILEGES ON `events`.* TO 'events_everyone'@'%';
GRANT ALL PRIVILEGES ON `events`.* TO 'na_upgrader'@'%';
-- lokální pohodlí: red účty mohou číst i events (není nutné pro /build login)
GRANT ALL PRIVILEGES ON `events`.* TO 'na_everyone'@'%';
GRANT ALL PRIVILEGES ON `events`.* TO 'na_auth'@'%';
GRANT ALL PRIVILEGES ON `events`.* TO 'na_admin'@'%';

-- Build (create/drop DB) — upgrader potřebuje globálnější práva na lokálu
GRANT CREATE, DROP, ALTER, INDEX, REFERENCES, CREATE USER ON *.* TO 'na_upgrader'@'%';
GRANT ALL PRIVILEGES ON `najdisi`.* TO 'na_upgrader'@'%';
GRANT ALL PRIVILEGES ON `single_login`.* TO 'na_upgrader'@'%';
GRANT ALL PRIVILEGES ON `events`.* TO 'na_upgrader'@'%';

FLUSH PRIVILEGES;
