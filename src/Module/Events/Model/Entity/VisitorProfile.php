<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of VisitorData
 *
 * @author pes2704
 */
class VisitorProfile extends EntityAbstract implements VisitorProfileInterface {

    private $loginLoginName;
    private $prefix;
    private $name;
    private $surname;
    private $postfix;
    private $email;
    private $phone;
    private $cvEducationText;
    private $cvSkillsText;
    private $cvDocument;
    private $letterDocument;


    private $keyAttribute = 'login_login_name';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }
    public function getLoginLoginName() {
        return $this->loginLoginName;
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

    public function getLetterDocument() {
        return $this->letterDocument;
    }

    public function setLoginLoginName($loginLoginName): VisitorProfileInterface {
        $this->loginLoginName = $loginLoginName;
        return $this;
    }

    public function setPrefix($prefix): VisitorProfileInterface {
        $this->prefix = $prefix;
        return $this;
    }

    public function setName($name): VisitorProfileInterface {
        $this->name = $name;
        return $this;
    }

    public function setSurname($surname): VisitorProfileInterface {
        $this->surname = $surname;
        return $this;
    }

    public function setPostfix($postfix): VisitorProfileInterface {
        $this->postfix = $postfix;
        return $this;
    }

    public function setEmail($email): VisitorProfileInterface {
        $this->email = $email;
        return $this;
    }

    public function setPhone($phone): VisitorProfileInterface {
        $this->phone = $phone;
        return $this;
    }

    public function setCvEducationText($cvEducationText): VisitorProfileInterface {
        $this->cvEducationText = $cvEducationText;
        return $this;
    }

    public function setCvSkillsText($cvSkillsText): VisitorProfileInterface {
        $this->cvSkillsText = $cvSkillsText;
        return $this;
    }

    public function setCvDocument($cvDocument): VisitorProfileInterface {
        $this->cvDocument = $cvDocument;
        return $this;
    }

    public function setLetterDocument($letterDocument): VisitorProfileInterface {
        $this->letterDocument = $letterDocument;
        return $this;
    }

}
