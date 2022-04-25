<?php

namespace Events\Model\Dao;

use Model\Dao\DaoContextualInterface;
use Model\Dao\DaoAutoincrementKeyInterface;

/**
 *
 * @author vlse2610
 */
interface EventDaoInterface extends DaoContextualInterface, DaoAutoincrementKeyInterface {

    /**
     *
     * @param int $eventContentIdFk
     * @return array
     */
    public function findByEventContentIdFk(array $eventContentIdFk ) ;

}
