<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use Ds\Set;
use JDWX\Web\Pages\PageInterface;


abstract readonly class AbstractResponse implements ResponseInterface {


    /** @var Set<string> */
    private Set $setHeaders;


    /** @param ?iterable<string> $i_rHeaders */
    public function __construct( private PageInterface $page, private int $uStatusCode = 200,
                                 ?iterable             $i_rHeaders = null ) {
        $this->setHeaders = new Set( $i_rHeaders ?? [] );
    }


    public function __toString() : string {
        return strval( $this->page );
    }


    public function getHeader( string $i_stHeaderName ) : ?string {
        $i_stHeaderName = strtolower( $i_stHeaderName );
        foreach ( $this->setHeaders as $stHeader ) {
            $stCheck = strtolower( $stHeader );
            if ( str_starts_with( $stCheck, $i_stHeaderName . ':' ) ) {
                return trim( substr( $stHeader, strlen( $i_stHeaderName ) + 1 ) );
            }
        }
        return null;
    }


    /** @return Set<string> */
    public function getHeaders() : Set {
        return $this->setHeaders;
    }


    public function getPage() : PageInterface {
        return $this->page;
    }


    public function getStatusCode() : int {
        return $this->uStatusCode;
    }


}
