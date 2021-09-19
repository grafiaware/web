<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;

use Model\Entity\EntityInterface;
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
     * @param type $row
     */
    public function hydrate(EntityInterface $multipage, &$row) {
        /** @var MultipageInterface $multipage */
        $multipage
            ->setId($row['id'])
            ->setMenuItemIdFk($row['menu_item_id_fk'])
            ->setTemplate($row['template'])
            ->setEditor($row['editor'])
            ->setUpdated($row['updated'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) : NULL);
    }

    /**
     *
     * @param MultipageInterface $article
     * @param type $row
     */
    public function extract(EntityInterface $article, &$row) {
        /** @var MultipageInterface $article */
        $row['id'] = $article->getId(); // id je autoincrement - readonly, hodnota pro where
        $row['menu_item_id_fk'] = $article->getMenuItemIdFk();
        $row['template'] = $article->getTemplate();
        $row['editor'] = $article->getEditor();
        // updated je timestamp
        // id je autoincrement - readonly
    }

}
