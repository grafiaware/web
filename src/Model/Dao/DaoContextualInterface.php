<?php
namespace Model\Dao;

/**
 *
 * @author pes2704
 */
interface DaoContextualInterface {
    public function getOutOfContext(array $id);
    public function getUniqueOutOfContext(array $unique);
    public function findOutOfContext($whereClause = "", $touplesToBind = []): iterable;
    public function findAllOutOfContext(): iterable;
}
