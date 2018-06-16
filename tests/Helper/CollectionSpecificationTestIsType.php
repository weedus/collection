<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 13.06.18
 * Time: 08:01
 */

namespace Weedus\Tests\Helper;

class CollectionSpecificationTestIsType
{
    protected $type;

    /**
     * CollectionSpecificationTestIsType constructor.
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function isSatisfiedBy($item)
    {
        return gettype($item) === $this->type;
    }
}