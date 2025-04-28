<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


class ScriptUri extends AbstractScript {


    public function __construct( private readonly string $uri ) { }


    protected function attrs() : iterable {
        yield from parent::attrs();
        yield 'src' => $this->uri;
    }


}