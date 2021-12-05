<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Model\RowData\RowDataInterface;
use Model\RowData\RowData;

/**
 * Description of RowDataTest
 *
 * @author pes2704
 */
class RowDataTest extends TestCase {

    public static function mock() {
    }

    public static function setUpBeforeClass(): void {
    }

    protected function setUp(): void {

    }

    public function testConstructWithNoData() {
        $rowData = new RowData();
        $this->assertInstanceOf(RowData::class, $rowData);
        $this->assertInstanceOf(RowDataInterface::class, $rowData);
    }

    public function testConstructWithArray() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertInstanceOf(RowData::class, $rowData);
        $this->assertInstanceOf(RowDataInterface::class, $rowData);
    }

    public function testOffsetSetGet() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertTrue($rowData->offsetExists(1));
        $this->assertIsString($rowData->offsetGet(1));
        $this->assertTrue($rowData->offsetExists('abcd'));
        $this->assertInstanceOf(\stdClass::class, $rowData->offsetGet('abcd'));
        $rowData->offsetUnset('abcd');
        $this->assertFalse($rowData->offsetExists('abcd'));
        $rowData->offsetSet('efgh', new \stdClass());
        $this->assertTrue($rowData->offsetExists('efgh'));
        $this->assertInstanceOf(\stdClass::class, $rowData->offsetGet('efgh'));
        $this->assertTrue($rowData->offsetExists('null'));
        $this->assertNull($rowData->offsetGet('null'));
    }

    public function testNoChange() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet(1, 'TRTRTS');
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet('null', null);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet('abcd', $rowData->offsetGet('abcd'));
        $this->assertFalse($rowData->isChanged());
        $changed = $rowData->changedNames();
        $this->assertCount(0, $changed);
    }

    public function testAdd() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet(1, 'TRTRTS');
        $this->assertFalse($rowData->isChanged());

        $rowData->offsetSet(2, 'TRTRTS');
        $rowData->offsetSet('newnull', null);
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals([2, 'newnull'], $rowData->changedNames());
//        $this->assertEquals([2=>'TRTRTS', 'newnull'=>null], $rowData->fetchChanged()->getArrayCopy());
    }

    public function testScalarValueChange() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet(1, 'TRTRTS1');
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals([1], $rowData->changedNames());
//        $this->assertEquals([1=>'TRTRTS1'], $rowData->fetchChanged()->getArrayCopy());
        //opakovaná změna dat
        $rowData->offsetSet(1, 'TRTRTS2');
        $this->assertTrue($rowData->isChanged());
        $rowData->offsetSet(1, 'TRTRTS3');
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals([1], $rowData->changedNames());
//        $this->assertEquals([1=>'TRTRTS3'], $rowData->fetchChanged()->getArrayCopy());
    }

    public function testObjectExchange() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $object = new \stdClass();
        $rowData->offsetSet('abcd', $object);
        $this->assertTrue($rowData->isChanged());
        $changed = $rowData->changedNames();
        $this->assertCount(1, $changed);
        $this->assertEquals(['abcd'], $changed);
        $this->assertTrue($rowData->offsetExists('abcd'));
        $this->assertInstanceOf(\stdClass::class, $rowData->offsetGet('abcd'));
        $this->assertTrue($rowData->offsetExists($changed[0]));
        $this->assertInstanceOf(\stdClass::class, $rowData->offsetGet($changed[0]));
    }

    public function testObjectAttributeChange() {
        $object = new \stdClass();
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>$object]);
        $this->assertFalse($rowData->isChanged());
        $object->new = 'UZUZUUZ';
        $rowData->offsetSet('abcd', $object);
        $this->assertFalse($rowData->isChanged());
        $this->assertCount(0, $rowData->changedNames());
    }

    public function testNullToNotnullChange() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet('null', 'not null');
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals(['null'], $rowData->changedNames());
//        $this->assertEquals(['null'=>'not null'], $rowData->fetchChanged()->getArrayCopy());
    }

    public function testNotnullToNullChange() {
        $rowData = new RowData(['a', null, 'null'=>'not null', 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet('null', null);
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals(['null'], $rowData->changedNames());
//        $this->assertEquals(['null'=>null], $rowData->fetchChanged()->getArrayCopy());
    }

    public function testFetchChanged() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet(1, 'TRTRTS');
        $this->assertFalse($rowData->isChanged());
        $this->assertCount(0, $rowData->changedNames());
        $rowData->offsetSet(2, 'TRTRTS');
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals([2=>'TRTRTS'], $rowData->yieldChanged()->getArrayCopy());
        $object = new \stdClass();
        $rowData->offsetSet('abcd', $object);
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals([2=>'TRTRTS', 'abcd'=>$object], $rowData->yieldChanged()->getArrayCopy());
        $this->assertCount(2, $rowData->changedNames());

    }
}
