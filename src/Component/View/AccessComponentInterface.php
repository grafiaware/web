<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

/**
 *
 * @author pes2704
 */
interface AccessComponentInterface {
    public function isAllowedToPresent($action): bool;
    public function getComponentPermissions(): array;

}
