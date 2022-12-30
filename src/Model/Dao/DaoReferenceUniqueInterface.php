<?php
namespace Model\Dao;

/**
 *
 * @author pes2704
 */
interface DaoReferenceUniqueInterface extends DaoReferenceCommonInterface {

    public function getByReference($referenceName, array $referenceTouples);

}
