<?php
namespace Red\Model\Dao;

use Model\Dao\DaoReferenceNonuniqueInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemDaoInterface extends DaoReferenceNonuniqueInterface {
    public function getById(array $id);
    public function getByPrettyUri(array $prettyUri);
    public function getByList(array $langCodeFkAndList);

    public function findAllLanguageVersions(array $uidFk);



}
