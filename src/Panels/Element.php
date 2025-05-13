<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use Stringable;


class Element implements Stringable {


    use ElementTrait;


    /** @var list<string|Stringable> */
    private array $rBody;


    /** @param list<string|Stringable>|string|Stringable $i_body */
    public function __construct( string $i_stElement = 'div', array|string|Stringable $i_body = [] ) {
        $this->setTagName( $i_stElement );
        $this->rBody = is_array( $i_body ) ? $i_body : [ $i_body ];
    }


    public function appendChild( string|Stringable $i_stBody ) : void {
        $this->rBody[] = $i_stBody;
    }


    /** @return iterable<string|Stringable> */
    public function inner() : iterable {
        return $this->rBody;
    }


    public function prependChild( string|Stringable $i_stBody ) : void {
        array_unshift( $this->rBody, $i_stBody );
    }


}
