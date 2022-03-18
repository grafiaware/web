<?php

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Context\ContextFactoryInterface;

/**
 * Description of DaoContextualAbstract
 *
 * @author pes2704
 */
abstract class DaoContextualAbstract extends DaoTableAbstract implements DaoContextualAbstractInterface {

    /**
     * @var ContextFactoryInterface
     */
    private $contextFactory;


    public function __construct(HandlerInterface $handler, $fetchClassName="", ContextFactoryInterface $contextFactory=null) {
        parent::__construct($handler, $fetchClassName);
        $this->contextFactory = $contextFactory;
    }

}
