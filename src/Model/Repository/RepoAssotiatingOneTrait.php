<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Model\Repository;

use Model\Repository\Association\AssociationOneToOneInterface;

/**
 * Description of RepoAssotiatingAbstract
 *
 * @author pes2704
 */
trait RepoAssotiatingOneTrait  {

    public function registerOneToOneAssociation($index, AssociationOneToOneInterface $assotiation) {
        $this->associations[$index] = $assotiation;
    }
}
