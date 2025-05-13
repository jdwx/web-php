<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


class SimpleElementPanel extends ElementPanel {


    /** @var list<string> */
    private array $rBody;


    /** @param list<string>|string $i_body */
    public function __construct( string $stElement = 'div', array|string $i_body = [] ) {
        parent::__construct( $stElement );
        if ( is_string( $i_body ) ) {
            $i_body = [ $i_body ];
        }
        $this->rBody = $i_body;
    }


    public function addBody( string $stBody ) : void {
        $this->rBody[] = $stBody;
    }


    /**
     * @inheritDoc
     */
    protected function innerBody() : iterable|string {
        return $this->rBody;
    }


}
