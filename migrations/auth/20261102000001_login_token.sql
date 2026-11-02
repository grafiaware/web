-- A1+A2 (#44): jednorázové přihlašovací odkazy (v DB jen SHA-256 hash tokenu) a index na e-mail registrace.
-- login_name_fk má znakovou sadu rodiče (login je utf8mb3), jinak cizí klíč nevznikne.

CREATE TABLE login_token (
  token_hash CHAR(64) CHARACTER SET ascii NOT NULL,
  login_name_fk VARCHAR(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  purpose VARCHAR(20) CHARACTER SET ascii NOT NULL DEFAULT 'login',
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires DATETIME NOT NULL,
  used DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (token_hash),
  KEY login_token_login_idx (login_name_fk, created),
  CONSTRAINT login_token_login_fk FOREIGN KEY (login_name_fk) REFERENCES login (login_name) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE registration ADD INDEX registration_email_idx (email);
