<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


readonly class CssInline implements CssInterface {


    public function __construct( private string $stBody ) {}


    public function __toString() : string {
        return '<style>' . $this->stBody . '</style>';
    }


}
