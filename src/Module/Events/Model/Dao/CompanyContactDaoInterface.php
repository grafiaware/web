<?php
namespace Events\Model\Dao;

use Model\Dao\DaoAutoincrementKeyInterface;

/**
 *
 * @author vlse2610
 */
interface CompanyContactDaoInterface  extends DaoAutoincrementKeyInterface {

    public function get($id) ;
    

    
    //public function find($whereClause="", $touplesToBind=[]) ;
    

    
}
