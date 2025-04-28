<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


/**
 * Panels represent chunks or modules that appear on an HTML page.
 * They are designed to be self-contained.
 */
interface PanelInterface {


    /** @return iterable<string>|string */
    public function body() : iterable|string;


    /** @return iterable<string>|string */
    public function bodyEarly() : iterable|string;


    /** @return iterable<string>|string */
    public function bodyLate() : iterable|string;


    /** @return iterable<CssInterface> */
    public function cssList() : iterable;


    public function first() : void;


    /** @return iterable<string>|string */
    public function head() : iterable|string;


    /** @return iterable<string> */
    public function headerList() : iterable;


    public function last() : void;


    /** @return iterable<ScriptInterface> */
    public function scriptList() : iterable;


}