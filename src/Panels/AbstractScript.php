<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


abstract class AbstractScript implements ScriptInterface {


    use ElementTrait;


    public function __construct() {
        $this->setTagName( 'script' );
    }


    public function setAsync( bool $bAsync = true ) : void {
        $this->setAttribute( 'async', $bAsync );
    }


    public function setDefer( bool $bDefer = true ) : void {
        $this->setAttribute( 'defer', $bDefer );
    }


    protected function inner() : ?string {
        return '';
    }


}