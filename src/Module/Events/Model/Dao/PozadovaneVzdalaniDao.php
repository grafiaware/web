<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;

/**
 * Description of JobToTagDao
 *
 * @author vlse2610
 */
class PozadovaneVzdalaniDao  extends DaoEditAbstract  implements DaoKeyDbVerifiedInterface {

    public function getPrimaryKeyAttribute(): array {
        return ['stupen'];
    }



    public function getAttributes(): array {
        return [
            'stupen',
            'vzdelani'
        ];
    }

    public function getTableName(): string {
        return 'pozadovane_vzdelani';
    }
}