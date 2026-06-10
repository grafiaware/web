<?php

namespace Events\Model\Dao;

use Pes\Model\Dao\DaoContextualInterface;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;

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
