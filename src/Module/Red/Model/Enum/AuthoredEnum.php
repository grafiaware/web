<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Enum;

use Pes\Type\Enum;

/**
 * Description of AuzhoredEnum
 *
 * @author pes2704
 */
class AuthoredEnum extends Enum {
    const PAPER = 'paper';
    const ARTICLE =  'article';
    const TEXTELEMENT = 'textelement';
    const HTMLELEMENT = 'htmlelement';
    const MULTIPAGE = 'multipage';
}
