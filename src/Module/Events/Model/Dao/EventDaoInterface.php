<?php

namespace Events\Model\Dao;

use Model\Dao\DaoAutoincrementKeyInterface;

/**
 *
 * @author vlse2610
 */
interface EventDaoInterface extends DaoAutoincrementKeyInterface {
    
    

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param int $id Hodnota primárního klíče
     * @return array Asociativní pole
     */   
    public function get(  $id) ;
        
    public function getOutOfContext(...$id) ;        
        
    /**
     * 
     * @param int $eventContentIdFk
     * @return array
     */
    public function getByEventContentIdFk( int $eventContentIdFk ) ;
           
    public function find($whereClause="", $touplesToBind=[]) ;   
      
}
