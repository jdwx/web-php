<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use Ds\Set;
use JDWX\Web\Framework\Exceptions\NotImplementedException;
use JDWX\Web\Pages\PageInterface;
use JDWX\Web\Pages\SimpleHtmlPage;
use JDWX\Web\Pages\SimpleJsonPage;
use JDWX\Web\Pages\SimpleTextPage;
use JDWX\Web\RequestInterface;
use JDWX\Web\ServerInterface;
use Psr\Log\LoggerInterface;


abstract class AbstractRoute implements RouteInterface {


    use DownstreamRouteTrait;


    protected const bool ALLOW_POST = false;

    private bool $bAllowPathInfo = false;


    public function __construct( private readonly RouterInterface $router ) {}


    public function allowPathInfo() : bool {
        return $this->bAllowPathInfo;
    }


    public function handle( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        return match ( $this->method() ) {
            'GET' => $this->handleGET( $i_stUri, $i_stPath, $i_rUriParameters ),
            'HEAD' => $this->handleHEAD( $i_stUri, $i_stPath, $i_rUriParameters ),
            'POST' => $this->handlePOST( $i_stUri, $i_stPath, $i_rUriParameters ),
            'PUT' => $this->handlePUT( $i_stUri, $i_stPath, $i_rUriParameters ),
            'DELETE' => $this->handleDELETE( $i_stUri, $i_stPath, $i_rUriParameters ),
            'CONNECT' => $this->handleCONNECT( $i_stUri, $i_stPath, $i_rUriParameters ),
            'OPTIONS' => $this->handleOPTIONS( $i_stUri, $i_stPath, $i_rUriParameters ),
            'PATCH' => $this->handlePATCH( $i_stUri, $i_stPath, $i_rUriParameters ),
            'TRACE' => $this->handleTRACE( $i_stUri, $i_stPath, $i_rUriParameters ),
            default => throw new NotImplementedException( "Method {$this->method()} not implemented." ),
        };
    }


    /**
     * @suppress PhanTypeMissingReturnReal
     * @param array<string, string|list<string>> $i_rUriParameters
     */
    protected function handleCONNECT( string $i_stUri, string $i_stPath,
                                      array  $i_rUriParameters ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /**
     * @suppress PhanTypeMissingReturnReal
     * @param array<string, string|list<string>> $i_rUriParameters
     */
    protected function handleDELETE( string $i_stUri, string $i_stPath,
                                     array  $i_rUriParameters ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /**
     * @suppress PhanTypeMissingReturnReal
     * @param array<string, string|list<string>> $i_rUriParameters
     */
    protected function handleGET( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /**
     * @suppress PhanTypeMissingReturnReal
     * @param array<string, string|list<string>> $i_rUriParameters
     */
    protected function handleHEAD( string $i_stUri, string $i_stPath,
                                   array  $i_rUriParameters ) : ?ResponseInterface {
        return $this->handleGET( $i_stUri, $i_stPath, $i_rUriParameters );
    }


    /**
     * @suppress PhanTypeMissingReturnReal
     * @param array<string, string|list<string>> $i_rUriParameters
     */
    protected function handleOPTIONS( string $i_stUri, string $i_stPath,
                                      array  $i_rUriParameters ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /**
     * @suppress PhanTypeMissingReturnReal
     * @param array<string, string|list<string>> $i_rUriParameters
     */
    protected function handlePATCH( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /**
     * @suppress PhanTypeMissingReturnReal
     * @param array<string, string|list<string>> $i_rUriParameters
     */
    protected function handlePOST( string $i_stUri, string $i_stPath,
                                   array  $i_rUriParameters ) : ?ResponseInterface {
        if ( static::ALLOW_POST ) {
            return $this->handleGET( $i_stUri, $i_stPath, $i_rUriParameters );
        }
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /**
     * @suppress PhanTypeMissingReturnReal
     * @param array<string, string|list<string>> $i_rUriParameters
     */
    protected function handlePUT( string $i_stUri, string $i_stPath,
                                  array  $i_rUriParameters ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    /**
     * @suppress PhanTypeMissingReturnReal
     * @param array<string, string|list<string>> $i_rUriParameters
     */
    protected function handleTRACE( string $i_stUri, string $i_stPath,
                                    array  $i_rUriParameters ) : ?ResponseInterface {
        $this->methodNotAllowed( $i_stUri, $i_stPath );
    }


    protected function logger() : LoggerInterface {
        return $this->router()->logger();
    }


    protected function method() : string {
        return $this->request()->method();
    }


    protected function methodNotAllowed( ?string $i_nstUri = null, ?string $i_nstPath = null,
                                         ?string $i_nstMessage = null ) : never {
        $this->router->methodNotAllowed( $i_nstUri, $i_nstPath, $i_nstMessage );
    }


    protected function request() : RequestInterface {
        return $this->router()->request();
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondHtml( string $i_stContent, int $i_uStatus = 200,
                                    ?Set   $i_setHeaders = null ) : ResponseInterface {
        return $this->respondPage( new SimpleHtmlPage( $i_stContent ), $i_uStatus, $i_setHeaders );
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondJson( mixed $i_content, int $i_uStatus = 200, bool $i_bPretty = false,
                                    ?Set  $i_setHeaders = null ) : ResponseInterface {
        return $this->respondPage( new SimpleJsonPage( $i_content, $i_bPretty ), $i_uStatus, $i_setHeaders );
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondPage( PageInterface $i_page, int $i_nstStatus = 200,
                                    ?Set          $i_setHeaders = null ) : ResponseInterface {
        return Response::page( $i_page, $i_nstStatus, $i_setHeaders );
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondText( string $i_stContent, int $i_uStatus = 200,
                                    ?Set   $i_setHeaders = null ) : ResponseInterface {
        return $this->respondPage( new SimpleTextPage( $i_stContent ), $i_uStatus, $i_setHeaders );
    }


    protected function router() : RouterInterface {
        return $this->router;
    }


    protected function server() : ServerInterface {
        return $this->router()->server();
    }


    protected function setAllowPathInfo( bool $i_bAllowPathInfo ) : void {
        $this->bAllowPathInfo = $i_bAllowPathInfo;
    }


}
