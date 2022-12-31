<?php
namespace Model\Repository;

use Model\Repository\Association\AssociationOneToOneInterface;

use Model\Repository\RepoAssotiatingOneInterface;  // použito je v komentáři

/**
 * Trait s implementací RepoAssotiatingOneInterface interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
trait RepoAssotiatingOneTrait {

    public function registerOneToOneAssociation($index, AssociationOneToOneInterface $assotiation) {
        $this->associations[$index] = $assotiation;
    }
}
