<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 12.06.18
 * Time: 23:35
 */

namespace Weedus\Collection;

interface SpecificationCollectionInterface extends CollectionInterface
{
    public function findBySpecification($spec);
}