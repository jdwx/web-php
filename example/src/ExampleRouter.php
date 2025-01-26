<?php


declare( strict_types = 1 );


namespace JDWX\Web\Example;


use JDWX\Web\Framework\AbstractRouter;
use JDWX\Web\SimpleHtmlPage;


class ExampleRouter extends AbstractRouter {


    public function route() : bool {
        switch ( $this->path() ) {
            case '/':
                $this->home();
                return true;
            case '/add':
                $this->add();
                return true;

        }
        return false;
    }


    private function add() : void {
        $this->assertPOST();
        $req = $this->request();
        $num1 = $req->postEx( 'num1' )->asFloat();
        $num2 = $req->postEx( 'num2' )->asFloat();
        $sum = $num1 + $num2;
        echo $sum;
    }


    private function home() : void {
        $this->assertGET();
        $page = new SimpleHtmlPage();
        $page->addCSS( '/example.css' );
        $page->setTitle( 'Example Web Application' );
        $page->setContent(
            "<p>Hello, world!</p>\n"
            . "<p></p><input type=\"number\" id=\"num1\" value=\"0\"> + \n"
            . "<input type=\"number\" id=\"num2\" value=\"0\"> = \n"
            . "<span id=\"sum\">???</span></p>\n"
            . "<button id=\"add\">Add</button>\n"
            . "<script src=\"/example.js\"></script>\n"
            . "</body>\n"
        );
        echo $page->render();
    }


}
