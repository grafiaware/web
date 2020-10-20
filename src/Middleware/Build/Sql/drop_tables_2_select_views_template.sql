/**
 * Author:  pes2704
 * Created: 19. 10. 2020
 */


SELECT table_name
FROM information_schema.views
WHERE table_schema = '{{database}}';