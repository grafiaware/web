-- F3 (#47): QR kódy pro tištěná média z QRdirectoru, nahrané jako SVG k odkazu (docs/vp/zadani/F1.md).
-- SVG se před uložením vyčistí (jen tvary QR, žádné skripty ani odkazy); decoded_url = adresa přečtená z QR v prohlížeči.
-- Jen nová tabulka. MySQL i MariaDB.

CREATE TABLE promo_qr_svg (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  link_id_fk INT UNSIGNED NOT NULL,
  channel VARCHAR(10) CHARACTER SET ascii NOT NULL,
  label VARCHAR(100) NOT NULL,
  svg MEDIUMTEXT NOT NULL,
  decoded_url VARCHAR(1000) NULL DEFAULT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY promo_qr_svg_link_idx (link_id_fk),
  CONSTRAINT promo_qr_svg_link_fk FOREIGN KEY (link_id_fk) REFERENCES promo_link (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
