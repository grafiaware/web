-- Pro UŽ BĚŽÍCÍ Docker volume (init/*.sql se znovu nespouští).
-- Vytvoří/obnoví lokální účty auth + events podle NajdiSi non-prod konfigurace.
-- Nemaže data v DB — jen MySQL users + GRANT.
--
-- Spuštění (jeden řádek):
--   bash:       docker compose -f docker-compose.migrations.yml exec -T mysql mysql -uroot -proot < docker/migrations/grant-auth-events.sql
--   PowerShell: Get-Content docker/migrations/grant-auth-events.sql -Raw | docker compose -f docker-compose.migrations.yml exec -T mysql mysql -uroot -proot

CREATE DATABASE IF NOT EXISTS `najdisi` CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE DATABASE IF NOT EXISTS `single_login` CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE DATABASE IF NOT EXISTS `events` CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Auth (ConfigurationAuth::login) → DB single_login
CREATE USER IF NOT EXISTS 'single_login'@'%' IDENTIFIED BY 'single_login';
ALTER USER 'single_login'@'%' IDENTIFIED BY 'single_login';
GRANT ALL PRIVILEGES ON `single_login`.* TO 'single_login'@'%';

-- Events (EventsModelContainerConfigurator) → DB events
CREATE USER IF NOT EXISTS 'events_everyone'@'%' IDENTIFIED BY 'events_everyone';
ALTER USER 'events_everyone'@'%' IDENTIFIED BY 'events_everyone';
GRANT ALL PRIVILEGES ON `events`.* TO 'events_everyone'@'%';

-- Red / Build (pro jistotu obnovení po dump importu)
CREATE USER IF NOT EXISTS 'na_everyone'@'%' IDENTIFIED BY 'na_everyone';
CREATE USER IF NOT EXISTS 'na_auth'@'%' IDENTIFIED BY 'na_auth';
CREATE USER IF NOT EXISTS 'na_admin'@'%' IDENTIFIED BY 'na_admin';
CREATE USER IF NOT EXISTS 'na_upgrader'@'%' IDENTIFIED BY 'na_upgrader';
ALTER USER 'na_everyone'@'%' IDENTIFIED BY 'na_everyone';
ALTER USER 'na_auth'@'%' IDENTIFIED BY 'na_auth';
ALTER USER 'na_admin'@'%' IDENTIFIED BY 'na_admin';
ALTER USER 'na_upgrader'@'%' IDENTIFIED BY 'na_upgrader';

GRANT ALL PRIVILEGES ON `najdisi`.* TO 'na_everyone'@'%';
GRANT ALL PRIVILEGES ON `najdisi`.* TO 'na_auth'@'%';
GRANT ALL PRIVILEGES ON `najdisi`.* TO 'na_admin'@'%';
GRANT ALL PRIVILEGES ON `najdisi`.* TO 'na_upgrader'@'%';
GRANT ALL PRIVILEGES ON `single_login`.* TO 'na_upgrader'@'%';
GRANT ALL PRIVILEGES ON `events`.* TO 'na_everyone'@'%';
GRANT ALL PRIVILEGES ON `events`.* TO 'na_auth'@'%';
GRANT ALL PRIVILEGES ON `events`.* TO 'na_admin'@'%';
GRANT ALL PRIVILEGES ON `events`.* TO 'na_upgrader'@'%';
GRANT CREATE, DROP, ALTER, INDEX, REFERENCES, CREATE USER ON *.* TO 'na_upgrader'@'%';

FLUSH PRIVILEGES;
