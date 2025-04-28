<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


trait ScriptListTrait {


    /** @var list<ScriptInterface> */
    private array $rScripts = [];


    public function addScript( ScriptInterface $i_script ) : void {
        $this->rScripts[] = $i_script;
    }


    public function addScriptBody( string $i_stBody ) : void {
        $this->addScript( new ScriptBody( $i_stBody ) );
    }


    public function addScriptUri( string $i_stScriptUri ) : void {
        $this->addScript( new ScriptUri( $i_stScriptUri ) );
    }


    /** @return iterable<ScriptInterface> */
    public function scriptList() : iterable {
        return $this->rScripts;
    }


}