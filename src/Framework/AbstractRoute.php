<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\Exceptions\NotImplementedException;
use JDWX\Web\RequestInterface;
use Psr\Log\LoggerInterface;


abstract class AbstractRoute implements RouteInterface {


    public function __construct( private readonly RouterInterface $router ) {}


    public function handle( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        return match ( $this->method() ) {
            'get' => $this->handleGET( $i_stUri, $i_stPath ),
            'post' => $this->handlePOST( $i_stUri, $i_stPath ),
            'put' => $this->handlePUT( $i_stUri, $i_stPath ),
            'delete' => $this->handleDELETE( $i_stUri, $i_stPath ),
            'head' => $this->handleHEAD( $i_stUri, $i_stPath ),
            'patch' => $this->handlePATCH( $i_stUri, $i_stPath ),
            default => throw new NotImplementedException(),
        };
    }


    /** @suppress PhanTypeMissingReturnReal */
    protected function handleDELETE( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /** @suppress PhanTypeMissingReturnReal */
    protected function handleGET( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /** @suppress PhanTypeMissingReturnReal */
    protected function handleHEAD( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /** @suppress PhanTypeMissingReturnReal */
    protected function handlePATCH( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /** @suppress PhanTypeMissingReturnReal */
    protected function handlePOST( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /** @suppress PhanTypeMissingReturnReal */
    protected function handlePUT( string $i_stUri, string $i_stPath ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    protected function logger() : LoggerInterface {
        return $this->router()->logger();
    }


    protected function method() : string {
        return $this->request()->method();
    }


    protected function methodNotAllowed( string $i_stUri, string $i_stPath ) : never {
        $stMessage = sprintf( 'Method %s not allowed for URI: %s, Path: %s', $this->method(), $i_stUri, $i_stPath );
        throw new MethodNotAllowedException( $stMessage );
    }


    protected function request() : RequestInterface {
        return $this->router()->request();
    }


    protected function router() : RouterInterface {
        return $this->router;
    }


}
