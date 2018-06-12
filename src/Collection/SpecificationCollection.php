<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 12.06.18
 * Time: 23:34
 */

namespace Weedus\Collection;


use Weedus\Specification\SpecificationInterface;

class SpecificationCollection extends Collection implements SpecificationCollectionInterface
{
    public function findBySpecification(SpecificationInterface $spec)
    {
        $items = [];
        foreach($this as $item){
            if($spec->isSatisfiedBy($item)){
                $items[] = $item;
            }
        }
        return $items;
    }
}