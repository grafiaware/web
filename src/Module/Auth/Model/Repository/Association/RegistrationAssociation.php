<?php
namespace Auth\Model\Repository\Association;

use Model\Repository\Association\AssociationOneToOne;
use Model\Repository\Association\AssociationOneToOneInterface;

use Auth\Model\Entity\LoginAggregateRegistrationInterface;
use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

/**
 * Description of RegistrationAssociation
 *
 * @author pes2704
 */
class RegistrationAssociation extends AssociationOneToOne implements AssociationOneToOneInterface {
    /**
     * Vyzvedne entitu z potomkovského repository getByReference() a pomocí child hydratoru hydratuje rodičovskou entitu vyzvednutou potomkonskou entitou.
     * Poznámka: Pokud potomkovská entita neexistuje hydratuje hodnotou null.
     *
     * @param PersistableEntityInterface $parentEntity
     * @param RowDataInterface $parentRowData
     * @return void
     */
    public function recreateEntity(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void {
        /** @var LoginAggregateRegistrationInterface $parentEntity */
        $parentEntity->setRegistration($this->getChild($parentRowData));
    }

    public function addEntity(PersistableEntityInterface $parentEntity): void {
        /** @var LoginAggregateRegistrationInterface $parentEntity */
        $this->addChild($parentEntity->getRegistration());
    }

    public function removeEntity(PersistableEntityInterface $parentEntity): void {
        /** @var LoginAggregateRegistrationInterface $parentEntity */
        $this->removeChild($parentEntity->getRegistration());
    }

}
