/* page3_2_selectIntoAdjList */
/**
 * Author:  
 * Created: 
 */

-- menu roots
INSERT INTO menu_adjlist (child, parent, poradi, level)

    SELECT DISTINCT

        childrens.list AS child, 
	parents.list AS parent,
        if ( childrens.poradi<0, 0, childrens.poradi ) AS poradi,
        parents.level AS level

    FROM
        
        (SELECT stranky_innodb.list,			
                if( stranky_innodb.list = {{root}} , 1,
                                if(stranky_innodb.list IN ( {{in_menu_roots}} ), 2,
                                           length(stranky_innodb.list)/3+2 ) ) AS level
        FROM stranky_innodb) AS parents        
        CROSS JOIN
        (SELECT  list,  
                 poradi         
        FROM stranky_innodb ) AS childrens	        
        WHERE (
        parents.list=  {{root}} AND childrens.list IN ( {{in_menu_roots}}  )
        )
