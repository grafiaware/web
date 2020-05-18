<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\PaperContentInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class PaperContentHydrator implements HydratorInterface {

    /**
     *
     * @param PaperInterface $paperContent
     * @param type $row
     */
    public function hydrate(EntityInterface $paperContent, &$row) {
        /** @var PaperContentInterface $paperContent */
        $paperContent
            ->setMenuItemIdFk($row['menu_item_id_fk'])
            ->setId($row['id'])
            ->setContent($row['content'])
            ->setEditor($row['editor'])
            ->setUpdated($row['updated'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) : NULL);
    }

    /**
     *
     * @param PaperInterface $paperContent
     * @param type $row
     */
    public function extract(EntityInterface $paperContent, &$row) {
        /** @var PaperContentInterface $paperContent */
        $row['menu_item_id_fk'] = $paperContent->getMenuItemIdFk();
        $row['id'] = $paperContent->getId();
        $row['content'] = $paperContent->getContent();
        $row['editor'] = $paperContent->getEditor();
        // updated je timestamp
    }

}
