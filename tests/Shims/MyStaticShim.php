<?php


declare( strict_types = 1 );


namespace Shims;


use JDWX\Web\Framework\StaticShim;


class MyStaticShim extends StaticShim {


    public function getAuthoritative() : bool {
        return $this->bAuthoritative;
    }


    public function setAuthoritative( bool $b ) : void {
        $this->bAuthoritative = $b;
    }


}
