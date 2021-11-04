<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface LanguageInterface extends EntityInterface {
    public function getLangCode();
    public function getLocale();
    public function getName();
    public function getCollation();
    public function getState();
    public function setLangCode($langCode): LanguageInterface;
    public function setLocale($locale): LanguageInterface;
    public function setName($name): LanguageInterface;
    public function setCollation($collation): LanguageInterface;
    public function setState($state): LanguageInterface;
}
