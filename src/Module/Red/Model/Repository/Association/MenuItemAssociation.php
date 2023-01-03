<?php
namespace Red\Model\Repository\Association;

use Model\Repository\Association\AssociationOneToOne;
use Model\Repository\Association\AssociationOneToOneInterface;

use Red\Model\Entity\HierarchyAggregateInterface;
use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

/**
 * Description of RegistrationAssociation
 *
 * @author pes2704
 */
class MenuItemAssociation extends AssociationOneToOne implements AssociationOneToOneInterface {
    /**
     * Vyzvedne entitu z potomkovského repository getByReference() a pomocí child hydratoru hydratuje rodičovskou entitu vyzvednutou potomkonskou entitou.
     * Poznámka: Pokud potomkovská entita neexistuje hydratuje hodnotou null.
     *
     * @param PersistableEntityInterface $parentEntity
     * @param RowDataInterface $parentRowData
     * @return void
     */
    public function recreateEntity(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void {
        /** @var HierarchyAggregateInterface $parentEntity */
        $parentEntity->setMenuItem($this->getChild($parentRowData));
    }

    public function addEntity(PersistableEntityInterface $parentEntity): void {
        /** @var HierarchyAggregateInterface $parentEntity */
        $this->addChild($parentEntity->getMenuItem());
    }

    public function removeEntity(PersistableEntityInterface $parentEntity): void {
        /** @var HierarchyAggregateInterface $parentEntity */
        $this->removeChild($parentEntity->getMenuItem());
    }

}
