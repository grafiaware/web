<?php
namespace Model\Dao;

/**
 *
 * @author pes2704
 */
interface DaoReferenceCommonInterface extends DaoInterface {

    public function getReference($parentTableName): array;

    public function getReferenceKeyTouples($parentTableName, array $referenceParams): array;

}
