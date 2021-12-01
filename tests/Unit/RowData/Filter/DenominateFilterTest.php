<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Model\RowData\Filter\DenominateFilter;
use Model\RowData\Filter\DenominateFilterInterface;

/**
 * Description of NominateFilterTest
 *
 * @author pes2704
 */
class DenominateFilterTest extends TestCase {

    public static function mock() {
    }

    public static function setUpBeforeClass(): void {
    }

    protected function setUp(): void {

    }

    public function testConstruct() {
        $filter = new DenominateFilter(new \ArrayIterator([1,2,3]));
        $this->assertInstanceOf(DenominateFilter::class, $filter);
        $this->assertInstanceOf(DenominateFilterInterface::class, $filter);
    }

    public function testFull() {
        $filter = new DenominateFilter(new \ArrayIterator([1,2,3]));
        $this->assertCount(3, $filter);
        $result = iterator_to_array($filter);
        $this->assertCount(3, $result);
    }


    public function testFilter() {
        $filter = new DenominateFilter(new \ArrayIterator(['a'=>1,'b'=>2,'c'=>3, 'd'=>4, 'e'=>5]));
        $this->assertCount(5, $filter);
        $filter->denominate(['b', 'e']);
        $this->assertCount(3, $filter);
        $result = iterator_to_array($filter);
        $this->assertCount(3, $result);
        $this->assertEquals(['a'=>1, 'c'=>3, 'd'=>4], $result);
    }


}
