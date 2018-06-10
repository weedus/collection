<?php
namespace Weedus\Tests;

use Weedus\CollectionInterface;
use Weedus\MultiDimensionCollection;

class MultiDimensionCollectionTest extends \Codeception\Test\Unit
{
    /** @var CollectionInterface */
    private $collection;

    protected function _before()
    {
        $this->collection = new MultiDimensionCollection();
    }

    protected function _after()
    {
    }

    // tests
    public function testCreation()
    {
        $this->assertInstanceOf(CollectionInterface::class,$this->collection);
        $this->assertInstanceOf(MultiDimensionCollection::class,$this->collection);
    }

    public function testSomething()
    {
        $this->assertTrue(false);
    }
}