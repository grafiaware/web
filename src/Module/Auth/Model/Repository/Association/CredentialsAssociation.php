<?php
namespace Auth\Model\Repository\Association;

use Model\Repository\Association\AssociationOneToOne;
use Model\Repository\Association\AssociationOneToOneInterface;
use Model\Entity\PersistableEntityInterface;

use Auth\Model\Dao\CredentialsDao;

/**
 * Description of RegistrationAssociation
 *
 * @author pes2704
 */
class CredentialsAssociation extends AssociationOneToOne implements AssociationOneToOneInterface {
    public function getReferenceName() {
        return CredentialsDao::REFERENCE_LOGIN;
    }

    public function extractChild(PersistableEntityInterface $parentEntity, &$childValue=null): void {
        /** @var LoginAggregateCredentialsInterface $parentEntity */
        $childValue = $parentEntity->getCredentials();
    }

    public function hydrateChild(PersistableEntityInterface $parentEntity, &$childValue): void {
        /** @var LoginAggregateCredentialsInterface $parentEntity */
        $parentEntity->setCredentials($childValue);
    }
}
