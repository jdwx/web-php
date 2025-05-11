<?php


declare( strict_types = 1 );


namespace Framework;


require_once __DIR__ . '/../Shims/MyRoute.php';
require_once __DIR__ . '/../Shims/MyRouteRouterTestBase.php';
require_once __DIR__ . '/../Shims/MyRouterInterface.php';
require_once __DIR__ . '/../Shims/MyRouterTrait.php';


use JDWX\Web\Framework\TrieRouter;
use JDWX\Web\RequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Shims\MyRouterInterface;
use Shims\MyRouteRouterTestBase;
use Shims\MyRouterTrait;


#[CoversClass( TrieRouter::class )]
final class TrieRouterTest extends MyRouteRouterTestBase {


    protected function newRouter( ?RequestInterface $i_req = null ) : MyRouterInterface {
        $i_req ??= $this->newRequest( 'GET', '/test' );
        return new class( i_req: $i_req ) extends TrieRouter implements MyRouterInterface {


            use MyRouterTrait;
        };
    }


}