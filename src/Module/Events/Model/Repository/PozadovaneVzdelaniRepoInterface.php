<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\PozadovaneVzdelaniInterface;

/**
 *
 * @author vlse2610
 */
interface PozadovaneVzdelaniRepoInterface extends RepoInterface  {
   
   
    /**
     * 
     * @param type $stupen
     * @return PozadovaneVzdelaniInterface|null
     */
    public function get( $stupen): ?PozadovaneVzdelaniInterface ;

    /**
     * 
     * @return PozadovaneVzdelaniInterface[]
     */
    public function findAll() :array ;
       
    
    /**
     * 
     * @param PozadovaneVzdelaniInterface $pozadovaneVzdelani
     * @return void
     */
    public function add(PozadovaneVzdelaniInterface $pozadovaneVzdelani ) :void ; 
    
    /**
     * 
     * @param PozadovaneVzdelaniInterface $pozadovaneVzdelani
     * @return void
     */
    public function remove(PozadovaneVzdelaniInterface $pozadovaneVzdelani) :void ;
}
