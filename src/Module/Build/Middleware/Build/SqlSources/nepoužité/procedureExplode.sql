-- rozloží string pSTR podle dilimiteru pDelim a vytvoří temporary tabulku temp_explode s výsledkem
-- delimeter můře být jen znak i více znaků , ale nesmí být \ (opačné lomítko) - to vede k zacyklení y vytvoření nekonečného řetězce

-- volání:

-- SET @str  = "The quick brown fox jumped over the lazy dog";
-- SET @delim = " ";

-- CALL explode(@delim,@str);
-- SELECT id,word FROM temp_explode;

CREATE PROCEDURE explode( pDelim VARCHAR(32), pStr TEXT)
BEGIN
  DROP TABLE IF EXISTS temp_explode;
  CREATE TEMPORARY TABLE temp_explode (id INT AUTO_INCREMENT PRIMARY KEY NOT NULL, word VARCHAR(40));
-- vytvoří řetězec se sérií insertů pro jednotlivá slova
  SET @sql := CONCAT('INSERT INTO temp_explode (word) VALUES (',  REPLACE(  QUOTE(pStr), pDelim, '\' ), (\'' ), ')' );
  PREPARE myStmt FROM @sql;
  EXECUTE myStmt;
END
