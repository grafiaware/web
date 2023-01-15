<?php
namespace Events\Model\Entity;

use Events\Model\Entity\InstitutionTypeInterface;
use Events\Model\Entity\InstitutionInterface;

/**
 *
 * @author vlse2610
 */
interface InstitutionTypeAggregateInstitutionInterface extends InstitutionTypeInterface {

    /**
     *
     * @return InstitutionInterface[]
     */
    public function getInstitutions(): array;

    /**
     *
     * @param InstitutionInterface[] $institutions
     * @return InstitutionTypeAggregateInstitutionInterface
     */
    public function setInstitutions(array $institutions): InstitutionTypeAggregateInstitutionInterface;


}
