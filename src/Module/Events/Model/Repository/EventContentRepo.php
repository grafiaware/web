<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\RowHydratorInterface;

use Events\Model\Entity\EventContent;
use Events\Model\Entity\EventContentInterface;
use Events\Model\Repository\EventContentRepoInterface;

use Events\Model\Dao\EventContentDao;

/**
 * Description of EventContentRepo
 *
 * @author pes2704
 */
class EventContentRepo extends RepoAbstract implements EventContentRepoInterface {

    protected $dao;

    public function __construct(EventContentDao $eventContentDao, RowHydratorInterface $eventContentHydrator) {
        $this->dataManager = $eventContentDao;
        $this->registerHydrator($eventContentHydrator);
    }

    /**
     *
     * @param string $id
     * @return EventContentInterface|null
     */
    public function get($id): ?EventContentInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['id'=>$id]);
        return $this->getEntity($key);
    }

    /**
     * 
     * @return EventContentInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     * 
     * @return EventContentInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }

    /**
     * 
     * @param EventContentInterface $eventContent
     * @return void
     */
    public function add(EventContentInterface $eventContent) :void  {
        $this->addEntity($eventContent);
    }
    
     
    /**
     * 
     * @param EventContentInterface $eventContent
     * @return void
     */
    public function remove(EventContentInterface $eventContent) :void {
        $this->removeEntity($eventContent);
    }

    
    
    /**
     * 
     * @param EventContentInterface $eventContent
     * @return void
     */
    protected function createEntity() : EventContentInterface {
        return new EventContent();
    }

    
    protected function indexFromEntity(EventContentInterface $eventLink) {
        return $eventLink->getId();
    }

    
    protected function indexFromRow($row) {
        return $row['id'];
    }
}
