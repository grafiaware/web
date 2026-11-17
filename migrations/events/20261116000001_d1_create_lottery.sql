-- D1 (#46): losování – losování, kola, ceny, anketa, losy, kód návštěvníka, přítomnost, výhry. Zadání docs/vp/zadani/D1.md
CREATE TABLE lottery (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(200) NOT NULL,
  description TEXT NULL,
  online TINYINT(1) NOT NULL DEFAULT 0,
  status VARCHAR(20) CHARACTER SET ascii NOT NULL DEFAULT 'draft',
  join_from DATETIME NULL DEFAULT NULL,
  join_until DATETIME NULL DEFAULT NULL,
  rules_url VARCHAR(500) NULL DEFAULT NULL,
  pickup_from DATE NULL DEFAULT NULL,
  pickup_until DATE NULL DEFAULT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE lottery_round (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lottery_id_fk INT UNSIGNED NOT NULL,
  draw_at DATETIME NOT NULL,
  substitute TINYINT(1) NOT NULL DEFAULT 0,
  status VARCHAR(20) CHARACTER SET ascii NOT NULL DEFAULT 'planned',
  drawn DATETIME NULL DEFAULT NULL,
  drawn_by VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY lottery_round_lottery_idx (lottery_id_fk, draw_at),
  CONSTRAINT lottery_round_lottery_fk FOREIGN KEY (lottery_id_fk) REFERENCES lottery (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE lottery_prize (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lottery_id_fk INT UNSIGNED NOT NULL,
  round_id_fk INT UNSIGNED NULL DEFAULT NULL,
  name VARCHAR(200) NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  company_id_fk INT UNSIGNED NULL DEFAULT NULL,
  priority INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY lottery_prize_lottery_idx (lottery_id_fk),
  KEY lottery_prize_round_idx (round_id_fk),
  KEY lottery_prize_company_idx (company_id_fk),
  CONSTRAINT lottery_prize_lottery_fk FOREIGN KEY (lottery_id_fk) REFERENCES lottery (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT lottery_prize_round_fk FOREIGN KEY (round_id_fk) REFERENCES lottery_round (id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT lottery_prize_company_fk FOREIGN KEY (company_id_fk) REFERENCES company (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE lottery_question (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lottery_id_fk INT UNSIGNED NOT NULL,
  position INT NOT NULL DEFAULT 0,
  text VARCHAR(500) NOT NULL,
  type VARCHAR(20) CHARACTER SET ascii NOT NULL DEFAULT 'single',
  options TEXT NULL,
  required TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY lottery_question_lottery_idx (lottery_id_fk, position),
  CONSTRAINT lottery_question_lottery_fk FOREIGN KEY (lottery_id_fk) REFERENCES lottery (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE lottery_answer (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  question_id_fk INT UNSIGNED NOT NULL,
  login_login_name_fk VARCHAR(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  answer TEXT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY lottery_answer_unique (question_id_fk, login_login_name_fk),
  KEY lottery_answer_login_idx (login_login_name_fk),
  CONSTRAINT lottery_answer_question_fk FOREIGN KEY (question_id_fk) REFERENCES lottery_question (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT lottery_answer_login_fk FOREIGN KEY (login_login_name_fk) REFERENCES login (login_name) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE lottery_ticket (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lottery_id_fk INT UNSIGNED NOT NULL,
  login_login_name_fk VARCHAR(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  reason VARCHAR(30) CHARACTER SET ascii NOT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  removed DATETIME NULL DEFAULT NULL,
  removed_by VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY lottery_ticket_unique (lottery_id_fk, login_login_name_fk, reason),
  KEY lottery_ticket_login_idx (login_login_name_fk),
  CONSTRAINT lottery_ticket_lottery_fk FOREIGN KEY (lottery_id_fk) REFERENCES lottery (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT lottery_ticket_login_fk FOREIGN KEY (login_login_name_fk) REFERENCES login (login_name) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE visitor_code (
  login_login_name_fk VARCHAR(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  code CHAR(12) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (login_login_name_fk),
  UNIQUE KEY visitor_code_code_unique (code),
  CONSTRAINT visitor_code_login_fk FOREIGN KEY (login_login_name_fk) REFERENCES login (login_name) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE lottery_presence (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lottery_id_fk INT UNSIGNED NOT NULL,
  login_login_name_fk VARCHAR(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  issued DATETIME NOT NULL,
  issued_by VARCHAR(100) NULL DEFAULT NULL,
  eligible_from DATETIME NOT NULL,
  valid_until DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY lottery_presence_unique (lottery_id_fk, login_login_name_fk),
  KEY lottery_presence_login_idx (login_login_name_fk),
  CONSTRAINT lottery_presence_lottery_fk FOREIGN KEY (lottery_id_fk) REFERENCES lottery (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT lottery_presence_login_fk FOREIGN KEY (login_login_name_fk) REFERENCES login (login_name) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE lottery_presence_event (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  presence_id_fk INT UNSIGNED NOT NULL,
  event VARCHAR(30) CHARACTER SET ascii NOT NULL,
  station VARCHAR(30) CHARACTER SET ascii NULL DEFAULT NULL,
  scanned DATETIME NULL DEFAULT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by VARCHAR(100) NULL DEFAULT NULL,
  valid_until DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY lottery_presence_event_presence_idx (presence_id_fk, created),
  CONSTRAINT lottery_presence_event_presence_fk FOREIGN KEY (presence_id_fk) REFERENCES lottery_presence (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE lottery_winner (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lottery_id_fk INT UNSIGNED NOT NULL,
  round_id_fk INT UNSIGNED NULL DEFAULT NULL,
  prize_id_fk INT UNSIGNED NOT NULL,
  login_login_name_fk VARCHAR(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  replaces_id_fk INT UNSIGNED NULL DEFAULT NULL,
  drawn DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  drawn_by VARCHAR(100) NULL DEFAULT NULL,
  status VARCHAR(20) CHARACTER SET ascii NOT NULL DEFAULT 'drawn',
  claimed DATETIME NULL DEFAULT NULL,
  claimed_by VARCHAR(100) NULL DEFAULT NULL,
  forfeited DATETIME NULL DEFAULT NULL,
  forfeited_by VARCHAR(100) NULL DEFAULT NULL,
  notified DATETIME NULL DEFAULT NULL,
  pickup_until DATE NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY lottery_winner_lottery_idx (lottery_id_fk, login_login_name_fk),
  KEY lottery_winner_round_idx (round_id_fk),
  KEY lottery_winner_prize_idx (prize_id_fk),
  KEY lottery_winner_login_idx (login_login_name_fk),
  KEY lottery_winner_replaces_idx (replaces_id_fk),
  CONSTRAINT lottery_winner_lottery_fk FOREIGN KEY (lottery_id_fk) REFERENCES lottery (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT lottery_winner_round_fk FOREIGN KEY (round_id_fk) REFERENCES lottery_round (id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT lottery_winner_prize_fk FOREIGN KEY (prize_id_fk) REFERENCES lottery_prize (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT lottery_winner_login_fk FOREIGN KEY (login_login_name_fk) REFERENCES login (login_name) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT lottery_winner_replaces_fk FOREIGN KEY (replaces_id_fk) REFERENCES lottery_winner (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
