<?php

namespace Model\RowData\Filter;

/**
 *
 * @author pes2704
 */
interface DenominateFilterInterface {
    public function denominate(array $names=[]): void;
    public function accept(): bool;
}
