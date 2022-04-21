<?php
namespace Events\Model\Dao;

use Model\Dao\DaoAutoincrementKeyInterface;


/**
 *
 * @author vlse2610
 */
interface EventLinkDaoInterface extends DaoAutoincrementKeyInterface {
    /**
     * Vrací jednu řádku tabulky 'event_link' ve formě asociativního pole podle primárního klíče.
     *
     * @param int $id Hodnota primárního klíče
     * @return array Asociativní pole
     */   
    public function get($id) ;
        
    //public function getOutOfContext(...$id) ;        
                   
    public function find($whereClause="", $touplesToBind=[]) ;   
      
}
