<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


use Stringable;


trait StringableListTrait {


    /** @var list<string|Stringable> */
    private array $rChildren = [];


    /**
     * @param iterable<string|Stringable|iterable<string|Stringable|null>|null>|string|Stringable|null ...$i_children
     * @noinspection PhpDocSignatureInspection
     * @suppress PhanTypeMismatchReturn
     */
    public function append( iterable|string|Stringable|null ...$i_children ) : static {
        foreach ( $i_children as $child ) {
            $this->appendOne( $child );
        }
        return $this;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function appendChild( string|Stringable|null $i_child ) : static {
        if ( ! is_null( $i_child ) ) {
            $this->rChildren[] = $i_child;
        }
        return $this;
    }


    /** @return iterable<string|Stringable> */
    public function children( ?callable $i_fnFilter = null ) : iterable {
        foreach ( $this->rChildren as $child ) {
            if ( ! $i_fnFilter || $i_fnFilter( $child ) ) {
                yield $child;
            }
        }
    }


    public function countChildren() : int {
        return count( $this->rChildren );
    }


    public function hasChildren() : bool {
        return 0 < $this->countChildren();
    }


    public function nthChild( int $i_n ) : string|Stringable|null {
        return $this->rChildren[ $i_n ] ?? null;
    }


    /**
     * There is no prepend() method because it's ambiguous whether
     * prepending multiple elements at the same time should
     * prepend them as a group or individually (the latter reversing
     * the order).
     *
     * @suppress PhanTypeMismatchReturn
     */
    public function prependChild( string|Stringable|null $i_child ) : static {
        if ( ! is_null( $i_child ) ) {
            array_unshift( $this->rChildren, $i_child );
        }
        return $this;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function removeAllChildren() : static {
        $this->rChildren = [];
        return $this;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function removeChild( string|Stringable $i_child ) : static {
        foreach ( $this->rChildren as $i => $child ) {
            if ( $child === $i_child ) {
                unset( $this->rChildren[ $i ] );
                return $this;
            }
        }
        return $this;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function removeChildren( callable $i_fnCallback ) : static {
        $this->rChildren = array_values( array_filter( $this->rChildren,
            fn( string|Stringable $i_child ) => ! $i_fnCallback( $i_child )
        ) );
        return $this;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function removeNthChild( int $i_n = 0 ) : static {
        if ( isset( $this->rChildren[ $i_n ] ) ) {
            unset( $this->rChildren[ $i_n ] );
        }
        return $this;
    }


    /**
     * @param iterable<string|Stringable|iterable<string|Stringable|null>|null>|string|Stringable|null $i_child
     * @suppress PhanTypeMismatchReturn
     */
    protected function appendOne( iterable|string|Stringable|null $i_child ) : static {
        if ( is_iterable( $i_child ) ) {
            return $this->append( ... $i_child );
        }
        return $this->appendChild( $i_child );
    }


}