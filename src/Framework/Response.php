<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Web\JsonPage;
use JDWX\Web\SimpleHtmlPage;
use JDWX\Web\TextPage;


/**
 * Provides static methods to create different types of responses. These
 * three types cover about 99% of all responses for the use cases this
 * framework is designed for. (This statistic is one of the 99% of all
 * statistics that are made up.)
 */
readonly class Response extends AbstractResponse {


    /** @param ?iterable<string> $i_rHeaders */
    public static function html( string $i_stContent, int $i_uStatusCode = 200, ?iterable $i_rHeaders = null ) : self {
        return new self( new SimpleHtmlPage( $i_stContent ), $i_uStatusCode, $i_rHeaders );
    }


    /** @param ?iterable<string> $i_rHeaders */
    public static function json( mixed $i_rContent, int $i_uStatusCode = 200, ?iterable $i_rHeaders = null ) : self {
        return new self( new JsonPage( $i_rContent ), $i_uStatusCode, $i_rHeaders );
    }


    /** @param ?iterable<string> $i_rHeaders */
    public static function text( string $i_stContent, int $i_uStatusCode = 200, ?iterable $i_rHeaders = null ) : self {
        return new self( new TextPage( $i_stContent ), $i_uStatusCode, $i_rHeaders );
    }


}
