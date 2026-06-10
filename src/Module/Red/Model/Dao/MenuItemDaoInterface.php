<?php
namespace Red\Model\Dao;

use Pes\Model\Dao\DaoContextualInterface;
use Pes\Model\Dao\DaoReferenceNonuniqueInterface;
use Pes\Model\Dao\DaoReferenceUniqueInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemDaoInterface extends DaoContextualInterface, DaoReferenceUniqueInterface {
//        DaoReferenceNonuniqueInterface,

    public function getByList(array $langCodeFkAndList);

    public function findAllLanguageVersions(array $uidFk);



}
