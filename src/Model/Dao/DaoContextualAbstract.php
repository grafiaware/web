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
abstract class DaoContextualAbstract extends DaoEditAbstract implements DaoContextualInterface {

    /**
     * @var ContextFactoryInterface
     */
    private $contextFactory;


    public function __construct(HandlerInterface $handler, SqlInterface $sql, $fetchClassName, ContextFactoryInterface $contextFactory=null) {
        parent::__construct($handler, $sql, $fetchClassName);
        $this->contextFactory = $contextFactory;
    }

//            $where = $this->where($this->and($this->getContextConditions(), ["`event`.`event_content_id_fk` = :event_content_id_fk"] ));

    public function get(array $id) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                        $this->getContextConditions(),
                        $this->sql->touples($this->getPrimaryKeyAttribute())
                    )
                );
        $touplesToBind = $this->getPrimaryKeyTouplesToBind($id);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getOutOfContext(array $id) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                        $this->sql->touples($this->getPrimaryKeyAttribute())
                    )
                );
        $touplesToBind = $this->getPrimaryKeyTouplesToBind($id);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause = "", $touplesToBind = []): iterable {
        $where = $this->sql->where($this->sql->and(
                        $this->getContextConditions(),
                        $whereClause));
        return parent::find($where, $touplesToBind);
    }

    public function findOutOfContext($whereClause = "", $touplesToBind = []): iterable {
        return parent::find($whereClause, $touplesToBind);
    }

}
