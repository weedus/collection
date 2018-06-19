<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 03.06.18
 * Time: 16:22
 */

namespace Weedus\Collection;

use Weedus\Exceptions\ClassNotFoundException;
use Weedus\Exceptions\InvalidArgumentException;
use Weedus\Exceptions\NotAllowedException;

class Collection implements CollectionInterface
{
    /** @var int */
    protected $maxCount;
    /** @var array */
    protected $restrictedKeys;
    /** @var array */
    protected $supportedClasses;
    /** @var array */
    protected $items=[];

    protected $overwriteExistingItem = true;
    /**
     * Collection constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws NotAllowedException
     */
    public function offsetSet($offset, $value)
    {
        self::validateOffset($offset);
        self::validateValue($value);
        if($this->hasItem()){
            if($this->maxCount !== null && count($this->items) >= $this->maxCount){
                throw new NotAllowedException('max count reached');
            }
            if(!$this->overwriteExistingItem && $this->offsetExists($offset)){
                throw new NotAllowedException("offset '$offset' already exists");
            }
        }
        $this->items[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return mixed
     * @throws NotAllowedException
     */
    public function offsetGet($offset)
    {
        self::validateOffset($offset);
        if(!$this->offsetExists($offset)){
            throw new NotAllowedException("offset '$offset' does not exist");
        }
        return $this->items[$offset];
    }

    /**
     * @param array $array
     * @return Collection
     */
    public static function fromArray(array $array)
    {
        $res = new static();
        foreach ($array as $offset => $value) {
            $res->validateOffset($offset);
            $res->validateValue($value);
        }
        $res->items = $array;
        return $res;
    }

    /**
     * @param $offset
     * @throws NotAllowedException
     */
    protected function validateOffset($offset)
    {
        if($offset === null){
            throw new NotAllowedException('offset must not be NULL');
        }
        if (!empty($this->restrictedKeys) && !in_array($offset, $this->restrictedKeys)) {
            throw new NotAllowedException("offset '$offset' not in enum[".implode(', ', $this->restrictedKeys)."]");
        }
    }

    /**
     * @param $value
     * @throws NotAllowedException
     */
    protected function validateValue($value)
    {
        if (!empty($this->supportedClasses)) {
            if(!is_object($value) || !in_array(get_class($value), $this->supportedClasses)){
                throw new NotAllowedException('value must be instance of [' . implode(', ',$this->supportedClasses) .']');
            }
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     * @throws NotAllowedException
     */
    public function offsetExists($offset)
    {
        $this->validateOffset($offset);
        return key_exists($offset, $this->items);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }


    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return (function () {
            foreach ($this->items as $key => $value) {
                yield $key => $value;
            }
        })();
    }

    public function hasItem()
    {
        return !empty($this->items);
    }

    public function unsetAll()
    {
        $this->items = [];
    }

    public function count()
    {
        return count($this->items);
    }


    public function getMaxCount()
    {
        if ($this->hasMaxCount()) {
            return $this->maxCount;
        }
        return null;
    }

    public function hasMaxCount()
    {
        return isset($this->maxCount);
    }

    /**
     * @param $maxCount
     */
    public function setMaxCount($maxCount)
    {

        $this->maxCount = $maxCount;
    }

    /**
     * @return bool
     */
    public function isOverwriteExistingItem()
    {
        return $this->overwriteExistingItem;
    }

    /**
     * @param bool $overwriteExistingItem
     */
    public function setOverwriteExistingItem($overwriteExistingItem)
    {
        $this->overwriteExistingItem = $overwriteExistingItem;
    }


    /**
     * @param array $restrictedKeys
     */
    public function setRestrictedKeys(array $restrictedKeys)
    {
        $this->restrictedKeys = $restrictedKeys;
    }

    /**
     * @param array $supportedClasses
     * @throws ClassNotFoundException
     * @throws NotAllowedException
     */
    public function setSupportedClasses(array $supportedClasses)
    {
        foreach($supportedClasses as $class){
            if(!is_string($class)){
                throw new NotAllowedException('classname must be a string');
            }
            if(!class_exists($class)){
                throw new ClassNotFoundException($class);
            }
        }
        $this->supportedClasses = $supportedClasses;
    }


    public function getKeys()
    {
        return array_keys($this->items);
    }
}