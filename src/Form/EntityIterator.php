<?php
namespace Form;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use ArrayObject;

use Traversable;

/**
 * Description of EntityIterator
 *
 * @author pes2704
 */
class EntityIterator implements EntityIteratorInterface {

    private $entity;
    private $hydrator;

    public function __construct(EntityInterface $entity, HydratorInterface $hydrator) {
        $this->entity = $entity;
        $this->hydrator = $hydrator;
    }

    public function getIterator(): Traversable {
        $row = new ArrayObject();
        $this->hydrator->extract($this->entity, $row);
        return $row->getIterator();
    }
}
