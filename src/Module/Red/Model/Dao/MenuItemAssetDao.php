<?php

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditAutoincrementKeyInterface;
use Model\Dao\DaoReferenceNonuniqueInterface;
use Model\Dao\DaoAutoincrementTrait;
use Model\Dao\DaoReferenceNonuniqueTrait;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class MenuItemAssetDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface, DaoReferenceNonuniqueInterface {

    const REFERENCE_MENU_ITEM = 'menu_item';

    use DaoAutoincrementTrait;
    use DaoReferenceNonuniqueTrait;
    
    public function getAutoincrementFieldName() {
        return 'id';
    }

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'menu_item_id_FK', 'filepath', 'mime_type', 'editor_login_name', 'created', 'updated'];
    }

    public function getReferenceAttributes($referenceName): array {
        return [
        self::REFERENCE_MENU_ITEM=>['menu_item_id_FK'=>'id']
        ][$referenceName];
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
