<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Web\Request;
use JDWX\Web\RequestInterface;


/**
 * This shim provides basic static file handling to help test a site
 * with the PHP built-in webserver.
 *
 * In production, static files should be served by a real web server,
 * and PHP requests should come in via FastCGI. If you are using this
 * in production, you are doing it wrong.
 *
 */
class StaticShim {


    use HttpTrait;

    use StaticTrait;


    /** @var list<string> */
    protected array $rStaticPaths = [];

    /** @var array<string, string> */
    protected array $rStaticMaps;

    /** @var list<string> */
    protected array $rExcludePaths = [];

    protected string $stDocumentRoot;

    protected bool $bAuthoritative = false;

    protected HttpError $error;

    protected RequestInterface $request;


    public function __construct( ?string           $i_nstDocumentRoot = null, ?HttpError $i_error = null,
                                 ?RequestInterface $i_req = null ) {
        $this->error = $i_error ?? new HttpError();
        $this->request = $i_req ?? Request::getGlobal();
        $this->stDocumentRoot = $i_nstDocumentRoot ?? $this->request->server()->documentRoot();
        if ( ! str_ends_with( $this->stDocumentRoot, '/' ) ) {
            $this->stDocumentRoot .= '/';
        }
        $this->rStaticMaps = [
            '/' => $this->stDocumentRoot,
        ];
    }


    /**
     * Maps a path to another path. This is useful if your static content is
     * in a /static/ folder but your URLs don't have /static/ in them, or if
     * you need to map in static content from multiple locations.
     *
     * Note that this calls addStaticUri(), and so it invokes the behavior
     * change described there. (I.e., the minute you map one static path,
     * you have to map or explicitly allow all static paths.)
     *
     * @param string $i_stFromUri URI path to map from.
     * @param string $i_stToDirectory Filesystem directory to map to.
     */
    public function addStaticMap( string $i_stFromUri, string $i_stToDirectory ) : void {
        $this->addStaticUri( $i_stFromUri );
        $this->rStaticMaps[ $i_stFromUri ] = $i_stToDirectory;
    }


    /**
     * Add a URI to the list of URIs that are considered static. This means
     * that the static shim will treat itself as authoritative for these paths.
     *
     * By default, the shim will consider any URI to be potentially static,
     * which is suitable when .php files coexist with static files in the
     * same directory. But if you add even one path here, then only paths in
     * this list will be considered static.
     *
     * Keep in mind that you will need to add static URIs for things like
     * /robots.txt and /favicon.ico once you start down this road.
     *
     * @param string $i_stStaticUri URI path to treat as authoritatively static.
     */
    public function addStaticUri( string $i_stStaticUri ) : void {
        $this->rStaticPaths[] = $i_stStaticUri;
    }


    /**
     * Exclude a path from static handling. Like .git, for example. (But
     * don't put .git in your document root, that's a bad idea.)
     *
     * @param string $i_stPath URI path to exclude from static handling.
     */
    public function excludeStaticPath( string $i_stPath ) : void {
        $this->rExcludePaths[] = $i_stPath;
    }


    public function handleStatic() : bool {

        $this->bAuthoritative = false;
        try {
            $stURI = $this->request->path();
        } catch ( \InvalidArgumentException ) {
            return false;
        }

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
        return false;
    }


    public function run() : bool {
        if ( $this->handleStatic() ) {
            return true;
        }
        return false;
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
        $pathName = $this->multiViews( $pathName );

        if ( ! is_string( $pathName ) ) {
            if ( $this->bAuthoritative ) {
                $this->error->show( 404 );
                return true;
            }
            return false;
        }

        if ( is_dir( $pathName ) ) {
            if ( $this->bAuthoritative ) {
                $this->error->show( 403 );
                return true;
            }
            return false;
        }

        $stExt = $this->inferExtension( $pathName );
        if ( 'php' === $stExt ) {
            return false;
        }
        $stContentType = $this->lookupContentType( $stExt ) ?? 'application/octet-stream';

        $this->setHeader( 'Content-Type: ' . $stContentType );
        readfile( $pathName );

        return true;

    }


}

