<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Access\Enum;

use Pes\Type\Enum;

/**
 * Description of RoleEnum
 *
 * @author pes2704
 */
class RoleEnum  extends Enum {
    const ANONYMOUS = 'anonymous';
    const AUTHENTICATED = 'authenticated';
    const EDITOR = 'editor';
    const SUPERVISOR = 'supervisor';
    const VISITOR = 'visitor';
    const REPRESENTATIVE = 'representative';

}
