<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\EventLink;
use Events\Model\Entity\EventLinkInterface;
use Events\Model\Dao\EventLinkDao;
use Events\Model\Hydrator\EventLinkHydrator;
use Events\Model\Repository\EventLinkRepoInterface;


/**
 * Description of EventLinkRepo
 *
 * @author vlse2610
 */
class EventLinkRepo  extends RepoAbstract implements EventLinkRepoInterface {

    public function __construct(EventLinkDao $eventLinkDao, EventLinkHydrator $eventLinkHydrator) {
        $this->dataManager = $eventLinkDao;
        $this->registerHydrator($eventLinkHydrator);
    }

    /**
     *
     * @param type $id
     * @return EventLinkInterface|null
     */
    public function get($id): ?EventLinkInterface {
        return $this->getEntity($id);
    }

    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return EventLinkInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     *
     * @return EventLinkInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }

    /**
     *
     * @param EventLinkInterface $eventLink
     * @return void
     */
    public function add(EventLinkInterface $eventLink) : void {
        $this->addEntity($eventLink);
    }
    /**
     *
     * @param EventLinkInterface $eventLink
     * @return void
     */
    public function remove(EventLinkInterface $eventLink)  :void {
        $this->removeEntity($eventLink);
    }




    protected function createEntity() : EventLinkInterface {
        return new EventLink();
    }

    protected function indexFromEntity(EventLinkInterface $eventLink) {
        return $eventLink->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
