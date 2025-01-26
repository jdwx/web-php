<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Web\Framework\AbstractRouter;


/** @deprecated Use AbstractRouter. */
abstract class Router extends AbstractRouter {


    /** @deprecated Use $this->error->show() */
    protected function error( int     $i_uHTTPStatus, ?string $i_nstErrorName = null,
                              ?string $i_nstErrorText = null ) : void {
        $this->error->show( $i_uHTTPStatus, $i_nstErrorName, $i_nstErrorText );
    }


}
