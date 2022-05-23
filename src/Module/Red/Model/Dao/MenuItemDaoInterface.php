<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Red\Model\Dao;

use Model\Dao\DaoFkNonuniqueInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemDaoInterface extends DaoFkNonuniqueInterface {
    public function getById(array $id);
    public function getByPrettyUri(array $prettyUri);
    public function getByList(array $langCodeFkAndList);

    public function findAllLanguageVersions(array $uidFk);



}
