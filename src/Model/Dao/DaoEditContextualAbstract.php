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
abstract class DaoEditContextualAbstract extends DaoEditAbstract implements DaoContextualInterface {

    /**
     * @var ContextFactoryInterface
     */
    private $contextFactory;


    public function __construct(HandlerInterface $handler, SqlInterface $sql, $fetchClassName, ContextFactoryInterface $contextFactory=null) {
        parent::__construct($handler, $sql, $fetchClassName);
        $this->contextFactory = $contextFactory;
    }

    public function get(array $id) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                        $this->getContextConditions(),
                        $this->sql->touples($this->getPrimaryKeyAttributes())
                    )
                );
        $touplesToBind = $this->getPrimaryKeyTouplesToBind($id);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getOutOfContext(array $id) {
        return parent::get($id);
    }

    /**
     * {@inheritDoc}
     *
     * @param array $unique
     * @return type
     */
    public function getUniqueOutOfContext(array $unique) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                                        $this->getContextConditions(),
                                        $this->sql->touples(array_keys($unique)))
                                  );
        $touplesToBind = $this->getPrimaryKeyTouplesToBind($unique);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause = "", $touplesToBind = []): iterable {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                        $this->getContextConditions(),
                        $whereClause));
        return $this->selectMany($select, $from, $where, $touplesToBind);        
    }

    public function findOutOfContext($whereClause = "", $touplesToBind = []): iterable {
        return parent::find($whereClause, $touplesToBind);
    }

    public function findAll(): iterable {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->getContextConditions());
        return $this->selectMany($select, $from, $where, []);
    }

    public function findAllOutOfContext(): iterable {
        return parent::findAll();
    }
}
