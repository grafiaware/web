<?php
namespace Events\Model\Dao;
use Pes\Model\Dao\DaoReferenceNonuniqueInterface;

/**
 *
 * @author vlse2610
 */
interface EnrollDaoInterface extends DaoReferenceNonuniqueInterface {

    public function findByLoginNameFk(array $loginNameFk): array ;

    public function findByEventIdFk(array $eventContentIdFk): array ;
    
}