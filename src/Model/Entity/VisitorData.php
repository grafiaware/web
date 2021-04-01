<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of VisitorData
 *
 * @author pes2704
 */
class VisitorData extends EntityAbstract implements VisitorDataInterface {

    private $loginName;
    private $prefix;
    private $name;
    private $surname;
    private $postfix;
    private $email;
    private $phone;
    private $cvEducationText;
    private $cvSkillsText;
    private $cvDocument;
    private $cvDocumentFilename;
    private $cvDocumentMimetype;
    private $letterDocument;
    private $letterDocumentFilename;
    private $letterDocumentMimetype;

    private $keyAttribute = 'login_name';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getLoginName() {
        return $this->loginName;
    }

    public function getPrefix() {
        return $this->prefix;
    }

    public function getName() {
        return $this->name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function getPostfix() {
        return $this->postfix;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getCvEducationText() {
        return $this->cvEducationText;
    }

    public function getCvSkillsText() {
        return $this->cvSkillsText;
    }

    public function getCvDocument() {
        return $this->cvDocument;
    }

    public function getCvDocumentFilename() {
        return $this->cvDocumentFilename;
    }

    public function getCvDocumentMimetype() {
        return $this->cvDocumentMimetype;
    }

    public function getLetterDocument() {
        return $this->letterDocument;
    }

    public function getLetterDocumentFilename() {
        return $this->letterDocumentFilename;
    }

    public function getLetterDocumentMimetype() {
        return $this->letterDocumentMimetype;
    }

    public function setLoginName($loginName): VisitorDataInterface {
        $this->loginName = $loginName;
        return $this;
    }

    public function setPrefix($prefix): VisitorDataInterface {
        $this->prefix = $prefix;
        return $this;
    }

    public function setName($name): VisitorDataInterface {
        $this->name = $name;
        return $this;
    }

    public function setSurname($surname): VisitorDataInterface {
        $this->surname = $surname;
        return $this;
    }

    public function setPostfix($postfix): VisitorDataInterface {
        $this->postfix = $postfix;
        return $this;
    }

    public function setEmail($email): VisitorDataInterface {
        $this->email = $email;
        return $this;
    }

    public function setPhone($phone): VisitorDataInterface {
        $this->phone = $phone;
        return $this;
    }

    public function setCvEducationText($cvEducationText): VisitorDataInterface {
        $this->cvEducationText = $cvEducationText;
        return $this;
    }

    public function setCvSkillsText($cvSkillsText): VisitorDataInterface {
        $this->cvSkillsText = $cvSkillsText;
        return $this;
    }

    public function setCvDocument($cvDocument): VisitorDataInterface {
        $this->cvDocument = $cvDocument;
        return $this;
    }

    public function setCvDocumentFilename($cvDocumentFilename): VisitorDataInterface {
        $this->cvDocumentFilename = $cvDocumentFilename;
        return $this;
    }

    public function setCvDocumentMimetype($cvDocumentMimetype): VisitorDataInterface {
        $this->cvDocumentMimetype = $cvDocumentMimetype;
        return $this;
    }

    public function setLetterDocument($letterDocument): VisitorDataInterface {
        $this->letterDocument = $letterDocument;
        return $this;
    }

    public function setLetterDocumentFilename($letterDocumentFilename): VisitorDataInterface {
        $this->letterDocumentFilename = $letterDocumentFilename;
        return $this;
    }

    public function setLetterDocumentMimetype($letterDocumentMimetype): VisitorDataInterface {
        $this->letterDocumentMimetype = $letterDocumentMimetype;
        return $this;
    }


}
