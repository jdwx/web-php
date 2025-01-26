<?php


declare( strict_types = 1 );


namespace Shims;


require_once __DIR__ . '/HttpTesterTrait.php';


use JDWX\Web\Framework\HttpError;


class MyHttpError extends HttpError {


    use HttpTesterTrait;
}
