<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Red\Model\Entity\ArticleInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class ArticleHydrator extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param ArticleInterface $article
     * @param type $rowData
     */
    public function hydrate(EntityInterface $article, RowDataInterface $rowData) {
        /** @var ArticleInterface $article */
        $article
            ->setId($this->getPhpValue($rowData,'id'))
            ->setMenuItemIdFk($this->getPhpValue($rowData,'menu_item_id_fk'))
            ->setContent($this->getPhpValue($rowData,'article'))
            ->setTemplate($this->getPhpValue($rowData,'template'))
            ->setEditor($this->getPhpValue($rowData,'editor'))
            ->setUpdated($this->getPhpDatetime($rowData,'updated'));
    }

    /**
     *
     * @param ArticleInterface $article
     * @param type $rowData
     */
    public function extract(EntityInterface $article, RowDataInterface $rowData) {
        /** @var ArticleInterface $article */
        $this->setSqlValue($rowData, 'id', $article->getId()); // id je autoincrement - readonly, hodnota pro where
        $this->setSqlValue($rowData, 'menu_item_id_fk', $article->getMenuItemIdFk());
        $this->setSqlValue($rowData, 'article', $article->getContent());
        $this->setSqlValue($rowData, 'template', $article->getTemplate());
        $this->setSqlValue($rowData, 'editor', $article->getEditor());
        // updated je timestamp
        // id je autoincrement - readonly
    }

}
