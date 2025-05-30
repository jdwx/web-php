<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Shims;


use Ds\Map;
use JDWX\Web\Framework\AbstractRoute;
use JDWX\Web\Framework\ResponseInterface;
use JDWX\Web\Framework\RouterInterface;
use JDWX\Web\ServerInterface;
use Psr\Log\LoggerInterface;


class MyRoute extends AbstractRoute {


    /** @var Map<string, callable> */
    private Map $mapCallbacks;


    /** @param iterable<string, callable> $i_rCallbacks */
    public function __construct( RouterInterface $router, iterable $i_rCallbacks = [],
                                 bool            $i_bAllowPathInfo = false ) {
        parent::__construct( $router );
        $this->mapCallbacks = new Map( $i_rCallbacks );
        $this->setAllowPathInfo( $i_bAllowPathInfo );
    }


    public function loggerPub() : LoggerInterface {
        return $this->logger();
    }


    public function serverPub() : ServerInterface {
        return $this->server();
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handleDELETE( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'delete' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'delete' ], $i_stUri, $i_stPath, $i_rUriParameters );
        }
        return parent::handleDELETE( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handleGET( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'get' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'get' ], $i_stUri, $i_stPath, $i_rUriParameters );
        }
        return parent::handleGET( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handleHEAD( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'head' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'head' ], $i_stUri, $i_stPath, $i_rUriParameters );
        }
        return parent::handleHEAD( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handlePATCH( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'patch' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'patch' ], $i_stUri, $i_stPath, $i_rUriParameters );
        }
        return parent::handlePATCH( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handlePOST( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'post' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'post' ], $i_stUri, $i_stPath, $i_rUriParameters );
        }
        return parent::handlePOST( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handlePUT( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        if ( isset( $this->mapCallbacks[ 'put' ] ) ) {
            return call_user_func( $this->mapCallbacks[ 'put' ], $i_stUri, $i_stPath );
        }
        return parent::handlePUT( $i_stUri, $i_stPath, $i_rUriParameters );
    }


}
