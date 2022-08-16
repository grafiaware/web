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
     * @param  $stupen
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
     * @param PozadovaneVzdelaniInterface $stupen
     * @return void
     */
    public function add(PozadovaneVzdelaniInterface $stupen ) :void ; 
    
    /**
     * 
     * @param PozadovaneVzdelaniInterface $eventContentType
     * @return void
     */
    public function remove(PozadovaneVzdelaniInterface $stupen) :void ;
}
