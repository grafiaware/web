<?php

namespace Red\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Red\Model\Entity\MenuItemAssetInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class MenuItemAssetHydrator extends TypeHydratorAbstract implements HydratorInterface {
//        return ['id', 'menu_item_id_FK', 'filepath', 'editor_login_name', 'created'];

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @param type $rowData
     */
    public function hydrate(EntityInterface $menuItemAsset, RowDataInterface $rowData) {
        /** @var MenuItemAssetInterface $menuItemAsset */
        $menuItemAsset
            ->setId( $this->getPhpValue( $rowData,'id') )
            ->setMenuItemIdFk( $this->getPhpValue( $rowData,'menu_item_id_FK') )
            ->setFilepath( $this->getPhpValue( $rowData,'filepath') )
            ->setEditorLoginName( $this->getPhpValue( $rowData,'editor_login_name') )
            ->setCreated( $this->getPhpValue( $rowData,'created') )
            ;
    }

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @param type $rowData
     */
    public function extract(EntityInterface $menuItemAsset, RowDataInterface $rowData) {
        /** @var MenuItemAssetInterface $menuItemAsset */
        // id je autoincrement
        $this->setSqlValue( $rowData, 'menu_item_id_FK', $menuItemAsset->getMenuItemIdFk() );
        $this->setSqlValue( $rowData, 'filepath', $menuItemAsset->getFilepath() );
        $this->setSqlValue( $rowData, 'editor_login_name', $menuItemAsset->getEditorLoginName() );
        // created je DEFAULT ON INSERT TIMESTAMP

    }

}
