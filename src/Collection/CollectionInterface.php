<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 03.06.18
 * Time: 16:23
 */

namespace Weedus\Collection;


use Weedus\Specification\SpecificationInterface;

interface CollectionInterface extends \ArrayAccess, \IteratorAggregate, \Countable
{
    public static function fromArray(array $array);

    public function hasItem();

    public function unsetAll();

    public function count();

    public function getMaxCount();

    public function hasMaxCount();

    public function setMaxCount($maxCount);

    public function setRestrictedKeys(array $keys);

    public function setSupportedClasses(array $classes);

    public function getKeys();

    public function setOverwriteExistingItem($overwriteExistingItem);

    public function isOverwriteExistingItem();

    public function findBySpecification(SpecificationInterface $spec);
}