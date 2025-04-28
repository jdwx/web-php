<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


trait HeaderListTrait {


    /** @var list<string> */
    private array $rHeaders = [];


    public function addHeader( string $i_stHeader, ?string $i_nstValue = null ) : void {
        if ( is_string( $i_nstValue ) ) {
            $i_stHeader .= ': ' . $i_nstValue;
        }
        $this->rHeaders[] = $i_stHeader;
    }


    /** @return iterable<string> */
    public function headerList() : iterable {
        return $this->rHeaders;
    }


}