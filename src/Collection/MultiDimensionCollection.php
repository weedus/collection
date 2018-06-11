<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 03.06.18
 * Time: 17:28
 */

namespace Weedus\Collection;


class MultiDimensionCollection extends Collection
{
    public function offsetSet($offset, $value)
    {
        list($offset, $deeperOffset) = $this->prepareOffset($offset);
        if ($deeperOffset === null) {
            parent::offsetSet($offset, $value);
            return;
        }
        if (!parent::offsetExists($offset)) {
            parent::offsetSet($offset, new MultiDimensionCollection());
        }
//        $collection = parent::offsetGet($offset);
//        if(!($collection instanceof MultiDimensionCollection)){
//            $helper = $collection;
//            $collection = new MultiDimensionCollection();
//            $collection->offsetSet('lost',$helper);
//            parent::offsetSet($offset,$collection);
//        }
        $collection = parent::offsetGet($offset);
        /** @var MultiDimensionCollection $collection */
        $collection->offsetSet($deeperOffset, $value);
    }

    public function offsetGet($offset)
    {
        list($offset, $deeperOffset) = $this->prepareOffset($offset);
        if (!parent::offsetExists($offset) || empty(parent::offsetGet($offset))) {
            return null;
        }
        if ($deeperOffset === null || !(parent::offsetGet($offset) instanceof MultiDimensionCollection)) {
            return parent::offsetGet($offset);
        }
        /** @var MultiDimensionCollection $collection */
        $collection = parent::offsetGet($offset);
        return $collection->offsetGet($deeperOffset);
    }

    public function offsetExists($offset)
    {
        list($offset, $deeperOffset) = $this->prepareOffset($offset);
        if ($deeperOffset === null || !parent::offsetExists($offset)) {
            return parent::offsetExists($offset);
        }
        /** @var MultiDimensionCollection $collection */
        $collection = parent::offsetGet($offset);
        return $collection->offsetExists($deeperOffset);
    }

    /**
     * @param mixed $offset
     * @throws \Assert\AssertionFailedException
     */
    public function offsetUnset($offset)
    {
        list($offset, $deeperOffset) = $this->prepareOffset($offset);
        if ($deeperOffset === null) {
            parent::offsetUnset($offset);
        }
        /** @var MultiDimensionCollection $collection */
        $collection = parent::offsetGet($offset);
        $collection->offsetUnset($deeperOffset);
    }

    public function count()
    {
        $count = 0;
        foreach ($this->items as $item) {
            if ($item instanceof CollectionInterface) {
                $count += $item->count();
                continue;
            }
            $count++;
        }
        return $count;
    }


    /**
     * @param $offset
     * @return array
     */
    private function prepareOffset($offset)
    {
        $array = explode('/', $offset);
        $offset = $array[0];
        unset($array[0]);
        $deeperOffset = implode('/', $array);
        return [$offset, $deeperOffset];

    }

}