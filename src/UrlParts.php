<?php


declare( strict_types = 1 );


namespace JDWX\Web;


/** @implements \ArrayAccess<string, string|list<string>> */
class UrlParts implements \ArrayAccess {


    public ?string $nstScheme = null;

    public ?string $nstHost = null;

    public ?int $nuPort = null;

    public ?string $nstUser = null;

    public ?string $nstPassword = null;

    /** @var list<string> */
    public array $subFolders = [];

    public ?string $nstFile = null;

    /** @var array<string, string|list<string>> */
    public array $rQuery = [];


    /** @param ?string $offset */
    public function offsetExists( mixed $offset ) : bool {
        return isset( $this->rQuery[ $offset ] );
    }


    /**
     * @param string $offset
     * @return string|list<string>
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function offsetGet( mixed $offset ) : string|array {
        return $this->rQuery[ $offset ];
    }


    public function offsetSet( mixed $offset, mixed $value ) : void {
        throw new \LogicException( 'UriParts does not support setting parameters' );
    }


    public function offsetUnset( mixed $offset ) : void {
        throw new \LogicException( 'UriParts does not support unsetting parameters' );
    }


    public function path() : string {
        $stPath = '/';
        if ( count( $this->subFolders ) > 0 ) {
            $stPath .= implode( '/', $this->subFolders ) . '/';
        }
        if ( $this->nstFile ) {
            $stPath .= $this->nstFile;
        }
        return $stPath;

    }


}
