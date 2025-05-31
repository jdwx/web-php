<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


use Stringable;


interface StringableListInterface extends Stringable {


    /**
     * @param iterable<string|Stringable|iterable<string|Stringable|null>|null>|string|Stringable|null ...$i_children
     * @noinspection PhpDocSignatureInspection
     * @suppress PhanTypeMismatchReturn
     */
    public function append( iterable|string|Stringable|null ...$i_children ) : static;


    public function appendChild( string|Stringable|null $i_child ) : static;


    /** @return iterable<string|Stringable> */
    public function children( ?callable $i_fnFilter = null ) : iterable;


    public function countChildren() : int;


    public function hasChildren() : bool;


    public function nthChild( int $i_n ) : string|Stringable|null;


    public function prependChild( string|Stringable|null $i_child ) : static;


    public function removeAllChildren() : static;


    public function removeChild( string|Stringable $i_child ) : static;


    public function removeChildren( callable $i_fnCallback ) : static;


    public function removeNthChild( int $i_n = 0 ) : static;


}