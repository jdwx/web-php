<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use Stringable;


class SimplePanel extends AbstractBodyPanel {


    /** @var list<string|Stringable> */
    private array $rBody;


    /** @param list<string|Stringable>|string|Stringable $i_body */
    public function __construct( array|string|Stringable $i_body = [] ) {
        if ( is_string( $i_body ) ) {
            $i_body = [ $i_body ];
        }
        $this->rBody = $i_body;
    }


    public function addBody( string|Stringable $i_stBody ) : void {
        $this->rBody[] = $i_stBody;
    }


    /** @return iterable<string|Stringable>|string|Stringable */
    public function body() : iterable|string|Stringable {
        return $this->rBody;
    }


}