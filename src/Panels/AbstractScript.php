<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


abstract class AbstractScript implements ScriptInterface {


    private bool $bAsync = false;

    private bool $bDefer = false;

    private ?string $nstSrc = null;


    public function __toString() : string {
        $st = '<script';
        if ( $this->bAsync ) {
            $st .= ' async';
        }
        if ( $this->bDefer ) {
            $st .= ' defer';
        }
        if ( is_string( $this->nstSrc ) ) {
            $st .= " src=\"{$this->nstSrc}\"";
        }
        $st .= '>';
        $st .= $this->inner();
        $st .= '</script>';
        return $st;
    }


    public function setAsync( bool $bAsync = true ) : void {
        $this->bAsync = $bAsync;
    }


    public function setDefer( bool $bDefer = true ) : void {
        $this->bDefer = $bDefer;
    }


    public function setSrc( string $stSrc ) : void {
        $this->nstSrc = $stSrc;
    }


    protected function inner() : ?string {
        return '';
    }


}