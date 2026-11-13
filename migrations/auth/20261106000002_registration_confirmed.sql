-- A3 (#45): čas potvrzení registrace, docs/vp/zadani/A3.md.
-- Doplnění: potvrzené registrace (existuje credentials) dostanou čas vzniku credentials. UPDATE je idempotentní.

ALTER TABLE registration ADD COLUMN confirmed DATETIME NULL DEFAULT NULL AFTER email_time;

UPDATE registration r JOIN credentials c ON c.login_name_fk = r.login_name_fk
  SET r.confirmed = c.created
  WHERE r.confirmed IS NULL;
