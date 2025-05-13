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


    public function appendChild( string|Stringable $i_stBody ) : void {
        $this->rChildren[] = $i_stBody;
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


    public function prependChild( string|Stringable $i_stBody ) : void {
        array_unshift( $this->rChildren, $i_stBody );
    }


}
