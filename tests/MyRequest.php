<?php


declare( strict_types = 1 );


use JDWX\Web\Request;


class MyRequest extends Request {


    public static function whackGlobal() : void {
        static::$req = null;
    }


}
