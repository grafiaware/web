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

use Red\Model\Entity\ArticleInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class ArticleHydrator implements HydratorInterface {

    /**
     *
     * @param ArticleInterface $article
     * @param type $rowData
     */
    public function hydrate(EntityInterface $article, RowDataInterface $rowData) {
        /** @var ArticleInterface $article */
        $article
            ->setId($rowData->offsetGet('id'))
            ->setMenuItemIdFk($rowData->offsetGet('menu_item_id_fk'))
            ->setContent($rowData->offsetGet('article'))
            ->setTemplate($rowData->offsetGet('template'))
            ->setEditor($rowData->offsetGet('editor'))
            ->setUpdated($rowData->offsetGet('updated') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('updated')) : NULL);
    }

    /**
     *
     * @param ArticleInterface $article
     * @param type $rowData
     */
    public function extract(EntityInterface $article, RowDataInterface $rowData) {
        /** @var ArticleInterface $article */
        $rowData->offsetSet('id', $article->getId()); // id je autoincrement - readonly, hodnota pro where
        $rowData->offsetSet('menu_item_id_fk', $article->getMenuItemIdFk());
        $rowData->offsetSet('article', $article->getContent());
        $rowData->offsetSet('template', $article->getTemplate());
        $rowData->offsetSet('editor', $article->getEditor());
        // updated je timestamp
        // id je autoincrement - readonly
    }

}
