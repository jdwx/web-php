<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


/** @codeCoverageIgnore */
trait DownstreamRouteTrait {


    /** @param array<string, string> $i_rUriParameters */
    abstract protected function handleDELETE( string $i_stUri, string $i_stPath,
                                              array  $i_rUriParameters ) : ?ResponseInterface;


    /** @param array<string, string> $i_rUriParameters */
    abstract protected function handleGET( string $i_stUri, string $i_stPath,
                                           array  $i_rUriParameters ) : ?ResponseInterface;


    /** @param array<string, string> $i_rUriParameters */
    abstract protected function handleHEAD( string $i_stUri, string $i_stPath,
                                            array  $i_rUriParameters ) : ?ResponseInterface;


    /** @param array<string, string> $i_rUriParameters */
    abstract protected function handlePATCH( string $i_stUri, string $i_stPath,
                                             array  $i_rUriParameters ) : ?ResponseInterface;


    /** @param array<string, string> $i_rUriParameters */
    abstract protected function handlePOST( string $i_stUri, string $i_stPath,
                                            array  $i_rUriParameters ) : ?ResponseInterface;


    /** @param array<string, string> $i_rUriParameters */
    abstract protected function handlePUT( string $i_stUri, string $i_stPath,
                                           array  $i_rUriParameters ) : ?ResponseInterface;


}