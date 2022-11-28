<?php

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditAutoincrementKeyInterface;
use Model\Dao\DaoFkNonuniqueInterface;
use Model\Dao\DaoAutoincrementTrait;
use Model\Dao\DaoFkNonuniqueTrait;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class MenuItemAssetDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface, DaoFkNonuniqueInterface {

    use DaoAutoincrementTrait;
    use DaoFkNonuniqueTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'menu_item_id_FK', 'filepath', 'editor_login_name', 'created'];
    }

    public function getForeignKeyAttributes(): array {
        return [
            'menu_item_id_FK'=>['menu_item_id_FK']
        ];
    }
    public function getTableName(): string {
        return 'menu_item_asset';
    }

    /**
     * Vrací řádek menu_item_assets vyhledaný podle filename, filename je unikátní (je definován unique index).
     *
     * @param array $filename
     * @return type
     */
    public function getByFilename(array $filename) {
        return $this->getUnique($filename);
    }

    public function findByMenuItemIdFk(array $menuItemIdFk) {
        return $this->findByFk('menu_item_id_FK', $menuItemIdFk);
    }
}
