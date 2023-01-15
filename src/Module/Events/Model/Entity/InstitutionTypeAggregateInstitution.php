<?php
namespace Events\Model\Entity;

use Events\Model\Entity\Institution;
use Events\Model\Entity\InstitutionType;
use Events\Model\Entity\InstitutionTypeAggregateInstitutionInterface;

/**
 * Description of InstitutionTypeAggregateInstitution
 *
 * @author vlse2610
 */
class InstitutionTypeAggregateInstitution extends InstitutionType implements InstitutionTypeAggregateInstitutionInterface {

    /**
     *
     * @return InstitutionInterface[]
     */
    public function getInstitutions(): array {

        return $this->institutions;
    }

    /**
     *
     * @param  $institutions[]
     * @return InstitutionTypeAggregateInstitutionInterface
     */
    public function setInstitutions(array $institutions=[]): InstitutionTypeAggregateInstitutionInterface {
        $this->institutions = $institutions;
        return $this;
     }

}
