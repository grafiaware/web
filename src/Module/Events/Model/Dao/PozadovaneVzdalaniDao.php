<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditKeyDbVerifiedInterface;

/**
 * Description of PozadovaneVzdalaniDao
 *
 * @author vlse2610
 */
class PozadovaneVzdalaniDao  extends DaoEditAbstract  implements DaoEditKeyDbVerifiedInterface {

    public function getPrimaryKeyAttributes(): array {
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