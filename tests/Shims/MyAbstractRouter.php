<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Shims;


use JDWX\Web\Framework\AbstractRouter;
use JDWX\Web\Framework\ResponseInterface;
use JDWX\Web\RequestInterface;
use JDWX\Web\UrlParts;


require_once __DIR__ . '/MyRouterTrait.php';


class MyAbstractRouter extends AbstractRouter {


    use MyRouterTrait;


    /** @var ?callable */
    public $fnRoute = null;

    public bool $bReturn = true;


    public UrlParts $uriPartsCheck;

    public string $stPathCheck;

    public string $stUriCheck;

    public RequestInterface $requestCheck;

    public ?ResponseInterface $response = null;


    /** @suppress PhanParamTooMany */
    public function route( ?string $i_nstOverride = null ) : bool {
        $fnRoute = $this->fnRoute;
        if ( $fnRoute ) {
            $fnRoute( $i_nstOverride );
        } elseif ( $this->response instanceof ResponseInterface ) {
            $this->respond( $this->response );
        }
        return $this->bReturn;
    }


    public function save() : void {
        $this->uriPartsCheck = $this->uriParts();
        $this->stPathCheck = $this->path();
        $this->stUriCheck = $this->uri();
        $this->requestCheck = $this->request();
    }


}
