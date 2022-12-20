<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\RowHydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Red\Model\Entity\PaperInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class PaperHydrator extends TypeHydratorAbstract implements RowHydratorInterface {

    /**
     *
     * @param PaperInterface $paper
     * @param type $rowData
     */
    public function hydrate(EntityInterface $paper, RowDataInterface $rowData) {
        /** @var PaperInterface $paper */
        $paper
            ->setId($this->getPhpValue($rowData,'id'))
            ->setMenuItemIdFk($this->getPhpValue($rowData,'menu_item_id_fk'))
            ->setHeadline($this->getPhpValue($rowData,'headline'))
            ->setPerex($this->getPhpValue($rowData,'perex'))
            ->setTemplate($this->getPhpValue($rowData,'template'))
            ->setKeywords($this->getPhpValue($rowData,'keywords'))
            ->setEditor($this->getPhpValue($rowData,'editor'))
            ->setUpdated($this->getPhpDatetime($rowData,'updated'));
    }

    /**
     *
     * @param PaperInterface $paper
     * @param type $rowData
     */
    public function extract(EntityInterface $paper, RowDataInterface $rowData) {
        /** @var PaperInterface $paper */
        $this->setSqlValue($rowData, 'id',  $paper->getId()); // id je autoincrement - readonly, hodnota pro where
        $this->setSqlValue($rowData, 'menu_item_id_fk',  $paper->getMenuItemIdFk());
        $this->setSqlValue($rowData, 'headline',  $paper->getHeadline());
        $this->setSqlValue($rowData, 'perex',  $paper->getPerex());
        $this->setSqlValue($rowData, 'template',  $paper->getTemplate());
        $this->setSqlValue($rowData, 'keywords',  $paper->getKeywords());
        $this->setSqlValue($rowData, 'editor',  $paper->getEditor());
        // updated je timestamp
        // id je autoincrement - readonly
    }

}
