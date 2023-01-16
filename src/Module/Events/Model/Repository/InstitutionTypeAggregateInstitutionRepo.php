<?php

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\InstitutionTypeAggregateInstitution;
use Events\Model\Entity\InstitutionTypeAggregateInstitutionInterface;
use Events\Model\Dao\InstitutionTypeDao;
use Events\Model\Hydrator\InstitutionTypeHydrator;

use \Model\Repository\RepoAssotiatingManyTrait;

/**
 * Description of InstitutionTypeAggregateInstitutionRepo
 *
 * @author vlse2610
 */
class InstitutionTypeAggregateInstitutionRepo extends RepoAbstract implements InstitutionTypeAggregateInstitutionRepoInterface {

    public function __construct( InstitutionTypeDao $institutionTypeDao, InstitutionTypeHydrator $institutionTypeHydrator) {
        $this->dataManager = $institutionTypeDao;
        $this->registerHydrator($institutionTypeHydrator);

    }

    use RepoAssotiatingManyTrait;

   /**
    *
    * @param type $id
    * @return InstitutionTypeAggregateInstitutionInterface|null
    */
    public function get($id): ?InstitutionTypeAggregateInstitutionInterface {
        return $this->getEntity($id);
    }

    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return InstitutionTypeAggregateInstitutionInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array {
        return $this->findEntities($whereClause, $touplesToBind);
    }


     /**
     *
     * @return InstitutionTypeAggregateInstitutionInterface[]
     */
    public function findAll() : array {
        return $this->findEntities();
    }


    /**
     *
     * @param InstitutionTypeAggregateInstitutionInterface $institutionType
     * @return void
     */
    public function add(InstitutionTypeAggregateInstitutionInterface $institutionType) :void {
        $this->addEntity($institutionType);
    }


    /**
     *
     * @param InstitutionTypeAggregateInstitutionInterface $institutionType
     * @return void
     */
    public function remove(InstitutionTypeAggregateInstitutionInterface $institutionType) : void {
        $this->removeEntity($institutionType);
    }

    ##################

    protected function createEntity() {
        return new InstitutionTypeAggregateInstitution();
    }

    #### protected ###########

    protected function indexFromEntity(InstitutionTypeAggregateInstitutionInterface $instTypeAggInst) {
        return $instTypeAggInst->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
