<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


class SimplePanel extends AbstractBodyPanel {


    /** @var list<string> */
    private array $rBody;


    /** @param string|list<string> $i_body */
    public function __construct( array|string $i_body = [] ) {
        if ( is_string( $i_body ) ) {
            $i_body = [ $i_body ];
        }
        $this->rBody = $i_body;
    }


    public function addBody( string $i_stBody ) : void {
        $this->rBody[] = $i_stBody;
    }


    public function body() : iterable|string {
        return $this->rBody;
    }


}