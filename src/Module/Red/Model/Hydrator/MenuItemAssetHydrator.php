<?php

namespace Red\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Red\Model\Entity\MenuItemAssetInterface;
use ArrayAccess;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class MenuItemAssetHydrator extends TypeHydratorAbstract implements HydratorInterface {
//        return ['menu_item_id_fk', 'asset_id_fk'];

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @param type $rowData
     */
    public function hydrate(EntityInterface $menuItemAsset, ArrayAccess $rowData) {
        /** @var MenuItemAssetInterface $menuItemAsset */
        $menuItemAsset
            ->setMenuItemIdFk($this->getPhpValue( $rowData,'menu_item_id_fk'))
            ->setAssetIdFk($this->getPhpValue( $rowData,'asset_id_fk'))
            ;
    }

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @param type $rowData
     */
    public function extract(EntityInterface $menuItemAsset, ArrayAccess $rowData) {
        /** @var MenuItemAssetInterface $menuItemAsset */
        $this->setSqlValue( $rowData, 'menu_item_id_fk', $menuItemAsset->getMenuItemIdFk() );
        $this->setSqlValue( $rowData, 'asset_id_fk', $menuItemAsset->getAssetIdFk() );

    }

}
