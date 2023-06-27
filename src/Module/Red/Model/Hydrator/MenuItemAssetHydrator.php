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
//        return ['id', 'menu_item_id_FK', 'filepath', 'editor_login_name', 'created'];

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @param type $rowData
     */
    public function hydrate(EntityInterface $menuItemAsset, ArrayAccess $rowData) {
        /** @var MenuItemAssetInterface $menuItemAsset */
        $menuItemAsset
            ->setMenuItemIdFk( $this->getPhpValue( $rowData,'menu_item_id_FK') )
            ->setFilepath( $this->getPhpValue( $rowData,'filepath') )
            ->setMimeType($this->getPhpValue( $rowData,'mime_type'))
            ->setEditorLoginName( $this->getPhpValue( $rowData,'editor_login_name') )
            ->setCreated( $this->getPhpDate( $rowData,'created') )
            ->setUpdated( $this->getPhpDate( $rowData,'updated') )
            ;
    }

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @param type $rowData
     */
    public function extract(EntityInterface $menuItemAsset, ArrayAccess $rowData) {
        /** @var MenuItemAssetInterface $menuItemAsset */
        $this->setSqlValue( $rowData, 'menu_item_id_FK', $menuItemAsset->getMenuItemIdFk() );
        $this->setSqlValue( $rowData, 'filepath', $menuItemAsset->getFilepath() );
        $this->setSqlValue( $rowData, 'mime_type', $menuItemAsset->getMimeType() );
        $this->setSqlValue( $rowData, 'editor_login_name', $menuItemAsset->getEditorLoginName() );
        // created je DEFAULT CURRENT_TIMESTAMP
        // updated je ON UPDATE CURRENT_TIMESTAMP

    }

}
