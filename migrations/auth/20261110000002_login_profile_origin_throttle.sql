-- A9 (#93): typ účtu, poslední přihlášení a zpomalené přihlašování v login_profile, docs/vp/zadani/A9.md.
-- Tabulky login a credentials se nemění (ostatní sites).
-- origin: self = registrace přes veřejný formulář, admin = založen v administraci, legacy = starší účet bez registrace.
-- MySQL i MariaDB: bez ADD COLUMN IF NOT EXISTS.

ALTER TABLE login_profile
  ADD COLUMN origin ENUM('self','admin','legacy') NULL DEFAULT NULL AFTER display_name,
  ADD COLUMN last_login DATETIME NULL DEFAULT NULL AFTER origin,
  ADD COLUMN failed_count INT NOT NULL DEFAULT 0 AFTER last_login,
  ADD COLUMN failed_last DATETIME NULL DEFAULT NULL AFTER failed_count,
  ADD COLUMN locked_until DATETIME NULL DEFAULT NULL AFTER failed_last;

-- řádek pro každý účet (dosud jen účty s osobním jménem)
INSERT IGNORE INTO login_profile (login_name_fk)
  SELECT login_name FROM login;

-- typ stávajících účtů: s registrací a e-mailem self, ostatní legacy (účty z administrace dostanou admin při založení)
UPDATE login_profile p
  LEFT JOIN registration r ON r.login_name_fk = p.login_name_fk
  SET p.origin = IF(r.login_name_fk IS NOT NULL AND TRIM(COALESCE(r.email, '')) <> '', 'self', 'legacy'),
      p.updated = p.updated
  WHERE p.origin IS NULL;
