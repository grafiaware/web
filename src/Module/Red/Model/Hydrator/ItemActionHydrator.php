<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;

use Model\Entity\EntityInterface;
use Red\Model\Entity\ItemActionInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class ItemActionHydrator implements HydratorInterface {

    /**
     *
     * @param ItemActionInterface $itemAction
     * @param type $row
     */
    public function hydrate(EntityInterface $itemAction, &$row) {
        /** @var ItemActionInterface $itemAction */
        $itemAction
            ->setTypeFk($row['type_fk'])
            ->setItemId($row['item_id'])
            ->setEditorLoginName($row['editor_login_name'])
            ->setCreated($row['created'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['created']) : NULL);
    }

    /**
     *
     * @param ItemActionInterface $itemAction
     * @param type $row
     */
    public function extract(EntityInterface $itemAction, &$row) {
        /** @var ItemActionInterface $itemAction */
        $row['type_fk'] = $itemAction->getTypeFk(); 
        $row['item_id'] = $itemAction->getItemId();
        $row['editor_login_name'] = $itemAction->getEditorLoginName();
        // created je timestamp
    }

}
