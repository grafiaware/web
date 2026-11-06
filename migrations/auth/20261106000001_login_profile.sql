-- A8 (#85): osobní jméno uživatele (zobrazované jméno), docs/vp/zadani/A8.md.
-- Samostatná tabulka – tabulka login a LoginDao zůstávají beze změny (ostatní sites).
-- login_name_fk má znakovou sadu rodiče (login je utf8mb3), jinak cizí klíč nevznikne.

CREATE TABLE login_profile (
  login_name_fk VARCHAR(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  display_name VARCHAR(100) NULL DEFAULT NULL,
  updated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (login_name_fk),
  CONSTRAINT login_profile_login_fk FOREIGN KEY (login_name_fk) REFERENCES login (login_name)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
