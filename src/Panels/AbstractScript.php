<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


class AbstractScript implements ScriptInterface {


    private bool $bAsync = false;

    private bool $bDefer = false;


    public function __toString() : string {
        $st = '<script';
        $rAttrs = iterator_to_array( $this->attrs() );
        ksort( $rAttrs );
        foreach ( $rAttrs as $stKey => $nstValue ) {
            $st .= ' ' . $stKey;
            if ( is_string( $nstValue ) ) {
                $st .= '="' . htmlspecialchars( $nstValue, ENT_QUOTES ) . '"';
            }
        }
        $st .= '>' . $this->body() . '</script>';
        return $st;
    }


    public function setAsync( bool $bAsync = true ) : void {
        $this->bAsync = $bAsync;
    }


    public function setDefer( bool $bDefer = true ) : void {
        $this->bDefer = $bDefer;
    }


    /** @return iterable<string, ?string> */
    protected function attrs() : iterable {
        if ( $this->bAsync ) {
            yield 'async' => null;
        }
        if ( $this->bDefer ) {
            yield 'defer' => null;
        }
    }


    protected function body() : string {
        return '';
    }


}