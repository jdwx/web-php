<?php


declare( strict_types = 1 );


namespace Shims;


use JDWX\Web\Framework\AbstractRouter;
use JDWX\Web\IRequest;
use JDWX\Web\UrlParts;


class MyRouter extends AbstractRouter {


    /** @var ?callable */
    public $fnRoute = null;

    public bool $bReturn = true;


    public UrlParts $uriPartsCheck;

    public string $stPathCheck;

    public string $stUriCheck;

    public IRequest $requestCheck;


    public function route() : bool {
        $fnRoute = $this->fnRoute;
        if ( $fnRoute ) {
            $fnRoute();
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
