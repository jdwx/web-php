<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use Stringable;


trait HeaderListTrait {


    /** @var list<string|Stringable> */
    private array $rHeaders = [];


    public function addHeader( string|Stringable $i_stHeader, string|Stringable|null $i_nstValue = null ) : void {
        if ( ! is_null( $i_nstValue ) ) {
            $i_stHeader = "{$i_stHeader}: {$i_nstValue}";
        }
        $this->rHeaders[] = $i_stHeader;
    }


    /** @return iterable<string|Stringable> */
    public function headerList() : iterable {
        return $this->rHeaders;
    }


}