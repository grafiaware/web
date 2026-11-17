-- D1c (#108): papírové anketní lístky v losování – sada lístků s číslem a kódem, přítomnost a výhra lístku. Zadání docs/vp/zadani/D1c.md
CREATE TABLE lottery_paper_ticket (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lottery_id_fk INT UNSIGNED NOT NULL,
  number INT UNSIGNED NOT NULL,
  code CHAR(12) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  login_login_name_fk VARCHAR(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY lottery_paper_ticket_number_unique (lottery_id_fk, number),
  UNIQUE KEY lottery_paper_ticket_code_unique (code),
  KEY lottery_paper_ticket_login_idx (login_login_name_fk),
  CONSTRAINT lottery_paper_ticket_lottery_fk FOREIGN KEY (lottery_id_fk) REFERENCES lottery (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT lottery_paper_ticket_login_fk FOREIGN KEY (login_login_name_fk) REFERENCES login (login_name) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE lottery_presence
  ADD COLUMN paper_ticket_id_fk INT UNSIGNED NULL DEFAULT NULL,
  ADD UNIQUE KEY lottery_presence_paper_unique (paper_ticket_id_fk),
  ADD CONSTRAINT lottery_presence_paper_fk FOREIGN KEY (paper_ticket_id_fk) REFERENCES lottery_paper_ticket (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE lottery_winner
  ADD COLUMN paper_ticket_id_fk INT UNSIGNED NULL DEFAULT NULL,
  ADD KEY lottery_winner_paper_idx (paper_ticket_id_fk),
  ADD CONSTRAINT lottery_winner_paper_fk FOREIGN KEY (paper_ticket_id_fk) REFERENCES lottery_paper_ticket (id) ON DELETE CASCADE ON UPDATE CASCADE;
