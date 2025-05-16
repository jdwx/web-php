<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use Stringable;


class Element implements Stringable {


    use ElementTrait;


    /** @var list<string|Stringable> */
    private array $rChildren;


    /** @param list<string|Stringable>|string|Stringable $i_body */
    public function __construct( string $i_stElement = 'div', array|string|Stringable $i_body = [] ) {
        $this->setTagName( $i_stElement );
        $this->rChildren = is_array( $i_body ) ? $i_body : [ $i_body ];
    }


    public function appendChild( string|Stringable|null $i_stBody ) : static {
        if ( ! is_null( $i_stBody ) ) {
            $this->rChildren[] = $i_stBody;
        }
        return $this;
    }


    /** @return iterable<Element> */
    public function childElements() : iterable {
        foreach ( $this->rChildren as $child ) {
            if ( $child instanceof Element ) {
                yield $child;
            }
        }
    }


    /** @return iterable<string|Stringable> */
    public function children() : iterable {
        foreach ( $this->rChildren as $child ) {
            yield $child;
        }
    }


    /** @return iterable<string|Stringable> */
    public function inner() : iterable {
        return $this->rChildren;
    }


    public function nthChild( int $i_n ) : string|Stringable|null {
        return $this->rChildren[ $i_n ] ?? null;
    }


    public function nthChildElement( int $i_n ) : Element|null {
        foreach ( $this->rChildren as $child ) {
            if ( $child instanceof Element ) {
                if ( 0 === $i_n ) {
                    return $child;
                }
                $i_n--;
            }
        }
        return null;
    }


    public function prependChild( string|Stringable|null $i_stBody ) : static {
        if ( ! is_null( $i_stBody ) ) {
            array_unshift( $this->rChildren, $i_stBody );
        }
        return $this;
    }


    public function removeAllChildren() : static {
        $this->rChildren = [];
        return $this;
    }


    public function removeChildren( callable $i_fnCallback ) : static {
        $this->rChildren = array_filter( $this->rChildren,
            fn( string|Stringable $i_child ) => ! $i_fnCallback( $i_child )
        );
        return $this;
    }


    public function removeNthChild( int $i_n = 0 ) : static {
        if ( isset( $this->rChildren[ $i_n ] ) ) {
            unset( $this->rChildren[ $i_n ] );
        }
        return $this;
    }


    public function removeNthChildElement( int $i_n = 0 ) : static {
        foreach ( $this->rChildren as $i => $child ) {
            if ( $child instanceof Element ) {
                if ( 0 === $i_n ) {
                    unset( $this->rChildren[ $i ] );
                    return $this;
                }
                $i_n--;
            }
        }
        return $this;
    }


}
