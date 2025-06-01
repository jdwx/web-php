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
    protected function handleCONNECT( string $i_stUri, string $i_stPath,
                                      array  $i_rUriParameters ) : ?ResponseInterface {
        $x = $this->tryCallback( $i_stUri, $i_stPath, $i_rUriParameters );
        return ( $x !== false ) ? $x : parent::handleCONNECT( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handleDELETE( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $x = $this->tryCallback( $i_stUri, $i_stPath, $i_rUriParameters );
        return ( $x !== false ) ? $x : parent::handleDELETE( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handleGET( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $x = $this->tryCallback( $i_stUri, $i_stPath, $i_rUriParameters );
        return ( $x !== false ) ? $x : parent::handleGET( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handleHEAD( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $x = $this->tryCallback( $i_stUri, $i_stPath, $i_rUriParameters );
        return ( $x !== false ) ? $x : parent::handleHEAD( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handleOPTIONS( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $x = $this->tryCallback( $i_stUri, $i_stPath, $i_rUriParameters );
        return ( $x !== false ) ? $x : parent::handleOPTIONS( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handlePATCH( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $x = $this->tryCallback( $i_stUri, $i_stPath, $i_rUriParameters );
        return ( $x !== false ) ? $x : parent::handlePATCH( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handlePOST( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $x = $this->tryCallback( $i_stUri, $i_stPath, $i_rUriParameters );
        return ( $x !== false ) ? $x : parent::handlePOST( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handlePUT( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $x = $this->tryCallback( $i_stUri, $i_stPath, $i_rUriParameters );
        return ( $x !== false ) ? $x : parent::handlePUT( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    protected function handleTRACE( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $x = $this->tryCallback( $i_stUri, $i_stPath, $i_rUriParameters );
        return ( $x !== false ) ? $x : parent::handleTRACE( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /** @param array<string, string> $i_rUriParameters */
    private function tryCallback( string $i_stUri, string $i_stPath,
                                  array  $i_rUriParameters ) : false|ResponseInterface|null {
        $stMethod = $this->method();
        if ( isset( $this->mapCallbacks[ $stMethod ] ) ) {
            return call_user_func( $this->mapCallbacks[ $stMethod ], $i_stUri, $i_stPath, $i_rUriParameters );
        }
        return false;
    }


}
