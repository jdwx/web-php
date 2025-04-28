<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


trait ElementTrait {


    use AttributeTrait;


    private string $stTagName;


    public function __toString() : string {
        $st = '<' . $this->getTagName() . $this->attributeString() . '>';
        $nst = $this->inner();
        if ( is_string( $nst ) ) {
            $st .= $nst;
            $st .= '</' . $this->stTagName . '>';
        }
        return $st;
    }


    public function getTagName() : string {
        return $this->stTagName;
    }


    public function setTagName( string $i_stTagName ) : void {
        $this->stTagName = $i_stTagName;
    }


    protected function inner() : ?string {
        return null;
    }


}