<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditKeyDbVerifiedInterface;

use Events\Model\Dao\JobTagDaoInterface;


/**
 * Description of JobTagDao
 *
 * @author vlse2610
 */
class JobTagDao    extends DaoEditAbstract  implements JobTagDaoInterface /*DaoEditKeyDbVerifiedInterface */{   
    
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
