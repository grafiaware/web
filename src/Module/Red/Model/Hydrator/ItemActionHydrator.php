<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;

use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

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
     * @param type $rowData
     */
    public function hydrate(EntityInterface $itemAction, RowDataInterface $rowData) {
        /** @var ItemActionInterface $itemAction */
        $itemAction
            ->setTypeFk($rowData->offsetGet('type_fk'))
            ->setContentId($rowData->offsetGet('content_id'))
            ->setEditorLoginName($rowData->offsetGet('editor_login_name'))
            ->setCreated($rowData->offsetGet('created') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('created')) : NULL);
    }

    /**
     *
     * @param ItemActionInterface $itemAction
     * @param type $rowData
     */
    public function extract(EntityInterface $itemAction, RowDataInterface $rowData) {
        /** @var ItemActionInterface $itemAction */
        $rowData->offsetSet('type_fk', $itemAction->getTypeFk());
        $rowData->offsetSet('content_id', $itemAction->getContentId());
        $rowData->offsetSet('editor_login_name', $itemAction->getEditorLoginName());
        // created je timestamp
    }

}
