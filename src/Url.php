<?php


declare( strict_types = 1 );


namespace JDWX\Web;


class Url {


    private const ALLOWED_GEN_DELIMITERS = ':/?#[]@';

    private const ALLOWED_SUB_DELIMITERS = '!$&\'()*+,;=';

    /** @noinspection SpellCheckingInspection */
    private const ALLOWED_UNRESERVED_ALPHA = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    private const ALLOWED_UNRESERVED_DIGIT = '0123456789';

    private const ALLOWED_UNRESERVED_MISC  = '-._~';

    private const ALLOWED_UNRESERVED       =
        self::ALLOWED_UNRESERVED_ALPHA
        . self::ALLOWED_UNRESERVED_DIGIT
        . self::ALLOWED_UNRESERVED_MISC;

    private const ALLOWED_CHARACTERS       =
        self::ALLOWED_GEN_DELIMITERS
        . self::ALLOWED_SUB_DELIMITERS
        . self::ALLOWED_UNRESERVED;


    public static function split( string $i_stUri ) : ?UrlParts {

        # We can't use filter_var with FILTER_VALIDATE_URL because it now requires
        # a scheme and host, whereas we want to allow Uri paths as well.
        # But parse_url is *too* lenient, so we have to at least check if the URL
        # is made of valid characters.
        if ( strspn( $i_stUri, self::ALLOWED_CHARACTERS ) !== strlen( $i_stUri ) ) {
            return null;
        }

        $rUri = parse_url( $i_stUri );
        $parts = static::makeParts();

        if ( isset( $rUri[ 'scheme' ] ) ) {
            $parts->nstScheme = $rUri[ 'scheme' ];
        }

        if ( isset( $rUri[ 'host' ] ) ) {
            $stHost = filter_var( $rUri[ 'host' ], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME );
            if ( false === $stHost ) {
                return null;
            }
            $parts->nstHost = $stHost;
        }

        if ( isset( $rUri[ 'port' ] ) ) {
            $parts->nuPort = (int) $rUri[ 'port' ];
        }

        if ( isset( $rUri[ 'user' ] ) ) {
            $parts->nstUser = $rUri[ 'user' ];
        }

        if ( isset( $rUri[ 'pass' ] ) ) {
            $parts->nstPassword = $rUri[ 'pass' ];
        }

        if ( isset( $rUri[ 'path' ] ) ) {
            self::splitPath( $parts, $rUri[ 'path' ] );
        }

        if ( isset( $rUri[ 'query' ] ) ) {
            self::splitQuery( $parts, $rUri[ 'query' ] );
        }

        return $parts;
    }


    public static function splitEx( string $i_stUri ) : UrlParts {
        $parts = self::split( $i_stUri );
        if ( $parts instanceof UrlParts ) {
            return $parts;
        }
        throw new \InvalidArgumentException( 'Invalid URI: "' . $i_stUri . '"' );
    }


    protected static function makeParts() : UrlParts {
        return new UrlParts();
    }


    private static function splitPath( UrlParts $o_uri, string $i_stPath ) : void {
        if ( '/' === $i_stPath ) {
            return;
        }
        $i_stPath = ltrim( $i_stPath, '/' );
        $r = explode( '/', $i_stPath );

        # If the last element isn't blank, it's the file. If it is, it's irrelevant.
        $stLast = array_pop( $r );
        if ( '' !== $stLast ) {
            $o_uri->nstFile = $stLast;
        }

        # The rest are folders.
        $o_uri->subFolders = $r;
    }


    private static function splitQuery( UrlParts $o_uri, string $i_stQuery ) : void {
        parse_str( $i_stQuery, $o_uri->rQuery );
    }


}
