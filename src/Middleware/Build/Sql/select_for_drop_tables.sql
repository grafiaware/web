/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  pes2704
 * Created: 19. 10. 2020
 */

SELECT concat('DROP TABLE IF EXISTS `', table_name, '`;')
FROM information_schema.tables
WHERE table_schema = 'MyDatabaseName';

SET FOREIGN_KEY_CHECKS = 0;
-- Your semicolon separated list of DROP statements here
SET FOREIGN_KEY_CHECKS = 1;