<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 12.06.18
 * Time: 23:34
 */

namespace Weedus\Collection;


use Assert\Assertion;

class SpecificationCollection extends Collection implements SpecificationCollectionInterface
{
    public static function fromArray(array $array)
    {
        $res = new self();
        foreach ($array as $offset => $value) {
            $res->validateOffset($offset);
            $res->validateValue($value);
        }
        $res->items = $array;
        return $res;
    }

    public function findBySpecification($spec)
    {
        Assertion::methodExists('isSatisfiedBy',$spec);

        $items = [];
        foreach($this as $item){
            if($spec->isSatisfiedBy($item)){
                $items[] = $item;
            }
        }
        return $items;
    }
}