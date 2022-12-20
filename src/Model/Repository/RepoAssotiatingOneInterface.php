<?php
namespace Model\Repository;

use Model\Repository\Association\AssociationOneToOneInterface;

/**
 *
 * @author pes2704
 */
interface RepoAssotiatingOneInterface {

    public function registerOneToOneAssociation($index, AssociationOneToOneInterface $assotiation);

}
