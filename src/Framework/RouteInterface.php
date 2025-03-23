<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


interface RouteInterface {


    public function handle( string $i_stUri, string $i_stPath ) : ?ResponseInterface;


}