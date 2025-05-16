<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use Stringable;


/**
 * Panels represent chunks or modules that appear on an HTML page.
 * They are designed to be self-contained.
 */
interface PanelInterface {


    /** @return iterable<string|Stringable>|string|Stringable */
    public function body() : iterable|string|Stringable;


    /** @return iterable<string|Stringable>|string|Stringable */
    public function bodyEarly() : iterable|string|Stringable;


    /** @return iterable<string|Stringable>|string|Stringable */
    public function bodyLate() : iterable|string|Stringable;


    /** @return iterable<CssInterface> */
    public function cssList() : iterable;


    public function first() : void;


    /** @return iterable<string|Stringable>|string|Stringable */
    public function head() : iterable|string|Stringable;


    /** @return iterable<string|Stringable> */
    public function headerList() : iterable;


    public function last() : void;


    /** @return iterable<ScriptInterface> */
    public function scriptList() : iterable;


}