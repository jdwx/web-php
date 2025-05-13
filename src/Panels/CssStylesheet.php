<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


class CssStylesheet implements CssInterface {


    use ElementTrait;


    public function __construct( string $i_stUri ) {
        $this->setTagName( 'link' );
        $this->setAlwaysClose( false );
        $this->addAttribute( 'rel', 'stylesheet' );
        $this->addAttribute( 'href', $i_stUri );
    }


}
