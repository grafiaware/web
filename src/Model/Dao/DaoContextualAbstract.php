<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Context\ContextFactoryInterface;

/**
 * Description of DaoContextAbstract
 *
 * @author pes2704
 */
abstract class DaoContextualAbstract extends DaoAbstract {

    /**
     *
     * @var ContextFactoryInterface
     */
    protected $contextFactory;


    public function __construct(HandlerInterface $dbHandler, $fetchClassName="", ContextFactoryInterface $context=null) {
        parent::__construct($dbHandler, $fetchClassName);
        $this->contextFactory = $context;
    }


    protected function getContextConditions() {
        return [];
    }
}
