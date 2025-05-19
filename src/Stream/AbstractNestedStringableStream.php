<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


abstract class AbstractNestedStringableStream extends AbstractStringableStream {


    use NestedStreamableTrait;
}
