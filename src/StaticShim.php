<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


/**
 * This shim provides basic static file handling to help test a site
 * with the PHP built-in webserver.  (In production, static files should
 * be served by the real web server and PHP requests should come in via
 * FastCGI.)
 */
class StaticShim {


    /** @var array<string, string> */
    protected array $rContentTypes = [
        'html' => 'text/html',
        'js' => 'application/javascript',
        'css' => 'text/css',
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'ico' => 'image/x-icon',
        'svg' => 'image/svg+xml',
        'txt' => 'text/plain',
        'webp' => 'image/webp',
    ];


    /** @var list<string> */
    protected array $rStaticPaths = [];

    /** @var array<string, string> */
    protected array $rStaticMaps;

    /** @var list<string> */
    protected array $rExcludePaths = [];

    protected string $stDocumentRoot;

    protected bool $bAuthoritative = false;


    public function __construct( ?string $i_nstDocumentRoot = null ) {
        $this->stDocumentRoot = $i_nstDocumentRoot ?? $_SERVER[ 'DOCUMENT_ROOT' ];
        if ( ! str_ends_with( $this->stDocumentRoot, '/' ) ) {
            $this->stDocumentRoot .= '/';
        }
        $this->rStaticMaps = [
            '/' => $this->stDocumentRoot,
        ];
    }


    public function addStaticMap( string $i_stFrom, string $i_stTo ) : void {
        $this->addStaticPath( $i_stFrom );
        $this->rStaticMaps[ $i_stFrom ] = $i_stTo;
    }


    public function addStaticPath( string $i_stPath ) : void {
        $this->rStaticPaths[] = $i_stPath;
    }


    public function excludeStaticPath( string $i_stPath ) : void {
        $this->rExcludePaths[] = $i_stPath;
    }


    public function handleStatic() : bool {

        $stURI = $_SERVER[ 'REQUEST_URI' ];

        if ( $this->rExcludePaths ) {
            foreach ( $this->rExcludePaths as $path ) {
                if ( str_starts_with( $stURI, $path ) ) {
                    return false;
                }
            }
        }

        if ( $this->rStaticPaths ) {
            $bOK = false;
            foreach ( $this->rStaticPaths as $path ) {
                if ( str_starts_with( $stURI, $path ) ) {
                    $bOK = true;
                    break;
                }
            }
            if ( ! $bOK ) {
                return false;
            }
            $this->bAuthoritative = true;
        }

        $b = $this->handleStaticFile( $stURI );
        if ( $b ) {
            return true;
        }
        if ( $this->bAuthoritative ) {
            $this->handle404();
            return true;
        }
        return false;
    }


    public function run() : bool {
        if ( $this->handleStatic() ) {
            return true;
        }
        if ( ! array_key_exists( 'QUERY_STRING', $_SERVER ) ) {
            $_SERVER[ 'QUERY_STRING' ] = '';
        }
        if ( ! array_key_exists( 'PATH_INFO', $_SERVER ) ) {
            $_SERVER[ 'PATH_INFO' ] = '';
        }
        return false;
    }


    protected function draw403() : void {
        echo "<html lang=\"en\"><head><title>403 Forbidden</title></head><body><h1>403 Forbidden</h1></body></html>";
    }


    protected function draw404() : void {
        echo "<html lang=\"en\"><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>";
    }


    protected function draw500() : void {
        echo "<html lang=\"en\"><head><title>500 Internal Server Error</title></head><body><h1>500 Internal Server Error</h1></body></html>";
    }


    protected function handle403() : void {
        http_response_code( 403 );
        $this->draw403();
    }


    protected function handle404() : void {
        http_response_code( 404 );
        $this->draw404();
    }


    protected function handle500() : void {
        http_response_code( 500 );
        $this->draw500();
    }


    protected function handleStaticFile( string $i_stURI ) : bool {

        $longest = 0;
        $stPrefix = '/var/empty/';
        foreach ( $this->rStaticMaps as $from => $to ) {
            if ( str_starts_with( $i_stURI, $from ) ) {
                if ( strlen( $from ) > $longest ) {
                    $longest = strlen( $from );
                    $stPrefix = $to;
                }
            }
        }


        $pathName = $stPrefix . substr( $i_stURI, $longest );
        $pathName = str_replace( '//', '/', $pathName );

        if ( str_contains( $pathName, '?' ) ) {
            $pathName = preg_replace( '#\?.*$#', '', $pathName );
        }

        $path = pathinfo( $pathName );
        if ( ! is_file( $pathName ) ) {
            if ( array_key_exists( 'extension', $path ) && $path[ 'extension' ] != '' ) {
                return false;
            }
        }

        if ( is_dir( $pathName ) ) {
            if ( $this->bAuthoritative ) {
                $this->handle403();
                return true;
            }
            return false;
        }

        // error_log( $pathName );
        if ( array_key_exists( 'extension', $path ) ) {
            $ext = $path[ 'extension' ];
        } else {
            $ext = null;
            foreach ( $this->rContentTypes as $e => $t ) {
                $newPath = $pathName . '.' . $e;
                if ( file_exists( $newPath ) ) {
                    $pathName = $newPath;
                    $ext = $e;
                    break;
                }
            }
            if ( is_null( $ext ) ) {
                return false;
            }
        }

        if ( $ext == 'php' ) {
            return false;
        }

        if ( array_key_exists( $ext, $this->rContentTypes ) ) {
            header( 'Content-Type: ' . $this->rContentTypes[ $ext ] );
            readfile( $pathName );
            return true;
        }

        header( 'Content-Type: text/plain' );
        readfile( $pathName );
        return true;

    }


}

