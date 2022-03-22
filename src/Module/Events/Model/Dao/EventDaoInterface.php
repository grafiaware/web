<?php

namespace Events\Model\Dao;

use Model\Dao\DaoAutoincrementKeyInterface;

/**
 *
 * @author vlse2610
 */
interface EventDaoInterface extends DaoAutoincrementKeyInterface {
    
    public function getByEventContentIdFk( $eventContentIdFk )  ;
    
    
}
