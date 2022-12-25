<?php
namespace Form;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowData;

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
        $row = new RowData();
        $this->hydrator->extract($this->entity, $row);
        return $row->yieldChanged()->getArrayCopy();
    }
}
