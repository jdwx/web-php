<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


use Stringable;
use Traversable;


/**
 * @extends \IteratorAggregate<string|Stringable>
 * @suppress PhanAccessWrongInheritanceCategoryInternal
 */
interface StreamInterface extends \IteratorAggregate {


    /** @return list<string|Stringable> */
    public function asArray() : array;


    /** @return Traversable<string|Stringable> */
    public function getIterator() : Traversable;


    /** @return iterable<string|Stringable> */
    public function stream() : iterable;


}