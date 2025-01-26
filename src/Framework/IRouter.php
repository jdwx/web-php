<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


interface IRouter {


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


}

