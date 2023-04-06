<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;

//use Model\Dao\DaoEditKeyDbVerifiedInterface;
//use Model\Dao\JobTagDaoInterface;
use Model\Dao\DaoEditAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;

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
       //return ['tag'];
        return ['id'];
    }

    public function getAttributes(): array {
        return [ 'id', 'tag'  ];
    }

    public function getTableName(): string {
        return 'job_tag';
    }
}
