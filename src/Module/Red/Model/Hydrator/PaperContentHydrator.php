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

use Red\Model\Entity\PaperContentInterface;
use Pes\Type\Date;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class PaperContentHydrator implements HydratorInterface {

    /**
     *
     * @param PaperInterface $paperContent
     * @param type $rowData
     */
    public function hydrate(EntityInterface $paperContent, RowDataInterface $rowData) {
        /** @var PaperContentInterface $paperContent */
        $paperContent
            ->setId($rowData->offsetGet('id'))
            ->setPaperIdFk($rowData->offsetGet('paper_id_fk'))
            ->setContent($rowData->offsetGet('content'))
            ->setTemplateName($rowData->offsetGet('template_name'))
            ->setTemplate($rowData->offsetGet('template'))
            ->setActive($rowData->offsetGet('active'))
            ->setPriority($rowData->offsetGet('priority'))
            ->setShowTime($rowData->offsetGet('show_time') ? \DateTime::createFromFormat('Y-m-d', $rowData->offsetGet('show_time')) : NULL)
            ->setHideTime($rowData->offsetGet('hide_time') ? \DateTime::createFromFormat('Y-m-d', $rowData->offsetGet('hide_time')) : NULL)
            ->setEventStartTime($rowData->offsetGet('event_start_time') ? \DateTime::createFromFormat('Y-m-d', $rowData->offsetGet('event_start_time')) : NULL)
            ->setEventEndTime($rowData->offsetGet('event_end_time') ? \DateTime::createFromFormat('Y-m-d', $rowData->offsetGet('event_end_time')) : NULL)
            ->setEditor($rowData->offsetGet('editor'))
            ->setUpdated($rowData->offsetGet('updated') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('updated')) : NULL)  // včetně času
            ;
    }

    /**
     *
     * @param PaperInterface $paperContent
     * @param type $rowData
     */
    public function extract(EntityInterface $paperContent, RowDataInterface $rowData) {
        /** @var PaperContentInterface $paperContent */
        $rowData->offsetSet('id',  $paperContent->getId());
        $rowData->offsetSet('paper_id_fk',  $paperContent->getPaperIdFk());
        $rowData->offsetSet('content',  $paperContent->getContent());
        $rowData->offsetSet('template_name',  $paperContent->getTemplateName());
        $rowData->offsetSet('template',  $paperContent->getTemplate());
        $rowData->offsetSet('active',  $paperContent->getActive());
        $rowData->offsetSet('priority',  $paperContent->getPriority());
        $rowData->offsetSet('show_time',  $paperContent->getShowTime() ? $paperContent->getShowTime()->format("Y-m-d") : null);
        $rowData->offsetSet('hide_time',  $paperContent->getHideTime() ? $paperContent->getHideTime()->format("Y-m-d") : null);
        $rowData->offsetSet('event_start_time',  $paperContent->getEventStartTime() ? $paperContent->getEventStartTime()->format("Y-m-d") : null);
        $rowData->offsetSet('event_end_time',  $paperContent->getEventEndTime() ? $paperContent->getEventEndTime()->format("Y-m-d") : null);
        $rowData->offsetSet('editor',  $paperContent->getEditor());
        // updated je timestamp
        // actual je readonly
    }

}
