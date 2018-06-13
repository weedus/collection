<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 13.06.18
 * Time: 08:01
 */

namespace Weedus\Tests\Helper;


use Assert\Assertion;

class CollectionSpecificationTestIsType
{
    protected $type;

    /**
     * IsType constructor.
     * @param $type
     * @throws \Assert\AssertionFailedException
     */
    public function __construct($type)
    {
        Assertion::string($type);
        Assertion::notEq($type, 'float', 'float not possible');
        $this->type = $type;
    }

    public function isSatisfiedBy($item)
    {
        return gettype($item) === $this->type;
    }
}