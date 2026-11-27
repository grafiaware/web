-- F1 (#47): zdroj registrace (kód odkazu, kanál, UTM), docs/vp/zadani/F1.md.
-- Samostatná tabulka – registration ani login_profile se nemění (ostatní sites).
-- login_name_fk má znakovou sadu rodiče (login je utf8mb3), jinak cizí klíč nevznikne.

CREATE TABLE registration_source (
  login_name_fk VARCHAR(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  link_code VARCHAR(30) CHARACTER SET ascii NULL DEFAULT NULL,
  channel VARCHAR(10) CHARACTER SET ascii NULL DEFAULT NULL,
  utm_source VARCHAR(100) NULL DEFAULT NULL,
  utm_medium VARCHAR(100) NULL DEFAULT NULL,
  utm_campaign VARCHAR(100) NULL DEFAULT NULL,
  landing_at DATETIME NULL DEFAULT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (login_name_fk),
  KEY registration_source_link_idx (link_code, channel),
  CONSTRAINT registration_source_login_fk FOREIGN KEY (login_name_fk) REFERENCES login (login_name)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
