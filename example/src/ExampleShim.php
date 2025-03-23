<?php


declare( strict_types = 1 );


namespace JDWX\Web\static;


use JDWX\Web\Example\ExampleRouter;
use JDWX\Web\Framework\PhpWsShim;


class ExampleShim extends PhpWsShim {


    public function __construct() {
        $router = new ExampleRouter();
        parent::__construct( $router, __DIR__ . '/../static/' );

        # We put this here because this class only runs on the test server. In
        # general, you should not expose this information on a production
        # server.
        $this->addHook( '/phpinfo', function () {
            phpinfo();
        } );

    }


}
