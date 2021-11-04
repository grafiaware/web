<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of Language
 *
 * @author pes2704
 */
class Language extends EntityAbstract implements LanguageInterface {

    private $langCode;
    private $locale;
    private $name;
    private $collation;
    private $state;

    private $keyAttribute = 'lang_code';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getLangCode() {
        return $this->langCode;
    }

    public function getLocale() {
        return $this->locale;
    }

    public function getName() {
        return $this->name;
    }

    public function getCollation() {
        return $this->collation;
    }

    public function getState() {
        return $this->state;
    }

    public function setLangCode($langCode): LanguageInterface {
        $this->langCode = $langCode;
        return $this;
    }

    public function setLocale($locale): LanguageInterface {
        $this->locale = $locale;
        return $this;
    }

    public function setName($name): LanguageInterface {
        $this->name = $name;
        return $this;
    }

    public function setCollation($collation): LanguageInterface {
        $this->collation = $collation;
        return $this;
    }

    public function setState($state): LanguageInterface {
        $this->state = $state;
        return $this;
    }
}
