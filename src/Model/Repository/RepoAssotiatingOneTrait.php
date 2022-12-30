<?php
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
