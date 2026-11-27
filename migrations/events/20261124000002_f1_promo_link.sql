-- F2 (#47): trvalé odkazy /events/v1/r/<kód>[/<kanál>] s měnitelným cílem a počítadlem návštěv.
-- Zadání docs/vp/zadani/F1.md. Jen nové tabulky. Počítadlo jsou jen součty za den – žádné údaje o návštěvnících.
-- MySQL i MariaDB.

CREATE TABLE promo_link (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(30) CHARACTER SET ascii NOT NULL,
  name VARCHAR(200) NOT NULL,
  owner_type VARCHAR(20) CHARACTER SET ascii NOT NULL,
  company_id_fk INT UNSIGNED NULL DEFAULT NULL,
  target VARCHAR(30) CHARACTER SET ascii NOT NULL,
  target_path VARCHAR(500) NULL DEFAULT NULL,
  note VARCHAR(1000) NULL DEFAULT NULL,
  texts TEXT NULL DEFAULT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by VARCHAR(100) NULL DEFAULT NULL,
  updated DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY promo_link_code_unique (code),
  KEY promo_link_company_idx (company_id_fk),
  CONSTRAINT promo_link_company_fk FOREIGN KEY (company_id_fk) REFERENCES company (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE promo_link_target_log (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  link_id_fk INT UNSIGNED NOT NULL,
  target VARCHAR(30) CHARACTER SET ascii NOT NULL,
  target_path VARCHAR(500) NULL DEFAULT NULL,
  changed DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  changed_by VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY promo_link_target_log_link_idx (link_id_fk),
  CONSTRAINT promo_link_target_log_link_fk FOREIGN KEY (link_id_fk) REFERENCES promo_link (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE promo_link_visit (
  link_id_fk INT UNSIGNED NOT NULL,
  day DATE NOT NULL,
  channel VARCHAR(10) CHARACTER SET ascii NOT NULL,
  visits INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (link_id_fk, day, channel),
  CONSTRAINT promo_link_visit_link_fk FOREIGN KEY (link_id_fk) REFERENCES promo_link (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
