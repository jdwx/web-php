<?php


declare( strict_types = 1 );


namespace Shims;


use JDWX\Web\Panels\BodyPanelTrait;
use JDWX\Web\Panels\PanelInterface;


class MyBodyPanel implements PanelInterface {


    use BodyPanelTrait;


    public string $stHead = '';

    public string $stBodyEarly = '';

    public string $stBody = '';

    public string $stBodyLate = '';

    public bool $bFirst = false;

    public bool $bLast = false;


    /**
     * @inheritDoc
     */
    public function body() : iterable|string {
        return $this->stBody;
    }


    public function bodyEarly() : iterable|string {
        return $this->stBodyEarly;
    }


    public function bodyLate() : iterable|string {
        return $this->stBodyLate;
    }


    public function first() : void {
        $this->bFirst = true;
    }


    public function head() : iterable|string {
        return $this->stHead;
    }


    public function last() : void {
        $this->bLast = true;
    }


}