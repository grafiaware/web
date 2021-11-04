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

use Red\Model\Entity\MultipageInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class MultipageHydrator implements HydratorInterface {

    /**
     *
     * @param MultipageInterface $multipage
     * @param type $rowData
     */
    public function hydrate(EntityInterface $multipage, RowDataInterface $rowData) {
        /** @var MultipageInterface $multipage */
        $multipage
            ->setId($rowData->offsetGet('id'))
            ->setMenuItemIdFk($rowData->offsetGet('menu_item_id_fk'))
            ->setTemplate($rowData->offsetGet('template'))
            ->setEditor($rowData->offsetGet('editor'))
            ->setUpdated($rowData->offsetGet('updated') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('updated')) : NULL);
    }

    /**
     *
     * @param MultipageInterface $article
     * @param type $rowData
     */
    public function extract(EntityInterface $article, RowDataInterface $rowData) {
        /** @var MultipageInterface $article */
        $rowData->offsetSet('id', $article->getId()); // id je autoincrement - readonly, hodnota pro where
        $rowData->offsetSet('menu_item_id_fk', $article->getMenuItemIdFk());
        $rowData->offsetSet('template', $article->getTemplate());
        $rowData->offsetSet('editor', $article->getEditor());
        // updated je timestamp
        // id je autoincrement - readonly
    }

}
