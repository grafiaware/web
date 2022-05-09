<?php
namespace Events\Model\Dao;
use Model\Dao\DaoFkNonuniqueInterface;

/**
 *
 * @author vlse2610
 */
interface EnrollDaoInterface extends DaoFkNonuniqueInterface {

    public function findByLoginNameFk(array $loginNameFk): array ;

    public function findByEventIdFk(array $eventContentIdFk): array ;
    
}