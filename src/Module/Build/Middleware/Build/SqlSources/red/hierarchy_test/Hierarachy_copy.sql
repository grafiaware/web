SET @sourceId := '6021a22f7ef27';
SET @targetId := '6299be00d5f2d';
SELECT left_node, right_node, right_node-left_node+1 INTO @source_left_node, @source_right_node, @source_width
FROM hierarchy WHERE uid = @sourceId;

-- vyřazení zdrojovéjo podstromu z nested set
--            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = 0-left_node, right_node = 0-right_node
--                WHERE left_node BETWEEN @source_left_node AND @source_right_node");

-- odstraň prostor zbylý po vyřazeném podstromu
--            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET right_node = right_node-@source_width WHERE right_node > @source_right_node");
--            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = left_node-@source_width WHERE left_node > @source_right_node");

-- data cílového uzlu (načtou se až po odstranění prostoru zbylého po přesunovaném stromu)
SELECT left_node INTO @target_left_node
FROM hierarchy WHERE uid = @targetId;

-- vytvoř cílový volný prostor
UPDATE hierarchy SET right_node = right_node+@source_width WHERE right_node >= @target_left_node;
UPDATE hierarchy SET left_node = left_node+@source_width WHERE left_node > @target_left_node;
            
-- UPDATE hierarchy SET
--                left_node = 0 - left_node - (@source_left_node - @target_left_node - 1),
--                right_node = 0 - right_node - (@source_left_node - @target_left_node - 1)
--                WHERE left_node < 0;
INSERT INTO hierarchy (uid, left_node, right_node)
SELECT concat('??', hex(uuid_short())), left_node - (@source_left_node - @target_left_node + 1), right_node - (@source_left_node - @target_left_node + 1) FROM hierarchy WHERE left_node BETWEEN @source_left_node AND @source_right_node;

-- insert menu_item
-- bez id - autoincrement
INSERT INTO menu_item (lang_code_fk, uid_fk, type_fk, `list`, `order`, title, prettyuri, active, auto_generated)

SELECT lang_code_fk, t.uid, type_fk, `list`, `order`, title, prettyuri, active, auto_generated 
FROM 
menu_item 
INNER JOIN 
hierarchy AS t 
INNER JOIN
hierarchy AS s
WHERE 
menu_item.uid_fk=s.uid AND  t.left_node= s.left_node - (@source_left_node - @target_left_node + 1)
AND 
s.left_node BETWEEN @source_left_node AND @source_right_node
AND 
t.left_node BETWEEN (@target_left_node + 1) AND (@target_left_node + @source_width) 
;