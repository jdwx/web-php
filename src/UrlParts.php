<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use Stringable;


/** @implements \ArrayAccess<string, string|list<string>> */
class UrlParts implements \ArrayAccess, Stringable {


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

    public ?string $nstFragment = null;


    public function __toString() : string {
        $st = '';
        if ( is_string( $this->nstScheme ) ) {
            $st .= $this->nstScheme . ':';
        }
        if ( is_string( $this->nstHost ) || is_string( $this->nstUser ) ) {
            $st .= '//';
        }
        if ( is_string( $this->nstUser ) ) {
            $st .= $this->nstUser;
            if ( is_string( $this->nstPassword ) ) {
                $st .= ':' . $this->nstPassword;
            }
            $st .= '@';
        }
        if ( is_string( $this->nstHost ) ) {
            $st .= $this->nstHost;
            if ( is_int( $this->nuPort ) ) {
                $st .= ':' . $this->nuPort;
            }
        }
        $st .= '/';
        if ( ! empty( $this->subFolders ) ) {
            $st .= join( '/', $this->subFolders ) . '/';
        }
        if ( is_string( $this->nstFile ) ) {
            $st .= $this->nstFile;
        }
        if ( ! empty( $this->rQuery ) ) {
            $st .= '?' . http_build_query( $this->rQuery );
        }
        if ( is_string( $this->nstFragment ) ) {
            $st .= '#' . $this->nstFragment;
        }
        return $st;
    }


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


    public function parent() : static {
        $parent = clone $this;
        $parent->nstFile = array_pop( $parent->subFolders );
        $parent->nstFragment = null;
        $parent->rQuery = [];
        return $parent;
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


    /**
     * Generates a new UrlParts object that is just the path component
     * of the current one.
     */
    public function pathOnly() : static {
        /** @phpstan-ignore new.static */
        $path = new static();
        $path->subFolders = $this->subFolders;
        $path->nstFile = $this->nstFile;
        return $path;
    }


    public function validate() : bool {
        foreach ( $this->subFolders as $part ) {
            if ( ! Url::validatePathSegment( $part ) ) {
                return false;
            }
        }

        if ( ! Url::validatePathSegment( $this->nstFile ) ) {
            return false;
        }

        return true;

    }


}
