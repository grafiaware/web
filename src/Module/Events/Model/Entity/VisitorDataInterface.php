<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface VisitorDataInterface extends EntityInterface {

    public function getLoginName();

    public function getPrefix();

    public function getName();

    public function getSurname();

    public function getPostfix();

    public function getEmail();

    public function getPhone();

    public function getCvEducationText();

    public function getCvSkillsText();

    public function getCvDocument();

    public function getCvDocumentFilename();

    public function getCvDocumentMimetype();

    public function getLetterDocument();

    public function getLetterDocumentFilename();

    public function getLetterDocumentMimetype();

    public function setLoginName($loginName): VisitorDataInterface;

    public function setPrefix($prefix): VisitorDataInterface;

    public function setName($name): VisitorDataInterface;

    public function setSurname($surname): VisitorDataInterface;

    public function setPostfix($postfix): VisitorDataInterface;

    public function setEmail($email): VisitorDataInterface;

    public function setPhone($phone): VisitorDataInterface;

    public function setCvEducationText($cvEducationText): VisitorDataInterface;

    public function setCvSkillsText($cvSkillsText): VisitorDataInterface;

    public function setCvDocument($cvDocument): VisitorDataInterface;

    public function setCvDocumentFilename($cvDocumentFilename): VisitorDataInterface;

    public function setCvDocumentMimetype($cvDocumentMimetype): VisitorDataInterface;

    public function setLetterDocument($letterDocument): VisitorDataInterface;

    public function setLetterDocumentFilename($letterDocumentFilename): VisitorDataInterface;

    public function setLetterDocumentMimetype($letterDocumentMimetype): VisitorDataInterface;

}
