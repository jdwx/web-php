<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


trait BodyPanelTrait {


    /** @var list<ScriptInterface> */
    private array $rScripts = [];

    /** @var list<string> */
    private array $rCssUris = [];

    /** @var list<string> */
    private array $rHeaders = [];


    public function addCssUri( string $i_stCssUri ) : void {
        $this->rCssUris[] = $i_stCssUri;
    }


    public function addHeader( string $i_stHeader, ?string $i_nstValue = null ) : void {
        if ( is_string( $i_nstValue ) ) {
            $i_stHeader .= ': ' . $i_nstValue;
        }
        $this->rHeaders[] = $i_stHeader;
    }


    public function addScript( ScriptInterface $i_script ) : void {
        $this->rScripts[] = $i_script;
    }


    public function addScriptUri( string $i_stScriptUri ) : void {
        $this->rScripts[] = new ScriptUri( $i_stScriptUri );
    }


    public function cssUris() : iterable {
        return $this->rCssUris;
    }


    public function headers() : iterable {
        return $this->rHeaders;
    }


    public function scripts() : iterable {
        return $this->rScripts;
    }


}