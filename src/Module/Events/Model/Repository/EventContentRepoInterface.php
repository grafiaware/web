<?php
namespace Events\Model\Repository;


use Model\Repository\RepoInterface;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\EventContent;
use Events\Model\Entity\EventContentInterface;
use Events\Model\Dao\EventContentDao;
use Events\Model\Repository\EventContentRepoInterface;

/**
 *
 * @author vlse2610
 */
interface EventContentRepoInterface extends RepoInterface  {
   
    /**
     * 
     * @param  $type
     * @return EventContentInterface|null
     */
    public function get( $type): ?EventContentInterface ;

    /**
     * 
     * @return EventContentInterface[]
     */
    public function findAll() :array ;
       
    /**
     * 
     * @param EventContentInterface $eventContent
     * @return void
     */
    public function add(EventContentInterface $eventContent) :void ; 
    
    /**
     * 
     * @param EventContentInterface $eventContent
     * @return void
     */
    public function remove(EventContentInterface $eventContent) :void ;
}
