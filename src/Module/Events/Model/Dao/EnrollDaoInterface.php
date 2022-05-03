<?php
namespace Events\Model\Dao;
use Model\Dao\DaoReadonlyFkInterface;

/**
 *
 * @author vlse2610
 */
interface EnrollDaoInterface extends DaoReadonlyFkInterface {

    public function findByLoginNameFk(array $loginNameFk): array ;

    public function findByEventIdFk(array $eventContentIdFk): array ;
    
}