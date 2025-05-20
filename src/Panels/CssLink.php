<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


readonly class CssLink implements CssInterface {


    public function __construct( private string $stUri ) {}


    public function __toString() : string {
        return "<link href=\"{$this->stUri}\" rel=\"stylesheet\">";
    }


}
