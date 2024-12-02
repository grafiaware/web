<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Component\View\Data;

use Events\Component\View\Data\CompanyContactComponent;
use Component\View\ComponentPrototypeInterface;

/**
 * Description of CompanyComponentPrototype
 *
 * @author pes2704
 */
class CompanyContactComponentPrototype extends CompanyContactComponent implements ComponentPrototypeInterface {
    
    public function __clone() {
    }
}
