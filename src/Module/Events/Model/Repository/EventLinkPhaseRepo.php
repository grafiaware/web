<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Dao\EventLinkPhaseDao;
use Events\Model\Hydrator\EventLinkPhaseHydrator;
use Events\Model\Repository\EventLinkPhaseRepoInterface;
use Events\Model\Entity\EventLinkPhase;
use Events\Model\Entity\EventLinkPhaseInterface;



/**
 * Description of EventLinkPhaseRepo
 *
 * @author vlse2610
 */
class EventLinkPhaseRepo extends RepoAbstract implements EventLinkPhaseRepoInterface {

    public function __construct( EventLinkPhaseDao $eventLinkPhaseDao, EventLinkPhaseHydrator $eventLinkPhaseHydrator) {
        $this->dataManager = $eventLinkPhaseDao;
        $this->registerHydrator($eventLinkPhaseHydrator);
    }

//
    //
    //

    /**
    *
    * @param type $id
    * @return EventLinkPhaseInterface|null
    */
    public function get($id): ?EventLinkPhaseInterface {
        return $this->getEntity($id);
    }



    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return EventLinkPhaseInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array {
        return $this->findEntities($whereClause, $touplesToBind);
    }


     /**
     *
     * @return EventLinkPhaseInterface[]
     */
    public function findAll() : array  {
       return $this->findEntities();
    }


    /**
     *
     * @param EventLinkPhaseInterface $eventLinkPhase
     * @return void
     */
    public function add(EventLinkPhaseInterface $eventLinkPhase) :void {
        $this->addEntity($eventLinkPhase);
    }


    /**
     *
     * @param EventLinkPhaseInterface $eventLinkPhase
     * @return void
     */
    public function remove( EventLinkPhaseInterface $eventLinkPhase) : void {
        $this->removeEntity($eventLinkPhase);
    }




    protected function createEntity() {
        return new EventLinkPhase();
    }

    protected function indexFromEntity(EventLinkPhaseInterface $eventLinkPhase) {
        return $eventLinkPhase->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}

