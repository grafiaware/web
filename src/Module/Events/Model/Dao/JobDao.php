<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;


/**
 * Description of JobDao
 *
 * @author vlse2610
 */
class JobDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            `id`,
            `company_id`,
            `pozadovane_vzdelani_stupen` ,
            `nazev`,
            `misto_vykonu`,
            `popis_pozice`,
            `pozadujeme`,
            `nabizime`
        ];
    }

    public function getTableName(): string {
        return 'job';
    }
}
