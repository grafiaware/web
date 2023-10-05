<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditAutoincrementKeyInterface;



/**
 * Description of EventContentTypeDao
 *
 * @author pes2704
 */
class EventContentTypeDao extends DaoEditAbstract  implements DaoEditAutoincrementKeyInterface  {   

    use DaoAutoincrementTrait;    
//extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {
    
    
    public function getAutoincrementFieldName() {
        return 'id';
    }
        
    public function getPrimaryKeyAttributes(): array {       
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'type', 'name'];
    }

       
    public function getTableName(): string {
        return 'event_content_type';
    }
}
