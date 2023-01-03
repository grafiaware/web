<?php

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Builder\SqlInterface;
use Model\Context\ContextFactoryInterface;
use Model\RowData\RowDataInterface;

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

    /**
     * {@inheritDoc}
     *
     * @param array $id
     * @return RowDataInterface|null
     */
    public function get(array $id): ?RowDataInterface {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                        $this->getContextConditions(),
                        $this->sql->touples($this->getPrimaryKeyAttributes())
                    )
                );
        $touplesToBind = $this->getPrimaryKeyPlaceholdersValues($id);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getOutOfContext(array $id): ?RowDataInterface {
        return parent::get($id);
    }

    /**
     * {@inheritDoc}
     *
     * @param array $unique
     * @return RowDataInterface|null
     */
    public function getUniqueOutOfContext(array $unique): ?RowDataInterface {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                                        $this->getContextConditions(),
                                        $this->sql->touples(array_keys($unique)))
                                  );
        $touplesToBind = $this->getPrimaryKeyPlaceholdersValues($unique);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }
    /**
     * {@inheritDoc}
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return iterable
     */
    public function find($whereClause = "", $touplesToBind = []): iterable {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                        $this->getContextConditions(),
                        $whereClause));
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    /**
     * {@inheritDoc}
     *
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return iterable
     */
    public function findOutOfContext($whereClause = "", $touplesToBind = []): iterable {
        return parent::find($whereClause, $touplesToBind);
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable
     */
    public function findAll(): iterable {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->getContextConditions());
        return $this->selectMany($select, $from, $where, []);
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable
     */
    public function findAllOutOfContext(): iterable {
        return parent::findAll();
    }
}
