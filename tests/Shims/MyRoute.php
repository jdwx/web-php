<?php


declare( strict_types = 1 );


namespace Shims;


use Ds\Map;
use JDWX\Web\Framework\AbstractRoute;
use JDWX\Web\Framework\ResponseInterface;
use JDWX\Web\Framework\RouterInterface;
use Psr\Log\LoggerInterface;


class MyRoute extends AbstractRoute {


    /** @var Map<string, callable> */
    private Map $mapCallbacks;


    /** @param iterable<string, callable> $i_rCallbacks */
    public function __construct( RouterInterface $router, iterable $i_rCallbacks = [] ) {
        parent::__construct( $router );
        $this->mapCallbacks = new Map( $i_rCallbacks );
    }


    protected function handleDELETE( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'delete' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'delete' ], $i_stUri, $i_stPath );
        }
        return parent::handleDELETE( $i_stUri, $i_stPath );
    }


    protected function handleGET( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'get' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'get' ], $i_stUri, $i_stPath );
        }
        return parent::handleGET( $i_stUri, $i_stPath );
    }


    protected function handleHEAD( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'head' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'head' ], $i_stUri, $i_stPath );
        }
        return parent::handleHEAD( $i_stUri, $i_stPath );
    }


    protected function handlePATCH( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'patch' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'patch' ], $i_stUri, $i_stPath );
        }
        return parent::handlePATCH( $i_stUri, $i_stPath );
    }


    protected function handlePOST( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'post' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'post' ], $i_stUri, $i_stPath );
        }
        return parent::handlePOST( $i_stUri, $i_stPath );
    }


    protected function handlePUT( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'put' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'put' ], $i_stUri, $i_stPath );
        }
        return parent::handlePUT( $i_stUri, $i_stPath );
    }


    public function loggerPub() : LoggerInterface {
        return $this->logger();
    }


}
