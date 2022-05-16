<?php
namespace Model\Dao;

/**
 *
 * @author pes2704
 */
interface DaoFkCommonInterface extends DaoInterface {

    public function getForeignKeyAttributes(): array;

    public function getForeignKeyTouples($fkAttributesName, array $row): array;

}
