<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 12.06.18
 * Time: 23:35
 */

namespace Weedus\Collection;


use Weedus\Specification\SpecificationInterface;

interface SpecificationCollectionInterface extends CollectionInterface
{
    public function findBySpecification(SpecificationInterface $spec);
}