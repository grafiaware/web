<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\EventContentType;
use Events\Model\Entity\EventContentTypeInterface;
use Events\Model\Dao\EventContentTypeDao;
use Events\Model\Repository\EventContentTypeRepoInterface;

/**
 * Description of EventTypeTypeRepo
 *
 * @author pes2704
 */
class EventContentTypeRepo extends RepoAbstract implements EventContentTypeRepoInterface {

    protected $dao;

    public function __construct(EventContentTypeDao $eventContentTypeDao, HydratorInterface $eventContentTypeHydrator) {
        $this->dataManager = $eventContentTypeDao;
        $this->registerHydrator($eventContentTypeHydrator);
    }

    
    /**
     *
     * @param type $id
     * @return EventContentTypeInterface|null
     */
    public function get($id): ?EventContentTypeInterface {
        return $this->getEntity($id); 
    }
    
    /**
     *
     * @param type $type
     * @return EventContentTypeInterface|null
     */
    public function getByType($type): ?EventContentTypeInterface {
        return $this->getEntityByUnique(['type'=>$type]);
    }
    
    /**
     *
     * @return EventContentTypeInterface[]
     */
    public function findAll() :array  {
        return $this->findEntities();
    }
    
    /**
     *
     * @param EventContentTypeInterface $eventContentType
     * @return void
     */
    public function add(EventContentTypeInterface $eventContentType) :void {
        $this->addEntity($eventContentType);
    }
    
    /**
     *
     * @param EventContentTypeInterface $eventContentType
     * @return void
     */
    public function remove(EventContentTypeInterface $eventContentType) :void {
        $this->removeEntity($eventContentType);
    }

    protected function createEntity() {
        return new EventContentType();
    }

    protected function indexFromEntity(EventContentTypeInterface $eventContentType) {
        return $eventContentType->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
