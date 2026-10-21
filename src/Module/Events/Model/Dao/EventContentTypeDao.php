<?php
namespace Events\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Dao\DaoAutoincrementTrait;




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
