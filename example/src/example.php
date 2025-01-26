<?php


declare( strict_types = 1 );


namespace JDWX\Web\Example;


use JDWX\Web\static\ExampleShim;


require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/ExampleRouter.php';
require __DIR__ . '/ExampleShim.php';


( new ExampleShim() )->run();
