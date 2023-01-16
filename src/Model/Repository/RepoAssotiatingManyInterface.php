<?php
namespace Model\Repository;

use Model\Repository\Association\AssociationOneToManyInterface;

/**
 * Interface pro RODIČOVSKÉ repository s asociací 1:N
 *
 * @author pes2704
 */
interface RepoAssotiatingManyInterface extends RepoInterface {

    public function registerOneToManyAssotiation(AssociationOneToManyInterface $assotiation);

}
