<?php

namespace Events\Model\Dao;

use Model\Dao\DaoContextualAbstract;
use Model\Dao\DaoAutoincrementTrait;

use Events\Model\Dao\EventDaoInterface;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class EventDao extends DaoContextualAbstract implements EventDaoInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'published',
            'start',
            'end',
            'enroll_link_id_fk',
            'enter_link_id_fk',
            'event_content_id_fk'
        ];
    }

    public function getTableName(): string {
        return 'event';
    }

    protected function getContextConditions() {
        $contextConditions = [];
        if (isset($this->contextFactory)) {
            $publishedContext = $this->contextFactory->createPublishedContext();
            if ($publishedContext) {
                if ($publishedContext->selectPublished()) {
                    $contextConditions['published'] = "event.published = 1";
                }
            }
        }

        return $contextConditions;
    }

    /**
     *
     * @param type $eventContentIdFk
     * @return array
     */
    public function getByEventContentIdFk(array $eventContentIdFk ) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->getContextConditions(), $this->sql->touples(['event_content_id_fk'])));
        $touplesToBind = $this->getTouplesToBind(array_combine(['event_content_id_fk'], $eventContentIdFk));
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

}
