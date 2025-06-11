<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Strict\OK;


/**
 * This class extends StaticShim and also does whatever minimal fix-ups
 * are necessary to make the PHP built-in web server work with the router
 * script. (I.e. make requests resemble FastCGI as much as possible.)
 *
 * In production, static files should be served by a real web server
 * and PHP requests should come in via FastCGI. If you are using this
 * in production, you are doing it wrong.
 */
class PhpWsShim extends StaticShim {


    /** @var array<string, callable> */
    protected array $rBaseHooks = [];

    /** @var array<string, callable> */
    protected array $rExactHooks = [];

    protected HttpError $error;


    public function __construct( private readonly RouterInterface $router, ?string $i_nstDocumentRoot = null ) {
        parent::__construct( $i_nstDocumentRoot, $router->getHttpError(), $router->request() );
    }


    /**
     * Add a hook to be called when the URI matches the given string.
     *
     * @param bool $i_bExact If true, requires exact match. If false (default) receives
     *                       anything underneath as well.
     */
    public function addHook( string $i_stURI, callable $i_fnCallBack, bool $i_bExact = false ) : void {
        if ( $i_bExact ) {
            $this->rExactHooks[ $i_stURI ] = $i_fnCallBack;
            return;
        }
        $this->rBaseHooks[ $i_stURI ] = $i_fnCallBack;
    }


    public function run() : bool {

        if ( parent::run() ) {
            return true;
        }

        # These are fixups to make the PHP built-in web server look more like FastCGI
        # for older code that hasn't been updated to look at the request instead of
        # the environment.
        $scriptName = $this->request->uri();
        $scriptName = OK::preg_replace_string( '#\?.*$#', '', $scriptName );
        $_SERVER[ 'SCRIPT_NAME' ] = $scriptName;
        $_SERVER[ 'PATH_INFO' ] = $scriptName;
        if ( ! array_key_exists( 'QUERY_STRING', $_SERVER ) ) {
            $_SERVER[ 'QUERY_STRING' ] = '';
        }

        foreach ( $this->rExactHooks as $stURI => $fnCallBack ) {
            if ( $scriptName === $stURI ) {
                $fnCallBack();
                return true;
            }
        }

        foreach ( $this->rBaseHooks as $stURI => $fnCallBack ) {
            if ( $scriptName === $stURI || ( str_starts_with( $scriptName, $stURI ) && str_ends_with( $stURI, '/' ) ) ) {
                $fnCallBack();
                return true;
            }
        }

        $this->router->run();
        return true;
    }


}
