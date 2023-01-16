<?php
namespace Events\Model\Repository\Association;

use Model\Repository\Association\AssociationOneToManyAbstract;
use Model\Repository\Association\AssociationOneToManyInterface;
use Model\Entity\PersistableEntityInterface;

use Events\Model\Dao\InstitutionDao;

use Events\Model\Entity\InstitutionTypeAggregateInstitutionInterface;   // pro komentáře

/**
 * Description of RegistrationAssociation
 *
 * @author pes2704
 */
class InstitutionsAssociation extends AssociationOneToManyAbstract implements AssociationOneToManyInterface {

    public function getReferenceName() {
        return InstitutionDao::REFERENCE_INSTITUTION_TYPE;
    }

    public function extractChild(PersistableEntityInterface $parentEntity, &$childValue=null): void {
        /** @var InstitutionTypeAggregateInstitutionInterface $parentEntity */
        $childValue = $parentEntity->getInstitutions();
    }

    public function hydrateChild(PersistableEntityInterface $parentEntity, &$childValue): void {
        /** @var InstitutionTypeAggregateInstitutionInterface $parentEntity */
        $parentEntity->setInstitutions($childValue);
    }
}
