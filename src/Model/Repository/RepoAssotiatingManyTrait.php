<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Model\Repository;

use Model\Repository\Association\AssociationOneToManyInterface;

use Model\Repository\RepoAssotiatingManyInterface;  // použito jen v komentáři

/**
 * Trait s implementací RepoAssotiatingManyInterface interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
class RepoAssotiatingManyTrait {

    public function registerOneToManyAssotiation(AssociationOneToManyInterface $assotiation, $referenceName = null) {
        $assotiation->setReferenceName($referenceName ?? $this->dataManager->getTableName());
        $this->associations[] = $assotiation;
    }

}
