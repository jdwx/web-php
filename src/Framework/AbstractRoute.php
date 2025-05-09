<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use Ds\Set;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\Exceptions\NotImplementedException;
use JDWX\Web\JsonPage;
use JDWX\Web\PageInterface;
use JDWX\Web\Panels\PanelInterface;
use JDWX\Web\Panels\PanelPage;
use JDWX\Web\RequestInterface;
use JDWX\Web\ServerInterface;
use JDWX\Web\SimpleHtmlPage;
use JDWX\Web\TextPage;
use Psr\Log\LoggerInterface;


abstract class AbstractRoute implements RouteInterface {


    public function __construct( private readonly RouterInterface $router ) { }


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


    /** @param ?Set<string> $i_setHeaders */
    protected function respondHtml( string $i_stContent, int $i_uStatus = 200,
                                    ?Set   $i_setHeaders = null ) : ResponseInterface {
        return $this->respondPage( new SimpleHtmlPage( $i_stContent ), $i_uStatus, $i_setHeaders );
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondJson( mixed $i_content, int $i_uStatus = 200, bool $i_bPretty = false,
                                    ?Set  $i_setHeaders = null ) : ResponseInterface {
        return $this->respondPage( new JsonPage( $i_content, $i_bPretty ), $i_uStatus, $i_setHeaders );
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondPage( PageInterface $i_page, int $i_nstStatus = 200,
                                    ?Set          $i_setHeaders = null ) : ResponseInterface {
        return Response::page( $i_page, $i_nstStatus, $i_setHeaders );
    }


    /**
     * @param list<PanelInterface>|PanelInterface $i_rPanels
     * @param ?Set<string> $i_setHeaders
     */
    protected function respondPanel( array|PanelInterface $i_rPanels, int $i_uStatus = 200,
                                     ?Set                 $i_setHeaders = null,
                                     ?string              $i_nstLanguage = null ) : ResponseInterface {
        return $this->respondPage( new PanelPage( $i_rPanels, $i_nstLanguage ), $i_uStatus, $i_setHeaders );
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondText( string $i_stContent, int $i_uStatus = 200,
                                    ?Set   $i_setHeaders = null ) : ResponseInterface {
        return $this->respondPage( new TextPage( $i_stContent ), $i_uStatus, $i_setHeaders );
    }


    protected function router() : RouterInterface {
        return $this->router;
    }


    protected function server() : ServerInterface {
        return $this->router()->server();
    }


}
