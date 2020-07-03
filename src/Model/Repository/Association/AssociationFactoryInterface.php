<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Entity\EntityInterface;
use Model\Repository\RepoInterface;
/**
 *
 * @author pes2704
 */
interface AssociationFactoryInterface {
    public function createAssociated(&$row);
    public function getChildRepo(): RepoInterface;
    public function getParentPropertyName();
    public function getParentIdName();
}
