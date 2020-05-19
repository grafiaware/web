<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\PaperInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class PaperHydrator implements HydratorInterface {

    /**
     *
     * @param PaperInterface $paper
     * @param type $row
     */
    public function hydrate(EntityInterface $paper, &$row) {
        /** @var PaperInterface $paper */
        $paper
            ->setMenuItemIdFk($row['menu_item_id_fk'])
            ->setPaperHeadline($row['headline'])
            ->exchangePaperContentsArray($row['contents']);
    }

    /**
     *
     * @param PaperInterface $paper
     * @param type $row
     */
    public function extract(EntityInterface $paper, &$row) {
        /** @var PaperInterface $paper */
        $row['menu_item_id_fk'] = $paper->getMenuItemIdFk();
        $row['headline'] = $paper->getPaperHeadline();
        $row['contents'] = $paper->getPaperContentsArray();
    }

}
