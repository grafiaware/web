<?php
namespace Red\Model\Dao;

use Model\Dao\DaoContextualInterface;
use Model\Dao\DaoReferenceNonuniqueInterface;
use Model\Dao\DaoReferenceUniqueInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemDaoInterface extends DaoContextualInterface, DaoReferenceUniqueInterface {
//        DaoReferenceNonuniqueInterface,

    public function getByList(array $langCodeFkAndList);

    public function findAllLanguageVersions(array $uidFk);



}
