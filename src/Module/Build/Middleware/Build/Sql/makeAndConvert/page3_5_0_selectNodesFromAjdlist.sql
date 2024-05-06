/**
 * Author:  pes2704
 * Created: 28. 12. 2023
 */

SELECT child, parent FROM menu_adjlist
        ORDER BY level ASC,
        parent ASC,
        poradi ASC,
        child ASC   