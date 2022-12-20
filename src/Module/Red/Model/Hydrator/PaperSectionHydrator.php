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

use Red\Model\Entity\PaperSectionInterface;
use Pes\Type\Date;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class PaperSectionHydrator extends TypeHydratorAbstract implements RowHydratorInterface {

    /**
     *
     * @param PaperInterface $paperContent
     * @param type $rowData
     */
    public function hydrate(EntityInterface $paperContent, RowDataInterface $rowData) {
        /** @var PaperSectionInterface $paperContent */
        $paperContent
            ->setId($this->getPhpValue($rowData, 'id'))
            ->setPaperIdFk($this->getPhpValue($rowData, 'paper_id_fk'))
            ->setContent($this->getPhpValue($rowData, 'content'))
            ->setTemplateName($this->getPhpValue($rowData, 'template_name'))
            ->setTemplate($this->getPhpValue($rowData, 'template'))
            ->setActive($this->getPhpValue($rowData, 'active'))
            ->setPriority($this->getPhpValue($rowData, 'priority'))
            ->setShowTime($this->getPhpDatetime($rowData, 'show_time'))
            ->setHideTime($this->getPhpDatetime($rowData, 'hide_time'))
            ->setEventStartTime($this->getPhpDatetime($rowData, 'event_start_time'))
            ->setEventEndTime($this->getPhpDatetime($rowData, 'event_end_time'))
            ->setEditor($this->getPhpValue($rowData, 'editor'))
            ->setUpdated($this->getPhpDatetime($rowData, 'updated'))  // včetně času
            ;
    }

    /**
     *
     * @param PaperInterface $paperContent
     * @param type $rowData
     */
    public function extract(EntityInterface $paperContent, RowDataInterface $rowData) {
        /** @var PaperSectionInterface $paperContent */
        $this->setSqlValue($rowData, 'id', $paperContent->getId());
        $this->setSqlValue($rowData, 'paper_id_fk',  $paperContent->getPaperIdFk());
        $this->setSqlValue($rowData, 'content',  $paperContent->getContent());
        $this->setSqlValue($rowData, 'template_name',  $paperContent->getTemplateName());
        $this->setSqlValue($rowData, 'template',  $paperContent->getTemplate());
        $this->setSqlValue($rowData, 'active',  $paperContent->getActive());
        $this->setSqlValue($rowData, 'priority',  $paperContent->getPriority());
        $this->setSqlDatetime($rowData, 'show_time',  $paperContent->getShowTime());
        $this->setSqlDatetime($rowData, 'hide_time',  $paperContent->getHideTime());
        $this->setSqlDatetime($rowData, 'event_start_time',  $paperContent->getEventStartTime());
        $this->setSqlDatetime($rowData, 'event_end_time',  $paperContent->getEventEndTime());
        $this->setSqlValue($rowData, 'editor',  $paperContent->getEditor());
        // updated je timestamp
        // actual je readonly
    }

}
