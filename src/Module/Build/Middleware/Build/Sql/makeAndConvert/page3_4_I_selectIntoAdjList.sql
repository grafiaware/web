/**
 * Author: 
 * Created: 
 */

-- bloky
-- potomci s list s podrtžítky
-- ostatní položky s různými hodnotami list
INSERT INTO menu_adjlist (child, parent, poradi, level)
		
    SELECT DISTINCT

        childrens.list AS child, 
	parents.list AS parent,
        if ( childrens.poradi<0, 0,  childrens.poradi ) AS poradi,
        parents.level AS level

        FROM        
            (SELECT stranky_innodb.list,			
		if (stranky_innodb.list = {{root}} , 1,
					if(stranky_innodb.list IN ( {{in_menu_roots}} ), 2,
					                        length(stranky_innodb.list)/3+2 ) ) AS level
            FROM stranky_innodb) AS parents
            CROSS JOIN
            (SELECT    list,  
                       poradi 
            FROM stranky_innodb) AS childrens	
            WHERE (
                parents.list=SUBSTRING(childrens.list,1,LENGTH(childrens.list)-3) AND SUBSTRING(childrens.list,4,1)='_'
                    OR
                parents.list = {{root}}  AND childrens.list <> {{root}}  AND 
                childrens.list NOT IN ( {{in_menu_roots}}) AND 
                LENGTH(childrens.list) > 3 AND
                SUBSTRING(childrens.list,4,1)<>'_'
            )