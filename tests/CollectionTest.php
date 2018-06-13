<?php
namespace Weedus\Tests;

use Weedus\Collection\Collection;
use Weedus\Collection\CollectionInterface;
use Weedus\Collection\SpecificationCollection;
use Weedus\Specification\IsType;
use Weedus\Tests\Helper\CollectionTest1;
use Weedus\Tests\Helper\CollectionTest2;
use Weedus\Tests\Helper\CollectionTest3;
use Weedus\Tests\Helper\CollectionTest3X;

class CollectionTest extends \Codeception\Test\Unit
{
    /** @var array */
    private $log;
    /** @var CollectionInterface */
    private $collection;

    /** @var array */
    private $items;

    protected function _before()
    {
        $this->collection = new Collection();
        $this->items = [
            'object' => new \stdClass(),
            'string' => 'string',
            'integer' => 13,
            'double' => 10.000123
        ];
    }

    protected function _after()
    {
    }

    // tests
    public function testCreation()
    {
        $this->assertInstanceOf(CollectionInterface::class,$this->collection);
        $this->assertInstanceOf(Collection::class,$this->collection);
        $this->assertFalse($this->collection->hasItem());

    }

    public function testCreationFromArray()
    {
        $collection = Collection::fromArray($this->items);
        $this->assertCount(count($this->items),$collection);
    }

    public function testStoringItemsFails()
    {
        $this->assertFalse($this->collection->hasItem());
        try{
            $this->collection->offsetSet(null,'bla');
        }catch(\Exception $exception){
            $this->assertContains('offset must not be NULL',$exception->getMessage());
        }
        $this->assertFalse($this->collection->hasItem());

        $this->collection->setMaxCount(1);
        $this->collection->offsetSet('one','asd');
        try{
            $this->collection->offsetSet('two','asd');
        }catch(\Exception $exception){
            $this->assertContains('max count reached',$exception->getMessage());
        }
        $this->assertTrue($this->collection->hasItem());
        $this->assertCount(1,$this->collection);
    }

    public function testStoringItems()
    {
        $this->assertFalse($this->collection->hasItem());

        foreach($this->items as $key => $item){
            $this->collection->offsetSet($key,$item);
        }

        $this->assertTrue($this->collection->hasItem());
        $this->assertCount($this->collection->count(),$this->items);

        $this->assertEquals('string', $this->collection->offsetGet('string'));
        $this->collection->offsetSet('string','not a string');
        $this->assertEquals('not a string', $this->collection->offsetGet('string'));
        $this->collection->setOverwriteExistingItem(false);
        try{
            $this->collection->offsetSet('string','not a string');
        }catch(\Exception $exception){
            $this->assertContains('offset already exists',$exception->getMessage());
        }
        $this->collection->setOverwriteExistingItem(true);
        $this->collection->offsetSet('string','string');
        $this->assertEquals('string', $this->collection->offsetGet('string'));
    }


    public function testRestrictedKeys()
    {
        $this->collection->setRestrictedKeys(['bla']);
        try{
            $this->collection->offsetSet('hallo','asd');
        }catch(\Exception $exception){
            $this->assertContains('offset not in enum',$exception->getMessage());
        }
        $this->collection->offsetSet('bla','asd');
        $this->assertTrue($this->collection->hasItem());
    }

    public function testRestrictedClasses()
    {
        $this->collection->setSupportedClasses([CollectionTest1::class,CollectionTest3::class]);
        $this->collection->offsetSet('bla',new CollectionTest1());
        $this->assertTrue($this->collection->hasItem());
        try{
            $this->collection->offsetSet('one',new CollectionTest2());
        }catch(\Exception $exception){
            $this->assertContains('must be instance of',$exception->getMessage());
        }
        try{
            $this->collection->offsetSet('blubb',new CollectionTest3X());
        }catch(\Exception $exception){
            $this->assertContains('must be instance of',$exception->getMessage());
        }

    }


    /**
     * @depends testStoringItems
     */
    public function testReceivingItems()
    {
        $this->testStoringItems();

        $this->assertFalse($this->collection->offsetExists('bla'));
        try{
            $item = $this->collection->offsetGet('bla');
        }catch(\Exception $exception){
            $this->assertContains('offset not found',$exception->getMessage());
        }
        foreach($this->items as $offset => $item){
            $this->assertTrue($this->collection->offsetExists($offset));
            $this->assertEquals($item, $this->collection->offsetGet($offset));
        }
        foreach($this->collection as $offset => $item){
            $this->assertArrayHasKey($offset,$this->items);
            $this->assertEquals($item, $this->items[$offset]);
        }
        $this->assertEquals($this->items['string'],$this->collection['string']);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testSpecificationCollection()
    {
        $specificationCollection = SpecificationCollection::fromArray(['bla',1,new \stdClass()]);
        $item = $specificationCollection->findBySpecification(new IsType('string'));
        $this->assertEquals(1, count($item));
        $this->assertEquals('bla', $item[0]);
    }
}