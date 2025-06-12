<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Strict\OK;
use JDWX\Strict\TypeIs;


class Url {


    private const string ALLOWED_GEN_DELIMITERS = ':/?#[]@';

    private const string ALLOWED_SUB_DELIMITERS = '!$&\'()*+,;=';

    private const string ALLOWED_ENCODE         = '%';

    /** @noinspection SpellCheckingInspection */
    private const string ALLOWED_UNRESERVED_ALPHA = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    private const string ALLOWED_UNRESERVED_DIGIT = '0123456789';

    private const string ALLOWED_UNRESERVED_MISC  = '-._~';

    private const string ALLOWED_UNRESERVED       =
        self::ALLOWED_UNRESERVED_ALPHA
        . self::ALLOWED_UNRESERVED_DIGIT
        . self::ALLOWED_UNRESERVED_MISC;

    private const string ALLOWED_CHARACTERS       =
        self::ALLOWED_GEN_DELIMITERS
        . self::ALLOWED_SUB_DELIMITERS
        . self::ALLOWED_ENCODE
        . self::ALLOWED_UNRESERVED;


    public static function host( string $i_url ) : ?string {
        return self::splitEx( $i_url )->nstHost;
    }


    public static function hostEx( string $i_url ) : string {
        $nst = self::host( $i_url );
        if ( is_string( $nst ) ) {
            return $nst;
        }
        throw new \RuntimeException( "No host in URL: {$i_url}" );
    }


    public static function parent( string $i_url ) : string {
        return self::splitEx( $i_url )->parent()->__toString();
    }


    public static function path( string $i_url ) : string {
        return self::splitEx( $i_url )->path();
    }


    public static function scheme( string $i_url ) : ?string {
        return self::splitEx( $i_url )->nstScheme;
    }


    public static function schemeEx( string $i_url ) : string {
        $nst = self::scheme( $i_url );
        if ( is_string( $nst ) ) {
            return $nst;
        }
        throw new \RuntimeException( "No scheme in URL: {$i_url}" );
    }


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

        if ( isset( $rUri[ 'fragment' ] ) ) {
            $parts->nstFragment = $rUri[ 'fragment' ];
        }

        return $parts;
    }


    public static function splitEx( string $i_stUri ) : UrlParts {
        $parts = static::split( $i_stUri );
        if ( $parts instanceof UrlParts ) {
            return $parts;
        }
        throw new \InvalidArgumentException( 'Invalid URI: "' . $i_stUri . '"' );
    }


    public static function validatePathSegment( ?string $i_nstComponent ) : bool {

        # It could be null if the URI ends in a slash, which is fine.
        if ( is_null( $i_nstComponent ) ) {
            return true;
        }

        # But an empty string is not.
        if ( '' === $i_nstComponent ) {
            return false;
        }

        # And we don't want to see any double-dots.
        if ( '..' === $i_nstComponent ) {
            return false;
        }

        # Don't really want single dots either. (I.e., don't use this
        # method to validate a relative URI!)
        if ( '.' === $i_nstComponent ) {
            return false;
        }

        # The allowable characters are:
        # pchar         = unreserved / pct-encoded / sub-delims / ":" / "@"
        # unreserved  = ALPHA / DIGIT / "-" / "." / "_" / "~"
        # pct-encoded = "%" HEXDIG HEXDIG
        # sub-delims   = "!" / "$" / "&" / "'" / "(" / ")" / "*" / "+" / "," / ";" / "="
        #
        # What we're going to do here is try to whittle the path down
        # to nothing by removing valid components. If we can't, then
        # there's something invalid in there.

        # Start with pct-encoded. It's the only multi-character component.
        $i_nstComponent = OK::preg_replace( '/%[0-9A-Fa-f]{2}/', '', $i_nstComponent );

        # Now remove the sub-delims. Note % is no longer valid after above,
        # but we'll get : and @ while we're at it.
        $i_nstComponent = OK::preg_replace( '/[!$&\'()*+,;=:@]/', '', $i_nstComponent );

        # Now remove the unreserved characters.
        $i_nstComponent = OK::preg_replace( '/[A-Za-z0-9\-._~]/', '', $i_nstComponent );

        return '' === $i_nstComponent;
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
        parse_str( $i_stQuery, $r );
        $o_uri->rQuery = TypeIs::mapStringOrListString( $r );
    }


}
