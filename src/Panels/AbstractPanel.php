<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


abstract class AbstractPanel implements PanelInterface {


    use BodyStubTrait;


    /** @return iterable<CssInterface> */
    public function cssList() : iterable {
        return [];
    }


    /** @return iterable<string> */
    public function headerList() : iterable {
        return [];
    }


    /** @return iterable<ScriptInterface> */
    public function scriptList() : iterable {
        return [];
    }


}