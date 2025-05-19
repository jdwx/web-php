<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Web\Pages\PageInterface;
use JDWX\Web\Pages\SimpleHtmlPage;
use JDWX\Web\Pages\SimpleJsonPage;
use JDWX\Web\Pages\SimpleTextPage;


/**
 * Provides static methods to create different types of responses. These
 * three types cover about 99% of all responses for the use cases this
 * framework is designed for. (This statistic is one of the 99% of all
 * statistics that are made up.)
 */
readonly class Response extends AbstractResponse {


    /** @param ?iterable<string> $i_rHeaders */
    public static function html( string $i_stContent, int $i_uStatusCode = 200, ?iterable $i_rHeaders = null ) : self {
        return static::page( new SimpleHtmlPage( $i_stContent ), $i_uStatusCode, $i_rHeaders );
    }


    /** @param ?iterable<string> $i_rHeaders */
    public static function json( mixed $i_rContent, int $i_uStatusCode = 200, ?iterable $i_rHeaders = null ) : self {
        return static::page( new SimpleJsonPage( $i_rContent ), $i_uStatusCode, $i_rHeaders );
    }


    /** @param ?iterable<string> $i_rHeaders */
    public static function page( PageInterface $i_page, int $i_uStatusCode = 200, ?iterable $i_rHeaders = null ) : self {
        return new self( $i_page, $i_uStatusCode, $i_rHeaders );
    }


    public static function redirectPermanentWithGet( string $i_stLocation ) : self {
        $page = new SimpleHtmlPage(
            '<p>Moved Permanently: <a href="' . $i_stLocation . '">' . $i_stLocation . '</a></p>'
        );
        return new self( $page, 301, [ "Location: {$i_stLocation}" ] );
    }


    public static function redirectPermanentWithSameMethod( string $i_stLocation ) : self {
        $page = new SimpleHtmlPage(
            '<p>Permanent Redirect: <a href="' . $i_stLocation . '">' . $i_stLocation . '</a></p>'
        );
        return new self( $page, 308, [ "Location: {$i_stLocation}" ] );
    }


    public static function redirectTemporaryWithGet( string $i_stLocation ) : self {
        $page = new SimpleHtmlPage(
            '<p>See Other: <a href="' . $i_stLocation . '">' . $i_stLocation . '</a></p>'
        );
        return new self( $page, 303, [ "Location: {$i_stLocation}" ] );
    }


    public static function redirectTemporaryWithSameMethod( string $i_stLocation ) : self {
        $page = new SimpleHtmlPage(
            '<p>Temporary Redirect: <a href="' . $i_stLocation . '">' . $i_stLocation . '</a></p>'
        );
        return new self( $page, 307, [ "Location: {$i_stLocation}" ] );
    }


    /** @param ?iterable<string> $i_rHeaders */
    public static function text( string $i_stContent, int $i_uStatusCode = 200, ?iterable $i_rHeaders = null ) : self {
        return static::page( new SimpleTextPage( $i_stContent ), $i_uStatusCode, $i_rHeaders );
    }


}
