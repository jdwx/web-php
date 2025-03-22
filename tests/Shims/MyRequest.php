<?php


declare( strict_types = 1 );


namespace Shims;


use JDWX\Web\Request;


readonly class MyRequest extends Request {


    public static function whackGlobal() : void {
        static::req( null, true );
    }


}
