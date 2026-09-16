<?php
namespace Auth\Model\Repository\Association;

use Pes\Model\Repository\Association\AssociationOneToOneAbstract;
use Pes\Model\Repository\Association\AssociationOneToOneInterface;
use Pes\Model\Entity\PersistableEntityInterface;

use Auth\Model\Dao\CredentialsDao;

/**
 * Description of RegistrationAssociation
 *
 * @author pes2704
 */
class CredentialsAssociation extends AssociationOneToOneAbstract implements AssociationOneToOneInterface {
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
