<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\DocumentInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class DocumentHydrator implements HydratorInterface {

    /**
     *
     * @param DocumentInterface $document
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $document, RowDataInterface $rowData) {
        /** @var DocumentInterface $document */
        $document
                ->setId($rowData->offsetGet('id'))
                ->setDocument($rowData->offsetGet('document'))
                ->setDocumentFilename($rowData->offsetGet('document_filename'))
                ->setDocumentMimetype($rowData->offsetGet('document_mimetype'));
    }

    /**
     *
     * @param DocumentInterface $document
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $document, RowDataInterface $rowData) {
        /** @var DocumentInterface $document */
            // id je autoincrement
            $rowData->offsetSet('document', $document->getDocument());
            $rowData->offsetSet('document_filename', $document->getDocumentFilename());
            $rowData->offsetSet('document_mimetype', $document->getDocumentMimetype());
    }

}
