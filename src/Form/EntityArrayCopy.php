<?php
namespace Form;

use Pes\Model\Entity\EntityInterface;
use Pes\Model\Hydrator\HydratorInterface;
use ArrayObject;

use IteratorAggregate;

/**
 * Description of EntityIterator
 *
 * @author pes2704
 */
class EntityArrayCopy implements EntityArrayCopyInterface {

    private $entity;
    private $hydrator;

    public function __construct(EntityInterface $entity, HydratorInterface $hydrator) {
        $this->entity = $entity;
        $this->hydrator = $hydrator;
    }

    public function getArrayCopy(): array {
        $row = new ArrayObject();
        $this->hydrator->extract($this->entity, $row);
        return $row->getArrayCopy();
    }
}
