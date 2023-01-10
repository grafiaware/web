<?php
namespace Model\Repository;

use Model\Repository\Association\AssociationOneToManyInterface;

use Model\Repository\RepoAssotiatingManyInterface;  // použito jen v komentáři

/**
 * Trait s implementací RepoAssotiatingManyInterface interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
trait RepoAssotiatingManyTrait {

    public function registerOneToManyAssotiation(AssociationOneToManyInterface $assotiation) {
        $this->associations[] = $assotiation;
    }

}
