<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\VisitorDataPostInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class VisitorDataPostHydrator implements HydratorInterface {

    /**
     *
     * @param VisitorDataPostInterface $visitorDataPost
     * @param type $row
     */
    public function hydrate(EntityInterface $visitorDataPost, &$row) {
        /** @var VisitorDataPostInterface $visitorDataPost */
        $visitorDataPost
            ->setPrefix($row['prefix'])
            ->setName($row['name'])
            ->setSurname($row['surname'])
            ->setPostfix($row['postfix'])
            ->setEmail($row['email'])
            ->setPhone($row['phone'])
            ->setCvEducationText($row['cv_education_text'])
            ->setCvSkillsText($row['cv_skills_text'])
            ->setCvDocument($row['cv_document'])
            ->setCvDocumentFilename($row['cv_document_filename'])
            ->setCvDocumentMimetype($row['cv_document_mimetype'])
            ->setLetterDocument($row['letter_document'])
            ->setLetterDocumentFilename($row['letter_document_filename'])
            ->setLetterDocumentMimetype($row['letter_document_mimetype'])
            // primary key
            ->setLoginName($row['login_name'])
            ->setShortName($row['short_name'])
            ->setPositionName($row['position_name']);
    }

    /**
     *
     * @param VisitorDataPostInterface $visitorDataPost
     * @param type $row
     */
    public function extract(EntityInterface $visitorDataPost, &$row) {
        /** @var VisitorDataPostInterface $visitorDataPost */
            $row['prefix'] = $visitorDataPost->getPrefix();
            $row['name'] = $visitorDataPost->getName();
            $row['surname'] = $visitorDataPost->getSurname();
            $row['postfix'] = $visitorDataPost->getPostfix();
            $row['email'] = $visitorDataPost->getEmail();
            $row['phone'] = $visitorDataPost->getPhone();
            $row['cv_education_text'] = $visitorDataPost->getCvEducationText();
            $row['cv_skills_text'] = $visitorDataPost->getCvSkillsText();
            $row['cv_document'] = $visitorDataPost->getCvDocument();
            $row['cv_document_filename'] = $visitorDataPost->getCvDocumentFilename();
            $row['cv_document_mimetype'] = $visitorDataPost->getCvDocumentMimetype();
            $row['letter_document'] = $visitorDataPost->getLetterDocument();
            $row['letter_document_filename'] = $visitorDataPost->getLetterDocumentFilename();
            $row['letter_document_mimetype'] = $visitorDataPost->getLetterDocumentMimetype();
            // primary key
            $row['login_name'] = $visitorDataPost->getLoginName();
            $row['short_name'] = $visitorDataPost->getShortName();
            $row['position_name'] = $visitorDataPost->getPositionName();
    }

}
