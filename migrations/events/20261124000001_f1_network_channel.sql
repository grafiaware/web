-- F1 (#47): kód kanálu u sociálních sítí – jedno místo zdroje dat pro seznam sítí (docs/vp/zadani/F1.md).
-- Kanály sítí a webu odkazů (/events/v1/r/<kód>/<kanál>) se berou z tabulky network.
-- Nové sítě X a TikTok (jen pokud ještě nejsou); objeví se i ve výběru sítí v profilu vystavovatele.
-- Ikony podle Fomantic UI 2.9.2 (twitter, tiktok). MySQL i MariaDB.

ALTER TABLE network ADD COLUMN channel VARCHAR(10) CHARACTER SET ascii NULL DEFAULT NULL;

ALTER TABLE network ADD UNIQUE KEY network_channel_unique (channel);

UPDATE network SET channel = 'fb' WHERE icon = 'facebook' AND channel IS NULL;

UPDATE network SET channel = 'ig' WHERE icon = 'instagram' AND channel IS NULL;

UPDATE network SET channel = 'li' WHERE icon = 'linkedin' AND channel IS NULL;

UPDATE network SET channel = 'yt' WHERE icon = 'youtube' AND channel IS NULL;

UPDATE network SET channel = 'web' WHERE icon = 'globe' AND channel IS NULL;

INSERT INTO network (icon, title, embed_code_template, channel)
  SELECT 'twitter', 'X', '', 'x' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM network WHERE channel = 'x' OR icon = 'twitter');

INSERT INTO network (icon, title, embed_code_template, channel)
  SELECT 'tiktok', 'TikTok', '', 'tt' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM network WHERE channel = 'tt' OR icon = 'tiktok');
