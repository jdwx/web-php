<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


class ScriptBody extends AbstractScript {


    public function __construct( private readonly string $stBody ) {
    }


    protected function body() : string {
        return $this->stBody;
    }


}