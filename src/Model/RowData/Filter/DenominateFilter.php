<?php

namespace Model\RowData\Filter;

/**
 * Description of NominateFilter
 *
 * @author pes2704
 */
class DenominateFilter extends \FilterIterator implements DenominateFilterInterface {

    private $names = [];

    public function denominate(array $names=[]): void {
        $this->names = array_flip($names);
    }

    public function accept(): bool {
        return !array_key_exists($this->key(), $this->names);
    }
}