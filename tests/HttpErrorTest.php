<?php


declare( strict_types = 1 );


use PHPUnit\Framework\TestCase;
use Shims\MyHttpError;


final class HttpErrorTest extends TestCase {


    public function testCustomError() : void {
        $error = new MyHttpError( __DIR__ . '/../example/errors/error%d.php' );
        $st = $error->render( 404 );
        self::assertStringContainsString( 'This is an example 404 error page.', $st );
    }


    public function testCustomErrorNotFound() : void {
        $error = new MyHttpError( __DIR__ . '/../example/errors/error%d.php' );
        $st = $error->render( 12345 );
        self::assertStringContainsString( 'Unknown Error', $st );
    }


    public function testErrorName() : void {
        $error = new MyHttpError();
        self::assertSame( 'Not Found', $error->errorName( 404 ) );
        self::assertSame( 'TEST_ERROR', $error->errorName( 404, 'TEST_ERROR' ) );
    }


    public function testErrorText() : void {
        $error = new MyHttpError();
        self::assertSame( 'The file or resource you requested was not found.',
            $error->errorText( 404 ) );
        self::assertSame( 'TEST_TEXT', $error->errorText( 404, 'TEST_TEXT' ) );
        self::assertSame( '', $error->errorText( 12345 ) );
    }


}
