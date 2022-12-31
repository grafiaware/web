<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Model\Repository;

use Model\Repository\Association\AssociationOneToManyInterface;

use Model\Repository\RepoAssotiatingManyInterface;  // použito je v komentáři

/**
 * Trait s implementací RepoAssotiatingManyInterface interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
class RepoAssotiatingManyTrait {

    public function registerOneToManyAssotiation($index, AssociationOneToManyInterface $assotiation) {
        $this->associations[$index] = $assotiation;
    }

}
