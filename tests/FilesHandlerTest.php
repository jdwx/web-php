<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use InvalidArgumentException;
use JDWX\Web\Backends\MockFilesBackend;
use JDWX\Web\FilesHandler;
use JDWX\Web\SafetyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;


#[CoversClass( FilesHandler::class )]
final class FilesHandlerTest extends TestCase {


    public function testError() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( UPLOAD_ERR_OK, $fh->error( 'foo' ) );
    }


    public function testErrorString() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_NO_FILE,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'UPLOAD_ERR_NO_FILE', $fh->errorString( 'foo' ) );
    }


    public function testErrorStringForCantWrite() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_CANT_WRITE,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'UPLOAD_ERR_CANT_WRITE', $fh->errorString( 'foo' ) );
    }


    public function testErrorStringForExtension() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_EXTENSION,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'UPLOAD_ERR_EXTENSION', $fh->errorString( 'foo' ) );
    }


    public function testErrorStringForNoTmp() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_NO_TMP_DIR,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'UPLOAD_ERR_NO_TMP_DIR', $fh->errorString( 'foo' ) );
    }


    public function testFetchAsString() : void {
        $stContent = 'test-content';
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/foo.txt',
        ], 'bar' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'bar.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/bar.txt',
        ] ];
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', $stContent );
        $be->addUploadedFile( '/tmp/bar.txt', $stContent );
        $fh = new FilesHandler( $rFiles, $be );
        self::assertSame( $stContent, $fh->fetchAsString( 'foo' ) );

    }


    public function testFetchAsStringForBogusTmpName() : void {
        $rFiles = [ 'baz' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'baz.txt',
            'size' => 10,
            'tmp_name' => '/tmp/baz.txt',
        ] ];
        $be = new MockFilesBackend();
        $fh = new FilesHandler( $rFiles, $be );
        $this->expectException( RuntimeException::class );
        $fh->fetchAsString( 'baz' );
    }


    public function testFetchAsStringForReadFailure() : void {
        $stContent = 'test-content';
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', $stContent );
        $be->bFailToReadFile = true;
        $fh = new FilesHandler( [
            'foo' => [
                'error' => UPLOAD_ERR_OK,
                'name' => 'foo.txt',
                'size' => strlen( $stContent ),
                'tmp_name' => '/tmp/foo.txt',
            ],
        ], $be );
        $this->expectException( RuntimeException::class );
        $fh->fetchAsString( 'foo' );
    }


    public function testHas() : void {

        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertTrue( $fh->has( 'foo' ) );
        self::assertFalse( $fh->has( 'bar' ) );

        $rFiles = [ 'foo' => [
            'error' => [ UPLOAD_ERR_OK ],
            'name' => [ 'foo.txt' ],
        ], 'bar' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'bar.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertFalse( $fh->has( 'foo' ) );
        self::assertTrue( $fh->has( 'foo', 0 ) );
        self::assertFalse( $fh->has( 'foo', 1 ) );
        self::assertFalse( $fh->has( 'bar', 0 ) );
    }


    public function testHasForNoFile() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_NO_FILE,
            'name' => '',
            'full_path' => '',
            'type' => '',
            'tmp_name' => '',
            'size' => 0,
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertFalse( $fh->has( 'foo' ) );
    }


    /** @suppress PhanDeprecatedFunction */
    public function testMove() : void {
        $stContent = 'test-content';
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/foo.txt',
        ] ];
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', $stContent );
        $fh = new FilesHandler( $rFiles, $be );
        /** @noinspection PhpDeprecationInspection */
        $fh->move( 'foo', '/tmp/bar.txt' );
        self::assertSame( $stContent, $be->fileGetContentsEx( '/tmp/bar.txt' ) );
        $be->bFailToMoveUpload = true;
        $this->expectException( RuntimeException::class );
        /** @noinspection PhpDeprecationInspection */
        $fh->move( 'foo', '/tmp/baz.txt' );
    }


    public function testMoveSafely() : void {
        $stContent = 'test-content';
        $dir = $this->makeTempDir();
        try {
            $rFiles = [ 'foo' => [
                'error' => UPLOAD_ERR_OK,
                'name' => 'foo.txt',
                'size' => strlen( $stContent ),
                'tmp_name' => '/tmp/foo.txt',
            ] ];
            $be = $this->makeDiskWritingBackend();
            $be->addUploadedFile( '/tmp/foo.txt', $stContent );
            $fh = new FilesHandler( $rFiles, $be );

            $stResult = $fh->moveSafely( 'foo', $dir, 'final.txt' );
            self::assertSame( realpath( $dir . '/final.txt' ), $stResult );
            self::assertFileExists( $dir . '/final.txt' );
            self::assertSame( $stContent, file_get_contents( $dir . '/final.txt' ) );
        } finally {
            $this->removeTempDir( $dir );
        }
    }


    public function testMoveSafelyWithRandomFilename() : void {
        $stContent = 'test-content';
        $dir = $this->makeTempDir();
        try {
            $rFiles = [ 'foo' => [
                'error' => UPLOAD_ERR_OK,
                'name' => 'client-supplied-name.txt',
                'size' => strlen( $stContent ),
                'tmp_name' => '/tmp/foo.txt',
            ] ];
            $be = $this->makeDiskWritingBackend();
            $be->addUploadedFile( '/tmp/foo.txt', $stContent );
            $fh = new FilesHandler( $rFiles, $be );

            $stResult = $fh->moveSafely( 'foo', $dir );
            # The returned path must live inside the requested dir, and the
            # filename must be random (not the client-supplied one).
            self::assertStringStartsWith( realpath( $dir ) . '/', $stResult );
            self::assertFileExists( $stResult );
            self::assertMatchesRegularExpression( '#/[0-9a-f]{32}$#', $stResult );
            self::assertStringNotContainsString( 'client-supplied-name', $stResult );
        } finally {
            $this->removeTempDir( $dir );
        }
    }


    public function testMoveSafelyRejectsNonexistentDir() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => 4,
            'tmp_name' => '/tmp/foo.txt',
        ] ];
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', 'test' );
        $fh = new FilesHandler( $rFiles, $be );
        $this->expectException( InvalidArgumentException::class );
        $fh->moveSafely( 'foo', '/nonexistent/directory/that/does/not/exist', 'final.txt' );
    }


    public function testMoveSafelyRejectsSlashInFilename() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => 4,
            'tmp_name' => '/tmp/foo.txt',
        ] ];
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', 'test' );
        $fh = new FilesHandler( $rFiles, $be );
        $this->expectException( SafetyException::class );
        $fh->moveSafely( 'foo', sys_get_temp_dir(), '../../etc/passwd' );
    }


    public function testMoveSafelyRejectsBackslashInFilename() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => 4,
            'tmp_name' => '/tmp/foo.txt',
        ] ];
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', 'test' );
        $fh = new FilesHandler( $rFiles, $be );
        $this->expectException( SafetyException::class );
        $fh->moveSafely( 'foo', sys_get_temp_dir(), 'bad\\name.txt' );
    }


    public function testMoveSafelyRejectsNullByteInFilename() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => 4,
            'tmp_name' => '/tmp/foo.txt',
        ] ];
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', 'test' );
        $fh = new FilesHandler( $rFiles, $be );
        try {
            $fh->moveSafely( 'foo', sys_get_temp_dir(), "bad\0name.txt" );
            self::fail( 'Expected SafetyException.' );
        } catch ( SafetyException $e ) {
            # The null byte must be escaped in the exception message to prevent
            # log truncation.
            self::assertStringNotContainsString( "\0", $e->getMessage() );
            self::assertStringContainsString( '\\0', $e->getMessage() );
        }
    }


    public function testMoveSafelyThrowsWhenFileIsMissingAfterMove() : void {
        # The stock MockFilesBackend records the move in memory but does not
        # write to disk — so realpath() fails and moveSafely() must throw.
        $dir = $this->makeTempDir();
        try {
            $rFiles = [ 'foo' => [
                'error' => UPLOAD_ERR_OK,
                'name' => 'foo.txt',
                'size' => 4,
                'tmp_name' => '/tmp/foo.txt',
            ] ];
            $be = new MockFilesBackend();
            $be->addUploadedFile( '/tmp/foo.txt', 'test' );
            $fh = new FilesHandler( $rFiles, $be );
            $this->expectException( RuntimeException::class );
            $fh->moveSafely( 'foo', $dir, 'final.txt' );
        } finally {
            $this->removeTempDir( $dir );
        }
    }


    public function testName() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'foo.txt', $fh->name( 'foo' ) );

        $rFiles = [ 'foo' => [
            'error' => [ UPLOAD_ERR_OK ],
            'name' => [ 'foo.txt' ],
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'foo.txt', $fh->name( 'foo', 0 ) );

        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
        ] ];
        $fh = new FilesHandler( $rFiles );
        $this->expectException( RuntimeException::class );
        $fh->name( 'foo' );
    }


    public function testSize() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => 123,
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 123, $fh->size( 'foo' ) );
    }


    public function testTmpName() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'tmp_name' => '/tmp/foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( '/tmp/foo.txt', $fh->tmpName( 'foo' ) );
    }


    public function testType() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'type' => 'text/plain',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'text/plain', $fh->type( 'foo' ) );
    }


    public function testTypeForNoFile() : void {
        $fh = new FilesHandler( [] );
        $this->expectException( RuntimeException::class );
        $fh->type( 'foo' );
    }


    public function testTypeForUnexpectedMultipleFiles() : void {
        $rFiles = [ 'foo' => [
            'error' => [ UPLOAD_ERR_OK ],
            'name' => [ 'foo.txt' ],
            'type' => [ 'text/plain' ],
        ] ];
        $fh = new FilesHandler( $rFiles );
        $this->expectException( RuntimeException::class );
        $fh->type( 'foo' );
    }


    public function testTypeForUnexpectedOneFile() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'type' => 'text/plain',
        ] ];
        $fh = new FilesHandler( $rFiles );
        $this->expectException( RuntimeException::class );
        $fh->type( 'foo', 0 );
    }


    private function makeDiskWritingBackend() : MockFilesBackend {
        # Subclass that mirrors moves to the real filesystem so realpath() works
        # in moveSafely(). The parent class records the move in memory; we then
        # write the content to disk.
        return new class extends MockFilesBackend {


            public function moveUploadedFile( string $i_stFrom, string $i_stTo ) : bool {
                if ( ! isset( $this->rUploadedFiles[ $i_stFrom ] ) ) {
                    return false;
                }
                $stContent = $this->rUploadedFiles[ $i_stFrom ];
                if ( ! parent::moveUploadedFile( $i_stFrom, $i_stTo ) ) {
                    return false;
                }
                file_put_contents( $i_stTo, $stContent );
                return true;
            }


        };
    }


    private function makeTempDir() : string {
        $dir = sys_get_temp_dir() . '/jdwx-web-test-' . bin2hex( random_bytes( 8 ) );
        mkdir( $dir );
        return $dir;
    }


    private function removeTempDir( string $dir ) : void {
        foreach ( glob( $dir . '/*' ) ?: [] as $stPath ) {
            unlink( $stPath );
        }
        rmdir( $dir );
    }


    /** @suppress PhanDeprecatedFunction */
    public function testValidate() : void {
        $stContent = 'test-content';
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/foo.txt',
        ], 'baz' => [
            'error' => UPLOAD_ERR_NO_FILE,
            'name' => '',
        ], 'qux' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'qux.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/qux.txt',
        ], 'quux' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'quux.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/quux.txt',
        ] ];
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', $stContent );
        $be->addUploadedFile( '/tmp/quux.txt', $stContent );
        $fh = new FilesHandler( $rFiles, $be );
        self::assertTrue( $fh->validate( 'foo' ) );
        /** @noinspection PhpDeprecationInspection */
        $fh->move( 'foo', '/tmp/foo-moved.txt' );
        self::assertTrue( $be->fileExists( '/tmp/foo-moved.txt' ) );

        # File does not exist at all.
        self::assertFalse( $fh->validate( 'bar' ) );

        # File upload error.
        self::assertFalse( $fh->validate( 'baz' ) );

        # Fake tmp_name.
        self::assertFalse( $fh->validate( 'qux' ) );

        # Temp file has gone missing.
        self::assertTrue( $fh->has( 'quux' ) );
        self::assertSame( UPLOAD_ERR_OK, $fh->error( 'quux' ) );
        self::assertSame( '/tmp/quux.txt', $fh->tmpName( 'quux' ) );
        self::assertTrue( $be->isUploadedFile( '/tmp/quux.txt' ) );
        $be->bFailFileExists = true;
        self::assertFalse( $fh->validate( 'quux' ) );

    }


}
