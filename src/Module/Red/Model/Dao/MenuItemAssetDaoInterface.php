<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Red\Model\Dao;

use Model\Dao\DaoReferenceUniqueInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemAssetDaoInterface extends DaoReferenceUniqueInterface {
    public function findByMenuItemIdFk(array $menuItemIdFk);
    public function findByAssetIdFk(array $assetIdFk);    
}
