<?php
namespace Events\Model\Dao;
use Model\Dao\DaoReadonlyFkInterface;

/**
 *
 * @author vlse2610
 */
interface EnrollInterface extends DaoReadonlyFkInterface {
    
    public function findByLoginNameFk(array $loginNameFk );

    public function findByEventIdFk(array $eventContentIdFk );
}