<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Param\ParameterSet;
use JDWX\Web\AbstractRequest;
use JDWX\Web\Backends\MockServer;
use JDWX\Web\FilesHandler;
use JDWX\Web\ServerInterface;
use JDWX\Web\UrlParts;
use JsonException;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractRequest::class )]
final class AbstractRequestTest extends TestCase {


    public function testBody() : void {
        $req = $this->newAbstractRequest();
        self::assertNull( $req->body() );

        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ) );
        self::assertSame( '', $req->body() );

        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: 'test body' );
        self::assertSame( 'test body', $req->body() );
    }


    public function testBodyEx() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ) );
        self::assertSame( '', $req->body() );

        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: 'test body' );
        self::assertSame( 'test body', $req->bodyEx() );

        $req = $this->newAbstractRequest();
        $this->expectException( OutOfBoundsException::class );
        $req->bodyEx();
    }


    public function testBodyJson() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: '{"foo":"bar","baz":1}' );
        $data = $req->bodyJson();
        assert( is_array( $data ) );
        self::assertSame( 'bar', $data[ 'foo' ] );
        self::assertSame( 1, $data[ 'baz' ] );

        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: '"text"' );
        self::assertSame( 'text', $req->bodyJson() );

        $req = $this->newAbstractRequest();
        self::assertNull( $req->bodyJson() );

        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: 'not json' );
        $this->expectException( JsonException::class );
        $req->bodyJson();
    }


    public function testBodyJsonArray() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: '{"foo":"bar","baz":1}' );
        $data = $req->bodyJsonArray();
        assert( is_array( $data ) );
        self::assertSame( 'bar', $data[ 'foo' ] );
        self::assertSame( 1, $data[ 'baz' ] );
    }


    public function testBodyJsonArrayEx() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: '{"foo":"bar","baz":1}' );
        $data = $req->bodyJsonArrayEx();
        self::assertSame( 'bar', $data[ 'foo' ] );
        self::assertSame( 1, $data[ 'baz' ] );

        $req = $this->newAbstractRequest();
        $this->expectException( OutOfBoundsException::class );
        $req->bodyJsonArrayEx();
    }


    public function testBodyJsonArrayForGET() : void {
        $req = $this->newAbstractRequest();
        self::assertNull( $req->bodyJsonArray() );
    }


    public function testBodyJsonArrayForNullValue() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: 'null' );
        $this->expectException( JsonException::class );
        $req->bodyJsonArray();
    }


    public function testBodyJsonArrayForStringValue() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: '"text"' );
        $this->expectException( JsonException::class );
        $req->bodyJsonArray();
    }


    public function testBodyJsonEx() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: '{"foo":"bar","baz":1}' );
        $data = $req->bodyJsonEx();
        assert( is_array( $data ) );
        self::assertSame( 'bar', $data[ 'foo' ] );
        self::assertSame( 1, $data[ 'baz' ] );

        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: '"text"' );
        self::assertSame( 'text', $req->bodyJsonEx() );

        $req = $this->newAbstractRequest();
        $this->expectException( OutOfBoundsException::class );
        $req->bodyJsonEx();
    }


    public function testBodyJsonExForInvalid() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ),
            i_stBody: 'not json' );
        $this->expectException( JsonException::class );
        $req->bodyJsonEx();
    }


    public function testCOOKIE() : void {
        $req = $this->newAbstractRequest( i_rCookie: [ 'foo' => 'bar', 1 => 'baz' ] );
        self::assertSame( 'bar', $req->COOKIE( 'foo' )?->asString() );
        self::assertSame( 'baz', $req->COOKIE( '1' )?->asString() );
        self::assertSame( 'quux', $req->COOKIE( 'qux', 'quux' )?->asString() );
        self::assertNull( $req->COOKIE( 'bar' ) );
    }


    public function testCookieEx() : void {
        $req = $this->newAbstractRequest( i_rCookie: [ 'foo' => 'bar' ] );
        self::assertSame( 'bar', $req->cookieEx( 'foo' )->asString() );
        self::assertSame( 'quux', $req->cookieEx( 'qux', 'quux' )->asString() );
        $this->expectException( OutOfBoundsException::class );
        $req->cookieEx( 'bar' );
    }


    public function testCookieHas() : void {
        $req = $this->newAbstractRequest( i_rCookie: [ 'foo' => 'bar' ] );
        self::assertTrue( $req->cookieHas( 'foo' ) );
        self::assertFalse( $req->cookieHas( 'bar' ) );
        self::assertFalse( $req->cookieHas( 'foo', 'bar' ) );
    }


    public function testFILES() : void {
        $req = $this->newAbstractRequest( i_rFiles: [ 'foo' => [ 'name' => 'bar' ] ] );
        self::assertTrue( $req->FILES()->has( 'foo' ) );
    }


    public function testGET() : void {
        $req = $this->newAbstractRequest( [ 'foo' => 'bar', 1 => 'baz' ] );
        self::assertSame( 'bar', $req->GET( 'foo' )?->asString() );
        self::assertSame( 'baz', $req->GET( '1' )?->asString() );
        self::assertSame( 'quux', $req->GET( 'qux', 'quux' )?->asString() );
        self::assertNull( $req->GET( 'qux' ) );
    }


    public function testGetEx() : void {
        $req = $this->newAbstractRequest( [ 'foo' => 'bar' ] );
        self::assertSame( 'bar', $req->getEx( 'foo' )->asString() );
        self::assertSame( 'quux', $req->getEx( 'qux', 'quux' )->asString() );
        $this->expectException( OutOfBoundsException::class );
        $req->getEx( 'baz' );
    }


    public function testGetHas() : void {
        $req = $this->newAbstractRequest( [ 'foo' => 'bar' ] );
        self::assertTrue( $req->getHas( 'foo' ) );
        self::assertFalse( $req->getHas( 'bar' ) );
        self::assertFalse( $req->getHas( 'foo', 'bar' ) );
    }


    public function testIsGET() : void {
        $req = $this->newAbstractRequest();
        self::assertTrue( $req->isGET() );
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ) );
        self::assertFalse( $req->isGET() );
    }


    public function testIsHEAD() : void {
        $req = $this->newAbstractRequest(
            i_server: ( new MockServer() )->withRequestMethod( 'HEAD' )
        );
        self::assertTrue( $req->isHEAD() );

        $req = $this->newAbstractRequest(
            i_server: ( new MockServer() )->withRequestMethod( 'Head' )
        );
        self::assertFalse( $req->isHEAD() );

        $req = $this->newAbstractRequest(
            i_server: ( new MockServer() )->withRequestMethod( 'head' )
        );
        self::assertFalse( $req->isHEAD() );

        $req = $this->newAbstractRequest(
            i_server: ( new MockServer() )->withRequestMethod( 'POST' )
        );
        self::assertFalse( $req->isHEAD() );
    }


    public function testIsPOST() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'POST' ) );
        self::assertTrue( $req->isPOST() );

        $req = $this->newAbstractRequest();
        self::assertFalse( $req->isPOST() );
    }


    public function testMethod() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestMethod( 'TEST_METHOD' ) );
        self::assertSame( 'TEST_METHOD', $req->method() );
    }


    public function testPOST() : void {
        $req = $this->newAbstractRequest( i_rPost: [ 'foo' => 'bar', 1 => 'baz' ] );
        self::assertSame( 'bar', $req->POST( 'foo' )?->asString() );
        self::assertSame( 'baz', $req->POST( '1' )?->asString() );
        self::assertSame( 'quux', $req->POST( 'qux', 'quux' )?->asString() );
        self::assertNull( $req->POST( 'bar' ) );
    }


    public function testParent() : void {
        $srv = ( new MockServer() )->withRequestUri( 'https://www.example.com/foo/bar' );
        $req = $this->newAbstractRequest( i_server: $srv );
        self::assertSame( 'https://www.example.com/foo', $req->parent() );
    }


    public function testParentPath() : void {
        $srv = ( new MockServer() )->withRequestUri( 'https://www.example.com/foo/bar' );
        $req = $this->newAbstractRequest( i_server: $srv );
        self::assertSame( '/foo', $req->parentPath() );

    }


    public function testPath() : void {
        $req = $this->newAbstractRequest(
            i_server: ( new MockServer() )->withRequestUri( '/foo/bar?a=b&c=d' )
        );
        self::assertSame( '/foo/bar', $req->path() );
    }


    public function testPostEx() : void {
        $req = $this->newAbstractRequest( i_rPost: [ 'foo' => 'bar' ] );
        self::assertSame( 'bar', $req->postEx( 'foo' )->asString() );

        self::assertSame( 'quux', $req->postEx( 'qux', 'quux' )->asString() );

        $this->expectException( OutOfBoundsException::class );
        $req->postEx( 'bar' );
    }


    public function testPostHas() : void {
        $req = $this->newAbstractRequest( i_rPost: [ 'foo' => 'bar' ] );
        self::assertTrue( $req->postHas( 'foo' ) );
        self::assertFalse( $req->postHas( 'bar' ) );
        self::assertFalse( $req->postHas( 'foo', 'bar' ) );
    }


    public function testReferer() : void {
        $srv = MockServer::new()->withHttpReferer( null );
        $req = $this->newAbstractRequest( i_server: $srv );
        self::assertNull( $req->refererParts() );

        $srv = MockServer::new()->withHttpReferer( 'https://www.example.com/foo/bar' );
        $req = $this->newAbstractRequest( i_server: $srv );
        self::assertSame( 'https://www.example.com/foo/bar', $req->referer() );
    }


    public function testRefererEx() : void {
        $srv = MockServer::new()->withHttpReferer( 'https://www.example.com/foo/bar' );
        $req = $this->newAbstractRequest( i_server: $srv );
        self::assertSame( 'https://www.example.com/foo/bar', $req->refererEx() );

        $srv = MockServer::new()->withHttpReferer( null );
        $req = $this->newAbstractRequest( i_server: $srv );
        $this->expectException( OutOfBoundsException::class );
        $req->refererEx();

    }


    public function testRefererParts() : void {
        $srv = MockServer::new()->withHttpReferer( null );
        $req = $this->newAbstractRequest( i_server: $srv );
        self::assertNull( $req->referer() );

        $srv = MockServer::new()->withHttpReferer( 'https://www.example.com/foo/bar' );
        $req = $this->newAbstractRequest( i_server: $srv );
        $parts = $req->refererParts();
        assert( $parts instanceof UrlParts );
        self::assertSame( 'https', $parts->nstScheme );
        self::assertSame( 'www.example.com', $parts->nstHost );
        self::assertSame( '/foo/bar', $parts->path() );
    }


    public function testRefererPartsEx() : void {
        $srv = MockServer::new()->withHttpReferer( 'https://www.example.com/foo/bar' );
        $req = $this->newAbstractRequest( i_server: $srv );
        $parts = $req->refererPartsEx();
        self::assertSame( 'https', $parts->nstScheme );
        self::assertSame( 'www.example.com', $parts->nstHost );
        self::assertSame( '/foo/bar', $parts->path() );

        $srv = MockServer::new()->withHttpReferer( null );
        $req = $this->newAbstractRequest( i_server: $srv );
        $this->expectException( OutOfBoundsException::class );
        $req->refererPartsEx();
    }


    public function testServer() : void {
        $srv = new MockServer();
        $req = $this->newAbstractRequest( i_server: $srv );
        self::assertSame( $srv, $req->server() );
    }


    public function testUri() : void {
        $req = $this->newAbstractRequest(
            i_server: ( new MockServer() )->withRequestUri( '/foo/bar?a=b&c=d' )
        );
        self::assertSame( '/foo/bar?a=b&c=d', $req->uri() );
    }


    public function testUriParts() : void {
        $req = $this->newAbstractRequest(
            i_server: ( new MockServer() )->withRequestUri( '/foo/bar/baz?a=b&c=d' )
        );
        $parts = $req->uriParts();
        self::assertSame( [ 'foo', 'bar' ], $parts->subFolders );
        self::assertSame( 'baz', $parts->nstFile );
        self::assertSame( 'b', $parts[ 'a' ] );
        self::assertSame( 'd', $parts[ 'c' ] );
    }


    public function testValidateUri() : void {
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestUri( '/test/' ) );
        self::assertTrue( $req->validateUri() );

        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestUri( '/test/this' ) );
        self::assertTrue( $req->validateUri() );

        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestUri( '/test/..' ) );
        self::assertFalse( $req->validateUri() );

        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestUri( '/te%st/this' ) );
        self::assertFalse( $req->validateUri() );

        /** @noinspection JSDeprecatedSymbols */
        $req = $this->newAbstractRequest( i_server: ( new MockServer() )->withRequestUri( '/:<script>alert(document.domain)</script>' ) );
        self::assertFalse( $req->validateUri() );
    }


    public function testXCOOKIE() : void {
        $req = $this->newAbstractRequest( i_rCookie: [ 'foo' => 'bar' ] );
        self::assertTrue( $req->_COOKIE()->has( 'foo' ) );
        self::assertFalse( $req->_COOKIE()->has( 'bar' ) );
    }


    public function testXGET() : void {
        $req = $this->newAbstractRequest( [ 'foo' => 'bar' ] );
        self::assertTrue( $req->_GET()->has( 'foo' ) );
        self::assertFalse( $req->_GET()->has( 'baz' ) );
    }


    public function testXPOST() : void {
        $req = $this->newAbstractRequest( i_rPost: [ 'foo' => 'bar' ] );
        self::assertTrue( $req->_POST()->has( 'foo' ) );
        self::assertFalse( $req->_POST()->has( 'baz' ) );
    }


    /**
     * @param array<int|string, string|list<string>> $i_rGet
     * @param array<int|string, string|list<string>> $i_rPost
     * @param array<int|string, string> $i_rCookie
     * @param mixed[] $i_rFiles
     * @param ServerInterface|null $i_server
     * @param string $i_stBody
     * @return AbstractRequest
     */
    private function newAbstractRequest( array            $i_rGet = [], array $i_rPost = [],
                                         array            $i_rCookie = [], array $i_rFiles = [],
                                         ?ServerInterface $i_server = null,
                                         string           $i_stBody = '' ) : AbstractRequest {
        $setGet = new ParameterSet( $i_rGet );
        $setPost = new ParameterSet( $i_rPost );
        $setCookie = new ParameterSet( $i_rCookie );
        $files = new FilesHandler( $i_rFiles );
        $srv = $i_server ?? MockServer::new();
        return new readonly class( $setGet, $setPost, $setCookie, $files, $srv, $i_stBody )
            extends AbstractRequest {


            public function __construct( ParameterSet    $setGet,
                                         ParameterSet    $setPost,
                                         ParameterSet    $setCookie,
                                         FilesHandler    $files,
                                         ServerInterface $server,
                                         private string  $stBody ) {
                parent::__construct( $setGet, $setPost, $setCookie, $files, $server );
            }


            protected function fetchInput() : string {
                return $this->stBody;
            }


        };
    }


}
