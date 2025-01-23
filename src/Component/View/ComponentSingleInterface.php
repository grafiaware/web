<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Component\View;

use Component\ViewModel\RouteSegment\SingleRouteSegmentInterface;

/**
 *
 * @author pes2704
 */
interface ComponentSingleInterface {
    public function createSingleRouteSegment(string $prefix, string $parentName): SingleRouteSegmentInterface;

}
