<?php

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Repository\InstitutionTypeRepo;
use Events\Model\Repository\InstitutionTypeRepoInterface;

use Events\Model\Entity\InstitutionTypeAggregateInstitution;
use Events\Model\Dao\InstitutionTypeDao;
use Events\Model\Hydrator\InstitutionTypeHydrator;
use Events\Model\Repository\InstitutionRepo;
use Events\Model\Hydrator\InstitutionTypeChildHydrator;
use Events\Model\Entity\InstitutionInterface;



/**
 * Description of InstitutionTypeAggregateInstitutionRepo
 *
 * @author vlse2610
 */
class InstitutionTypeAggregateInstitutionRepo extends InstitutionTypeRepo implements RepoInterface, InstitutionTypeRepoInterface {
    
     public function __construct( InstitutionTypeDao $institutionTypeDao, InstitutionTypeHydrator $institutionTypeHydrator,
                                      InstitutionRepo $institutionRepo, InstitutionTypeChildHydrator $institutionTypeChildHydrator) {
         
        parent::__construct($institutionTypeDao, $institutionTypeHydrator);
        $this->registerOneToManyAssociation(InstitutionInterface::class, 'id', $institutionRepo);
        $this->registerHydrator($institutionTypeChildHydrator);
    }

    
    protected function createEntity() {
        return new InstitutionTypeAggregateInstitution();
    }
    
    
}
