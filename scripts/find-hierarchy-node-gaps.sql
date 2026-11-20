-- Najde díry v číslování nested setu v tabulce hierarchy.
--
-- Jen čte, nic nezapisuje. Není to migrace.
-- Díra = souvislý úsek celých čísel od 1 do nejvyššího kladného left_node/right_node,
-- které nejsou ani left_node, ani right_node.
-- Záporné souřadnice (přesun podstromu je dočasně používá) se do číslování nepočítají
-- a vypíší se zvlášť. Číslo použité víckrát také není díra a vypíše se zvlášť.
-- Běží na MySQL 5.6, MySQL 8 i MariaDB (bez WITH a bez okenních funkcí).
-- Spuštění: mysql najdisi < scripts/find-hierarchy-node-gaps.sql

-- 1. souvislé díry
-- Čísla se seřadí v odvozené tabulce (LIMIT donutí MySQL 5.6 pořadí zachovat).
-- @prev se čte v prvních výrazech a přepisuje se až v posledním sloupci _n.
SELECT gap_start, gap_end, width,
       CASE WHEN width % 2 = 1 THEN 'lichá šířka' ELSE '' END AS poznamka
FROM (
    SELECT @prev + 1 AS gap_start,
           n - 1 AS gap_end,
           n - @prev - 1 AS width,
           @prev := n AS _n
    FROM (SELECT @prev := 0) AS init
    JOIN (
        SELECT n
        FROM (
            SELECT left_node AS n FROM hierarchy WHERE left_node > 0
            UNION
            SELECT right_node AS n FROM hierarchy WHERE right_node > 0
        ) nums
        ORDER BY n
        LIMIT 18446744073709551615
    ) ordered
) gaps
WHERE width > 0
ORDER BY gap_start;

-- 2. čísla použitá víckrát (překryv, ne díra)
SELECT n, COUNT(*) AS krat
FROM (
    SELECT left_node AS n FROM hierarchy WHERE left_node > 0
    UNION ALL
    SELECT right_node FROM hierarchy WHERE right_node > 0
) endpoints
GROUP BY n
HAVING COUNT(*) > 1
ORDER BY n;

-- 3. záporné nebo nulové souřadnice
SELECT uid, left_node, right_node
FROM hierarchy
WHERE left_node <= 0 OR right_node <= 0
ORDER BY left_node, right_node;
