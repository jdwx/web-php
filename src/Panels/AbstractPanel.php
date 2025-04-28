<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


abstract class AbstractPanel implements PanelInterface {


    public function bodyEarly() : iterable|string {
        return '';
    }


    public function bodyLate() : iterable|string {
        return '';
    }


    /** @return iterable<string> */
    public function cssUris() : iterable {
        return [];
    }


    public function first() : void {
    }


    public function head() : iterable|string {
        return '';
    }


    /** @return iterable<string> */
    public function headers() : iterable {
        return [];
    }


    public function last() : void {
    }


    /** @return iterable<ScriptInterface> */
    public function scripts() : iterable {
        return [];
    }


}