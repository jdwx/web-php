<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


class CssInline implements CssInterface {


    use ElementTrait;


    public function __construct( private readonly string $stBody ) {
        $this->setTagName( 'style' );
    }


    protected function inner() : ?string {
        return $this->stBody;
    }


}
