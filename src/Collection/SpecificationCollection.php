<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 12.06.18
 * Time: 23:34
 */

namespace Weedus\Collection;

use Weedus\Exceptions\MethodNotFoundException;

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

    /**
     * @param $spec
     * @return array
     * @throws MethodNotFoundException
     */
    public function findBySpecification($spec)
    {
        if(!method_exists($spec,'isSatisfiedBy')){
            throw new MethodNotFoundException('isSatisfiedBy()');
        }

        $items = [];
        foreach($this as $item){
            if($spec->isSatisfiedBy($item)){
                $items[] = $item;
            }
        }
        return $items;
    }
}