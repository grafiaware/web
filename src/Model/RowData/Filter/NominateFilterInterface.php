<?php

namespace Model\RowData\Filter;

/**
 *
 * @author pes2704
 */
interface NominateFilterInterface {
    public function nominate(array $names=[]): void;
    public function accept(): bool;
}
