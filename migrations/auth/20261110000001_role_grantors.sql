-- A9 (#93): role pro přidělování rolí, docs/vp/zadani/A9.md.
-- global_role_grantor se do tabulky role NEZAPISUJE (pseudorole z konfigurace, nejde přidělit).
-- red_administrator je v kódu (RoleEnum) už dřív, v tabulce chyběla.
-- INSERT IGNORE: opakované spuštění nic nezmění (MySQL i MariaDB).

INSERT IGNORE INTO role (role) VALUES
  ('auth_role_grantor'),
  ('red_role_grantor'),
  ('events_role_grantor'),
  ('red_administrator');
