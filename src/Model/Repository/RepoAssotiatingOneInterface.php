<?php
namespace Model\Repository;

use Model\Repository\Association\AssociationOneToOneInterface;

/**
 * Interface pro RODIČOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
interface RepoAssotiatingOneInterface {

    public function registerOneToOneAssociation(AssociationOneToOneInterface $assotiation);

}
