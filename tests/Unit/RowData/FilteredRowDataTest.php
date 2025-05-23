<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Model\RowData\RowDataInterface;
use Model\RowData\RowData;
use Model\RowData\Filter\NominateFilter;
/**
 * Description of RowDataTest
 *
 * @author pes2704
 */
class FilteredRowDataTest extends TestCase {

    public static function mock() {
    }

    public static function setUpBeforeClass(): void {
    }

    protected function setUp(): void {
    }

    public function testAdd() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);

        $rowData->offsetSet(2, 'TRTRTS');
        $rowData->offsetSet('newnull', null);
        $this->assertTrue($rowData->isChanged());
        $changed = $rowData->fetchChanged();
        $this->assertEquals([2=>'TRTRTS', 'newnull'=>null], $changed);
        $filter = new NominateFilter($rowData->getIterator());
        $filter->nominate(array_keys($changed));
        $this->assertCount(2, $filter);
        $actual = iterator_to_array($filter);
        $this->assertEquals([2=>'TRTRTS', 'newnull'=>null], $actual);
    }

}
