-- F1–F3 (#47): role media_manager – zakládá odkazy, určuje cíl, vidí přehled. Zadání docs/vp/zadani/F1.md.
-- INSERT IGNORE: opakované spuštění nic nezmění (MySQL i MariaDB).

INSERT IGNORE INTO role (role) VALUES ('media_manager');
