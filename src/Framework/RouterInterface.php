<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Web\RequestInterface;
use JDWX\Web\ServerInterface;
use Psr\Log\LoggerInterface;


interface RouterInterface {


    public function assertGET() : void;


    public function assertPOST() : void;


    public function getHttpError() : HttpError;


    public function logger() : LoggerInterface;


    public function methodNotAllowed( ?string $i_nstUri = null, ?string $i_nstPath = null,
                                      ?string $i_nstMessage = null ) : never;


    /**
     * Retrieve the Request object this router is using.
     */
    public function request() : RequestInterface;


    /**
     * Use the route() entry point to handle the request from a
     * higher-level handler that might be trying multiple ways
     * to handle the request. Performs minimal error handling.
     *
     * @return bool True if this handled the request, otherwise false.
     */
    public function route() : bool;


    /**
     * Use the run() entry point to handle the request if you're eating
     * it directly from the web server via FastCGI or similar. Performs
     * more extensive error handling, including catching HttpStatusExceptions.
     *
     * @return void
     */
    public function run() : void;


    /**
     * @return ServerInterface The server object this router is using.
     */
    public function server() : ServerInterface;


}

