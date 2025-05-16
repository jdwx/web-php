<?php


declare( strict_types = 1 );


namespace Shims;


use JDWX\Web\Panels\BodyPanelTrait;
use JDWX\Web\Panels\PanelInterface;
use Stringable;


class MyBodyPanel implements PanelInterface {


    use BodyPanelTrait;


    public string $stHead = '';

    public string $stBodyEarly = '';

    public string $stBody = '';

    public string $stBodyLate = '';

    public bool $bFirst = false;

    public bool $bLast = false;

    /** @var ?callable */
    public $fnFirst = null;

    /** @var ?callable */
    public $fnLast = null;


    /** @return iterable<string|Stringable>|string|Stringable */
    public function body() : iterable|string|Stringable {
        return $this->stBody;
    }


    /** @return iterable<string|Stringable>|string|Stringable */
    public function bodyEarly() : iterable|string|Stringable {
        return $this->stBodyEarly;
    }


    /** @return iterable<string|Stringable>|string|Stringable */
    public function bodyLate() : iterable|string|Stringable {
        return $this->stBodyLate;
    }


    public function first() : void {
        $this->bFirst = true;
        if ( is_callable( $this->fnFirst ) ) {
            ( $this->fnFirst )();
        }
    }


    /** @return iterable<string|Stringable>|string|Stringable */
    public function head() : iterable|string|Stringable {
        return $this->stHead;
    }


    public function last() : void {
        $this->bLast = true;
        if ( is_callable( $this->fnLast ) ) {
            ( $this->fnLast )();
        }
    }


}