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

use Events\Model\Entity\VisitorDataInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class VisitorDataHydrator implements HydratorInterface {

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $rowData
     */
    public function hydrate(EntityInterface $visitorData, RowDataInterface $rowData) {
        /** @var VisitorDataInterface $visitorData */
        $visitorData
            ->setPrefix($rowData->offsetGet('prefix'))
            ->setName($rowData->offsetGet('name'))
            ->setSurname($rowData->offsetGet('surname'))
            ->setPostfix($rowData->offsetGet('postfix'))
            ->setEmail($rowData->offsetGet('email'))
            ->setPhone($rowData->offsetGet('phone'))
            ->setCvEducationText($rowData->offsetGet('cv_education_text'))
            ->setCvSkillsText($rowData->offsetGet('cv_skills_text'))
            ->setCvDocument($rowData->offsetGet('cv_document'))
            ->setCvDocumentFilename($rowData->offsetGet('cv_document_filename'))
            ->setCvDocumentMimetype($rowData->offsetGet('cv_document_mimetype'))
            ->setLetterDocument($rowData->offsetGet('letter_document'))
            ->setLetterDocumentFilename($rowData->offsetGet('letter_document_filename'))
            ->setLetterDocumentMimetype($rowData->offsetGet('letter_document_mimetype'))

            ->setLoginName($rowData->offsetGet('login_name'));
    }

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $rowData
     */
    public function extract(EntityInterface $visitorData, RowDataInterface $rowData) {
        /** @var VisitorDataInterface $visitorData */
            $rowData->offsetSet('prefix', $visitorData->getPrefix());
            $rowData->offsetSet('name', $visitorData->getName());
            $rowData->offsetSet('surname', $visitorData->getSurname());
            $rowData->offsetSet('postfix', $visitorData->getPostfix());
            $rowData->offsetSet('email', $visitorData->getEmail());
            $rowData->offsetSet('phone', $visitorData->getPhone());
            $rowData->offsetSet('cv_education_text', $visitorData->getCvEducationText());
            $rowData->offsetSet('cv_skills_text', $visitorData->getCvSkillsText());
            $rowData->offsetSet('cv_document', $visitorData->getCvDocument());
            $rowData->offsetSet('cv_document_filename', $visitorData->getCvDocumentFilename());
            $rowData->offsetSet('cv_document_mimetype', $visitorData->getCvDocumentMimetype());
            $rowData->offsetSet('letter_document', $visitorData->getLetterDocument());
            $rowData->offsetSet('letter_document_filename', $visitorData->getLetterDocumentFilename());
            $rowData->offsetSet('letter_document_mimetype', $visitorData->getLetterDocumentMimetype());

            $rowData->offsetSet('login_name', $visitorData->getLoginName());
    }

}
