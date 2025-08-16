<?php


declare( strict_types = 1 );


namespace JDWX\Web\Example;


use JDWX\Web\Framework\HttpError;
use JDWX\Web\Framework\Router;


require_once __DIR__ . '/ExampleRouteAdd.php';
require_once __DIR__ . '/ExampleRouteHome.php';
require_once __DIR__ . '/ExampleRouteRedirect.php';
require_once __DIR__ . '/ExampleRouteStream.php';


class ExampleRouter extends Router {


    public function __construct() {
        $error = new HttpError( __DIR__ . '/../errors/error%d.php' );
        parent::__construct( i_error: $error );
        $this->addRoute( '/', ExampleRouteHome::class );
        $this->addRoute( '/add', ExampleRouteAdd::class );
        $this->addRoute( '/redirect', ExampleRouteRedirect::class );
        $this->addRoute( '/stream', ExampleRouteStream::class );
    }


}
