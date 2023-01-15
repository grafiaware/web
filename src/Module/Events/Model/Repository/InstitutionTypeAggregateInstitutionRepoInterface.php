<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Model\Repository\RepoAssotiatingManyInterface;

use Events\Model\Entity\InstitutionTypeAggregateInstitutionInterface;

/**
 *
 * @author vlse2610
 */
interface InstitutionTypeAggregateInstitutionRepoInterface  extends RepoAssotiatingManyInterface {
   /**
    *
    * @param type $id
    * @return InstitutionTypeAggregateInstitutionInterface|null
    */
    public function get($id): ?InstitutionTypeAggregateInstitutionInterface;



    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return InstitutionTypeAggregateInstitutionInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array ;


     /**
     *
     * @return InstitutionTypeAggregateInstitutionInterface[]
     */
    public function findAll() : array  ;


    /**
     *
     * @param InstitutionTypeAggregateInstitutionInterface $institutionType
     * @return void
     */
    public function add(InstitutionTypeAggregateInstitutionInterface $institutionType) :void;


    /**
     *
     * @param InstitutionTypeAggregateInstitutionInterface $institutionType
     * @return void
     */
    public function remove(InstitutionTypeAggregateInstitutionInterface $institutionType) : void ;
}