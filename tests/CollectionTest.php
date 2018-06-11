<?php
namespace Weedus\Tests;

use Weedus\Collection\Collection;
use Weedus\Collection\CollectionInterface;
use Weedus\Tests\Helper\Test1;
use Weedus\Tests\Helper\Test2;
use Weedus\Tests\Helper\Test3;
use Weedus\Tests\Helper\Test3x;

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
        if(!empty($this->log)) var_dump($this->log);
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
            $this->assertContains('offset must not be empty',$exception->getMessage());
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
        $this->log('collection',$this>$this->collection);
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
        $this->collection->setSupportedClasses([Test1::class,Test3::class]);
        $this->collection->offsetSet('bla',new Test1());
        $this->assertTrue($this->collection->hasItem());
        try{
            $this->collection->offsetSet('one',new Test2());
        }catch(\Exception $exception){
            $this->assertContains('must be instance of',$exception->getMessage());
        }
        try{
            $this->collection->offsetSet('blubb',new Test3x());
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
    }


    private function log($key, $item){
        $this->log[$key] = $item;
    }
}