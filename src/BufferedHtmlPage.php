<?php
declare( strict_types = 1 );


namespace JDWX\Web;


abstract class BufferedHtmlPage extends HtmlPage {


    abstract protected function bufferedContent() : void;


    protected function content() : string {
        ob_start();
        $this->bufferedContent();
        return ob_get_clean();
    }


}