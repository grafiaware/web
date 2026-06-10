<?php
namespace Events\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Dao\DaoAutoincrementTrait;

/**
 * Description of JobTagDao
 *
 * @author vlse2610
 */
class JobTagDao extends DaoEditAbstract  implements DaoEditAutoincrementKeyInterface  {   

    use DaoAutoincrementTrait;
    
    

    public function getAutoincrementFieldName() {
        return 'id';
    }
    
    public function getPrimaryKeyAttributes(): array {       
        return ['id'];
    }

    public function getAttributes(): array {
        return [ 'id', 'tag', 'color' ];
    }

    public function getTableName(): string {
        return 'job_tag';
    }
}
