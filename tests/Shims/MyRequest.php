<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Shims;


use JDWX\Web\Request;


readonly class MyRequest extends Request {


    public static function whackGlobal() : void {
        static::req( null, true );
    }


}
