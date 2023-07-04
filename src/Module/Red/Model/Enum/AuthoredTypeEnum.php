<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Enum;

use Pes\Type\Enum;

/**
 * Description of AuthoredEnum
 * 
 * Výčet typů authoringového obsahu (editovatelného obsahu)
 * POZOR! Hodnoty se používají mimo jiné i pro konstruování url řetězců (pro authoringové templaty) a musí odpovídat zaregistrovanýmm url v API.
 *
 * @author pes2704
 */
class AuthoredTypeEnum extends Enum {

    const PAPER = 'paper';
    const ARTICLE =  'article';
    const MULTIPAGE = 'multipage';
}
