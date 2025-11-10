<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace FrontControler;

use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @author pes2704
 */
interface ComponentControlerInterface {
    public function component(ServerRequestInterface $request, $name);
}
