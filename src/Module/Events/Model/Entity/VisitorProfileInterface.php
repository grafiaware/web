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
interface VisitorProfileInterface extends EntityInterface {
    
    public function getLoginLoginName();

    public function getPrefix();

    public function getName();

    public function getSurname();

    public function getPostfix();

    public function getEmail();

    public function getPhone();

    public function getCvEducationText();

    public function getCvSkillsText();

    public function getCvDocument();

    public function getLetterDocument();

    public function setLoginLoginName($loginLoginName): VisitorProfileInterface;

    public function setPrefix($prefix): VisitorProfileInterface;

    public function setName($name): VisitorProfileInterface;

    public function setSurname($surname): VisitorProfileInterface;

    public function setPostfix($postfix): VisitorProfileInterface;

    public function setEmail($email): VisitorProfileInterface;

    public function setPhone($phone): VisitorProfileInterface;

    public function setCvEducationText($cvEducationText): VisitorProfileInterface;

    public function setCvSkillsText($cvSkillsText): VisitorProfileInterface;

    public function setCvDocument($cvDocument): VisitorProfileInterface;

    public function setLetterDocument($letterDocument): VisitorProfileInterface;
}
