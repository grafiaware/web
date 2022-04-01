<?php

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Builder\SqlInterface;

use Model\Context\ContextFactoryInterface;

/**
 * Description of DaoContextualAbstract
 *
 * @author pes2704
 */
abstract class DaoContextualAbstract extends DaoEditAbstract implements DaoContextualAbstractInterface {

    /**
     * @var ContextFactoryInterface
     */
    private $contextFactory;


    public function __construct(HandlerInterface $handler, SqlInterface $sql, $fetchClassName, ContextFactoryInterface $contextFactory=null) {
        parent::__construct($handler, $sql, $fetchClassName);
        $this->contextFactory = $contextFactory;
    }

}
