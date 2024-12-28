<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Component\View;
use Component\ViewModel\ViewModelListInterface;

/**
 *
 * @author pes2704
 */
interface ComponentFamilyInterface {
    public function createFamilyRouteSegment(string $prefix, string $parentName, string $parentId, string $childName);

}
