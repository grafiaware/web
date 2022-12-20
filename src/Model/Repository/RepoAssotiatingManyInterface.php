<?php
namespace Model\Repository;

use Model\Repository\Association\AssociationOneToManyInterface;

/**
 *
 * @author pes2704
 */
interface RepoAssotiatingManyInterface {

    public function registerOneToManyAssotiation($index, AssociationOneToManyInterface $assotiation);

}
