<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


/**
 * This class extends StaticShim and also does whatever minimal fix-ups
 * are necessary to make the PHP built-in web server work with the router
 * script. (I.e. make requests resemble FastCGI as much as possible.)
 */
class PhpWsShim extends StaticShim {


    /** @var array<string, callable> */
    protected array $rBaseHooks = [];

    /** @var array<string, callable> */
    protected array $rExactHooks = [];


    public function __construct( private readonly string $stRouterPath, ?string $i_nstDocumentRoot = null ) {
        parent::__construct( $i_nstDocumentRoot );
    }


    /** @param bool $i_bExact If true, requires exact match. If false (default) receives anything underneath as well. */
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

        $scriptName = $_SERVER[ 'REQUEST_URI' ];
        $scriptName = preg_replace( '#\?.*$#', '', $scriptName );
        $_SERVER[ 'SCRIPT_NAME' ] = $scriptName;
        $_SERVER[ 'PATH_INFO' ] = $scriptName;

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

        require $this->stRouterPath;

        return true;
    }


}
