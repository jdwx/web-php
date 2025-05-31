<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Shims;


/**
 * Can't declare an abstract route() method in this class because
 * static analysis loses its mind.
 */
trait MyRouterTrait {


    /** @suppress PhanUndeclaredMethod */
    public function routeOutput( mixed ...$x ) : ?string {
        ob_start();
        $b = $this->route( ...$x );
        $st = ob_get_clean();
        return $b ? $st : null;
    }


    /** @suppress PhanUndeclaredMethod */
    public function routeQuiet( mixed ...$x ) : bool {
        ob_start();
        $b = $this->route( ...$x );
        ob_end_clean();
        return $b;
    }


}