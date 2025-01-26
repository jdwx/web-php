<?php


declare( strict_types = 1 );


use JDWX\Web\Framework\StaticShim;
use JDWX\Web\Http;
use JDWX\Web\Request;
use Shims\MyStaticShim;
use Shims\MyTestCase;


require_once __DIR__ . '/Shims/MyStaticShim.php';
require_once __DIR__ . '/Shims/MyTestCase.php';


final class StaticShimTest extends MyTestCase {


    public function testAddStaticUriForAuthoritative() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/example.nope' );
        $shim = new MyStaticShim( __DIR__, i_req: $req );
        self::assertFalse( $shim->handleStatic() );
        $shim->addStaticUri( '/' );
        ob_start();
        $bResult = $shim->handleStatic();
        ob_end_clean();
        self::assertTrue( $bResult );
        self::assertSame( 404, Http::getResponseCode() );
    }


    public function testRunForBogus() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/no/such/file' );
        $shim = new StaticShim( __DIR__, i_req: $req );
        self::assertFalse( $shim->run() );
    }


    public function testRunForBogusAuthoritative() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/no/such/file' );
        $shim = new StaticShim( __DIR__, i_req: $req );
        $shim->addStaticUri( '/' );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( '404', $st );
        self::assertStringContainsString( 'Not Found', $st );
    }


    public function testRunForDirectory() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/' );
        $shim = new StaticShim( __DIR__ . '/../example/static/', i_req: $req );
        self::assertFalse( $shim->run() );
    }


    public function testRunForDirectoryAuthoritative() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/static' );
        $shim = new StaticShim( __DIR__ . '/../example/', i_req: $req );
        $shim->addStaticUri( '/' );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertSame( 403, Http::getResponseCode() );
        self::assertStringContainsString( '403', $st );
        self::assertStringContainsString( 'Access Denied', $st );
    }


    public function testRunForExcludedDirectory() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/exclude/exclude.txt' );
        $shim = new StaticShim( __DIR__ . '/../example/static/', i_req: $req );
        self::assertTrue( $shim->run() );
        $shim->excludeStaticPath( '/exclude' );
        self::assertFalse( $shim->run() );
    }


    public function testRunForMappedUri() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/decoy/alias.txt' );
        $shim = new StaticShim( __DIR__ . '/../example/', i_req: $req );
        $shim->addStaticMap( '/decoy/', __DIR__ . '/../example/static/alias/' );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( 'aliased example', $st );
    }


    public function testRunForMultiViews() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/example2' );
        $shim = new StaticShim( __DIR__ . '/../example/static/', i_req: $req );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( 'This is also a test.', $st );
    }


    public function testRunForNotFound() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/example.exe' );
        $shim = new StaticShim( __DIR__, i_req: $req );
        self::assertFalse( $shim->run() );
    }


    public function testRunForNotFoundAuthoritative() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/example.exe' );
        $shim = new StaticShim( __DIR__, i_req: $req );
        $shim->addStaticUri( '/' );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( '404', $st );
        self::assertStringContainsString( 'Not Found', $st );
    }


    public function testRunForOutsideStatic() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/exclude/exclude.txt' );
        $shim = new StaticShim( __DIR__ . '/../example/static/', i_req: $req );
        $shim->addStaticUri( '/alias/' );
        self::assertFalse( $shim->run() );
    }


    public function testRunForPHP() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/example.php' );
        $shim = new StaticShim( __DIR__ . '/../example/static/', i_req: $req );
        self::assertFalse( $shim->run() );
    }


    public function testRunForSuccess() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/example.txt' );
        $shim = new StaticShim( __DIR__ . '/../example/static/', i_req: $req );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( 'This is a test.', $st );
    }


    public function testRunForUnknownType() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/example3.wtf' );
        $shim = new StaticShim( __DIR__ . '/../example/static/', i_req: $req );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertSame( 200, Http::getResponseCode() );
        self::assertStringContainsString( 'text file', $st );
    }


}
