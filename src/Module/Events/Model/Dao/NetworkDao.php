<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;

/**
 * Description of JobTagDao
 *
 * @author vlse2610
 */
class NetworkDao extends DaoEditAbstract  implements DaoEditAutoincrementKeyInterface  {   

    use DaoAutoincrementTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }
    
    public function getPrimaryKeyAttributes(): array {       
        return ['id'];
    }

    public function getAttributes(): array {
        return [ 'id', 'icon', 'title', 'embed_code_template' ];
    }

    public function getTableName(): string {
        return 'network';
    }
}
