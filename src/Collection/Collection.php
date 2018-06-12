<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 03.06.18
 * Time: 16:22
 */

namespace Weedus\Collection;


use Assert\Assertion;
use Weedus\Specification\SpecificationInterface;

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
     * @throws \Assert\AssertionFailedException
     */
    public function offsetSet($offset, $value)
    {
        self::validateOffset($offset);
        self::validateValue($value);
        if($this->hasItem()){
            if($this->maxCount !== null){
                Assertion::lessThan(count($this->items), $this->maxCount,'max count reached');
            }
            if(!$this->overwriteExistingItem){
                Assertion::false($this->offsetExists($offset),'offset already exists');
            }
        }
        $this->items[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return mixed
     * @throws \Assert\AssertionFailedException
     */
    public function offsetGet($offset)
    {
        self::validateOffset($offset);
        Assertion::true($this->offsetExists($offset),'offset not found');
        return $this->items[$offset];
    }

    /**
     * @param array $array
     * @return Collection
     * @throws \Assert\AssertionFailedException
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
     * @throws \Assert\AssertionFailedException
     */
    protected function validateOffset($offset)
    {
        Assertion::notEmpty($offset,'offset must not be empty');
        if (!empty($this->restrictedKeys)) {
            Assertion::inArray($offset, $this->restrictedKeys, 'offset not in enum [' . implode(',', $this->restrictedKeys) . ']');
        }
    }

    /**
     * @param $value
     */
    protected function validateValue($value)
    {
        if (!empty($this->supportedClasses)) {
            Assertion::isObject($value);
            Assertion::inArray(
                get_class($value),
                $this->supportedClasses,
                'must be instance of [' . implode(', ',$this->supportedClasses) .']'
            );
        }
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     * @throws \Assert\AssertionFailedException
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
     * @throws \Assert\AssertionFailedException
     */
    public function setMaxCount($maxCount)
    {
        Assertion::integer($maxCount);
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
     */
    public function setSupportedClasses(array $supportedClasses)
    {
        Assertion::allString($supportedClasses);
        Assertion::allClassExists($supportedClasses);
        $this->supportedClasses = $supportedClasses;
    }


    public function getKeys()
    {
        return array_keys($this->items);
    }
}