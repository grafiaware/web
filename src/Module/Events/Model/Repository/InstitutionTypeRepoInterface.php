<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Entity\InstitutionTypeInterface;

/**
 *
 * @author vlse2610
 */
interface InstitutionTypeRepoInterface  extends RepoInterface {
   /**
    *
    * @param type $id
    * @return InstitutionTypeInterface|null
    */
    public function get($id): ?InstitutionTypeInterface;



    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return InstitutionTypeInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array ;


     /**
     *
     * @return InstitutionTypeInterface[]
     */
    public function findAll() : array  ;


    /**
     *
     * @param InstitutionTypeInterface $institutionType
     * @return void
     */
    public function add(InstitutionTypeInterface $institutionType) :void;


    /**
     *
     * @param InstitutionTypeInterface $institutionType
     * @return void
     */
    public function remove(InstitutionTypeInterface $institutionType) : void ;
}