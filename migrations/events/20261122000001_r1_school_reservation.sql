-- R1 (#48): rezervace míst na přednáškách pro školy. Zadání docs/vp/zadani/R1.md
-- Jen přidává: sloupec event.capacity a tři nové tabulky. MySQL i MariaDB.

ALTER TABLE event ADD COLUMN capacity INT UNSIGNED NULL DEFAULT NULL;

CREATE TABLE reservation_offer (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(50) CHARACTER SET ascii NOT NULL,
  name VARCHAR(200) NOT NULL,
  event_date DATE NOT NULL,
  status VARCHAR(20) CHARACTER SET ascii NOT NULL DEFAULT 'draft',
  booking_from DATETIME NULL DEFAULT NULL,
  booking_until DATETIME NULL DEFAULT NULL,
  change_until DATETIME NULL DEFAULT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY reservation_offer_code_unique (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE reservation_offer_event (
  offer_id_fk INT UNSIGNED NOT NULL,
  event_id_fk INT UNSIGNED NOT NULL,
  PRIMARY KEY (offer_id_fk, event_id_fk),
  KEY reservation_offer_event_event_idx (event_id_fk),
  CONSTRAINT reservation_offer_event_offer_fk FOREIGN KEY (offer_id_fk) REFERENCES reservation_offer (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT reservation_offer_event_event_fk FOREIGN KEY (event_id_fk) REFERENCES event (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE school_reservation (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  offer_id_fk INT UNSIGNED NOT NULL,
  event_id_fk INT UNSIGNED NOT NULL,
  status VARCHAR(20) CHARACTER SET ascii NOT NULL DEFAULT 'active',
  pupils INT UNSIGNED NOT NULL,
  teachers INT UNSIGNED NOT NULL DEFAULT 1,
  school_name VARCHAR(200) NOT NULL,
  school_place VARCHAR(100) NOT NULL,
  field_code VARCHAR(5) CHARACTER SET ascii NOT NULL,
  school_class VARCHAR(50) NULL DEFAULT NULL,
  contact_email VARCHAR(200) NOT NULL,
  contact_name VARCHAR(200) NULL DEFAULT NULL,
  contact_phone VARCHAR(30) NULL DEFAULT NULL,
  phone_consent_at DATETIME NULL DEFAULT NULL,
  phone_deleted_at DATETIME NULL DEFAULT NULL,
  note TEXT NULL,
  consent_at DATETIME NOT NULL,
  login_login_name_fk VARCHAR(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  manage_token_hash CHAR(64) CHARACTER SET ascii NOT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated DATETIME NULL DEFAULT NULL,
  cancelled_at DATETIME NULL DEFAULT NULL,
  cancelled_by VARCHAR(100) NULL DEFAULT NULL,
  anonymized_at DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY school_reservation_token_unique (manage_token_hash),
  KEY school_reservation_event_idx (event_id_fk, status),
  KEY school_reservation_offer_email_idx (offer_id_fk, contact_email),
  KEY school_reservation_login_idx (login_login_name_fk),
  CONSTRAINT school_reservation_offer_fk FOREIGN KEY (offer_id_fk) REFERENCES reservation_offer (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT school_reservation_event_fk FOREIGN KEY (event_id_fk) REFERENCES event (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT school_reservation_login_fk FOREIGN KEY (login_login_name_fk) REFERENCES login (login_name) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
