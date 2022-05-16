<?php

namespace Events\Model\Dao;

use Model\Dao\DaoContextualInterface;
use Model\Dao\DaoEditAutoincrementKeyInterface;

/**
 *
 * @author vlse2610
 */
interface EventDaoInterface extends DaoContextualInterface, DaoEditAutoincrementKeyInterface {

    /**
     *
     * @param int $eventContentIdFk
     * @return array
     */
    public function findByEventContentIdFk(array $eventContentIdFk ) ;

}
