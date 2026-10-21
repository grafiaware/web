<?php
namespace Auth\Model\Repository\Association;

use Pes\Model\Repository\Association\AssociationOneToOneAbstract;
use Pes\Model\Repository\Association\AssociationOneToOneInterface;
use Pes\Model\Entity\PersistableEntityInterface;

use Auth\Model\Dao\RegistrationDao;

use Auth\Model\Entity\LoginAggregateRegistrationInterface;

/**
 * Description of RegistrationAssociation
 *
 * @author pes2704
 */
class RegistrationAssociation extends AssociationOneToOneAbstract implements AssociationOneToOneInterface {

    public function getReferenceName() {
        return RegistrationDao::REFERENCE_LOGIN;
    }

    public function extractChild(PersistableEntityInterface $parentEntity, &$childValue=null): void {
        /** @var LoginAggregateRegistrationInterface $parentEntity */
        $childValue = $parentEntity->getRegistration();
    }

    public function hydrateChild(PersistableEntityInterface $parentEntity, &$childValue): void {
        /** @var LoginAggregateRegistrationInterface $parentEntity */
        $parentEntity->setRegistration($childValue);
    }
}
