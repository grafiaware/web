<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Pes\Dom\Node\Tag\TagAbstract;
use Pes\Dom\Node\Attributes\DivAttributes;

/**
 * Tag Headline je custom tag, reálně s ním prohlížeč zachází jako s tagem div. Proto má jako vlatnost atributes použit objekt DivAttributes.
 *
 * @author pes2704
 */
class Headline extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'div';
        $this->attributes = new DivAttributes($attributes);
    }

    /**
     *
     * @return DivAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }
}