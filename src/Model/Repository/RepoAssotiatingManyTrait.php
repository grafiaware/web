<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Model\Repository;

use Model\Repository\Association\AssociationOneToManyInterface;

/**
 * Description of RepoAssotiatingAbstract
 *
 * @author pes2704
 */
class RepoAssotiatingManyTrait {

    public function registerOneToManyAssotiation($index, AssociationOneToManyInterface $assotiation) {
        $this->associations[$index] = $assotiation;
    }

}
