<?php
namespace Auth\Model\Repository\Association;

use Model\Repository\Association\AssociationOneToOne;
use Model\Repository\Association\AssociationOneToOneInterface;

use Auth\Model\Entity\LoginAggregateCredentialsInterface;
use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

/**
 * Description of RegistrationAssociation
 *
 * @author pes2704
 */
class CredentialsAssociation extends AssociationOneToOne implements AssociationOneToOneInterface {
    /**
     * Vyzvedne entitu z potomkovského repository getByReference() a pomocí child hydratoru hydratuje rodičovskou entitu vyzvednutou potomkonskou entitou.
     * Poznámka: Pokud potomkovská entita neexistuje hydratuje hodnotou null.
     *
     * @param LoginAggregateCredentialsInterface $parentEntity
     * @param RowDataInterface $parentRowData
     * @return void
     */
    public function recreateEntity(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void {
        /** @var LoginAggregateCredentialsInterface $parentEntity */
        $parentEntity->setCredentials($this->getChild($parentRowData));
    }

    public function addEntity(PersistableEntityInterface $parentEntity): void {
        /** @var LoginAggregateCredentialsInterface $parentEntity */
        $this->addChild($parentEntity->getCredentials());
    }

    public function removeEntity(PersistableEntityInterface $parentEntity): void {
        /** @var LoginAggregateCredentialsInterface $parentEntity */
        $this->removeChild($parentEntity->getCredentials());
    }

}
