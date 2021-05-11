#Set IDs
-- SET @sourceId := :sourceId; #pc kurzy
-- SET @targetId := :targetId; #prednasky a akce

SELECT uid_fk INTO @sourceId FROM menu_item WHERE title='PC kurzy a školení' LIMIT 1;
SELECT uid_fk INTO @targetId FROM menu_item WHERE title='Přednášky a akce' LIMIT 1;


-- data přesunovaného podstromu
SELECT left_node, right_node, right_node-left_node+1 INTO @source_left_node, @source_right_node, @source_width 
FROM menu_nested_set 
WHERE uid = @sourceId;
-- data cílového uzlu
SELECT left_node INTO @target_left_node 
FROM menu_nested_set 
WHERE uid = @targetId;

#vyřazení podstromu z nested set
UPDATE menu_nested_set SET left_node = 0-left_node, right_node = 0-right_node WHERE left_node BETWEEN @source_left_node AND @source_right_node;
-- zpátky:
-- UPDATE menu_nested_set SET left_node = 0-left_node, right_node = 0-right_node WHERE left_node<0;
SELECT @sourceId, @targetId, @source_left_node, @source_right_node, @source_width ;

-- odstraň prostor zbylý po vyřazeném podstromu
UPDATE menu_nested_set SET right_node = right_node-@source_width WHERE right_node > @source_right_node;
UPDATE menu_nested_set SET left_node = left_node-@source_width WHERE left_node > @source_right_node;

-- vytvoř cílový volný prostor
UPDATE menu_nested_set SET right_node = right_node+@source_width WHERE right_node >= @target_left_node;
UPDATE menu_nested_set SET left_node = left_node+@source_width WHERE left_node > @target_left_node;

UPDATE menu_nested_set SET
   left_node     = 0 - left_node - (@source_left_node - @target_left_node - 1), 
   right_node     = 0 - right_node - (@source_left_node - @target_left_node - 1)
WHERE 
   left_node < 0; #that could be more precise...