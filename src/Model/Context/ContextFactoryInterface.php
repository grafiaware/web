<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Context;

/**
 *
 * @author pes2704
 */
interface ContextFactoryInterface {
    public function createPublishedContext(): PublishedContextInterface;
}
