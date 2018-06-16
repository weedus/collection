# collection
This is an easy storage object.

Implements Interfaces to be used like an Array.

You can optionally set MaxCount, allowed Keys or Classes.

SpecificationCollection additionally grants the possibility to search Items by inserting an Specification.
Following the specification pattern the inserted Object must have a method called 'isSatisfiedBy'.
No Interface or Class check here, feel free to use whatever implementation you like most. 

Usage Example
-------------
Collection
``` php
<?php

    // Example#1
    $collection = new Collection();
    
    // optional
    $collection->setMaxCount(5);
    //optional
    $collection->setRestrictedKeys(['foo','bar']);
    //optional
    $collection->setSupportedClasses([Foo::class,Bar::class]);
    
    $collection->offsetSet('foo', new Foo());
    $collection->offsetSet('bar', new Bar());
    
    $foo = $collection->offsetGet('foo'); // $foo instanceof Foo
    $bar = $collection->offsetGet('bar'); // $bar instanceof Bar
        
    $collection->count(); // 2
    $collection->offsetUnset('foo');
    $collection->count(); // 1
    
    
    // Example#2
    $collection2 = Collection::fromArray(['foo' => new Foo(), 'bar' => new Bar()]);
    $collection2->count(); // 2
    
    foreach($collection2 as $key => $value){
        ...
    }
    
    $foo = $collection['foo']; // $foo instanceof Foo
    
    $collection2->unsetAll();
    empty($collection2); // true
?>
```
SpecificationCollection (extends Collection)

not bound to specific specification implementation.
given specification must implement method 'isSatisfiedBy'
``` php
<?php
    $specificationCollection = SpecificationCollection::fromArray(['bla',1,new \stdClass()]);
    $item = $specificationCollection->findBySpecification(new CollectionSpecificationTestIsType('string')); 
    // $item = ['bla']  
?>
```    