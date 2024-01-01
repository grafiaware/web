/**
 * Author:  pes2704
 * Created: 28. 12. 2023
 */

-- root
INSERT INTO menu_adjlist (child, parent, poradi, level)

		SELECT {{root}} AS child, NULL AS parent, 0 AS poradi, 1 AS level    