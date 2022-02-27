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
 * @author pes2704
 */
class AuthoredTypeEnum extends Enum {
//    db menu_item:
//article
//empty
//multipage
//paper
//redirect
//root
//static
//trash
    const PAPER = 'paper';
    const ARTICLE =  'article';
    const MULTIPAGE = 'multipage';
    const ITEM_TYPE_SELECT = 'item_type_select';
}
