<?php


declare( strict_types = 1 );


use JDWX\Web\MockFilesBackend;
use PHPUnit\Framework\TestCase;


/**
 * We really can't guarantee that the live PHPFilesBackend class will do what
 * we want, but we can be darn sure that the mockup behaves as we expect so
 * that any discrepancies observed in the wild should be easier to detect.
 */
class MockFilesBackendTest extends TestCase {


    public function testFileExists() : void {
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', 'test-foo' );
        $be->addUploadedFile( '/tmp/bar.txt', 'test-bar' );
        self::assertTrue( $be->fileExists( '/tmp/foo.txt' ) );
        self::assertTrue( $be->fileExists( '/tmp/bar.txt' ) );
        self::assertFalse( $be->fileExists( '/tmp/baz.txt' ) );
        $be->moveUploadedFile( '/tmp/bar.txt', '/tmp/bar-moved.txt' );
        self::assertTrue( $be->fileExists( '/tmp/foo.txt' ) );
        self::assertFalse( $be->fileExists( '/tmp/bar.txt' ) );
        self::assertTrue( $be->fileExists( '/tmp/bar-moved.txt' ) );

        $be->bFailFileExists = true;
        self::assertFalse( $be->fileExists( '/tmp/foo.txt' ) );
    }


    public function testFileGetContents() : void {
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', 'test-foo' );
        $be->addUploadedFile( '/tmp/bar.txt', 'test-bar' );
        static::assertSame( 'test-foo', $be->fileGetContents( '/tmp/foo.txt' ) );
        static::assertSame( 'test-bar', $be->fileGetContents( '/tmp/bar.txt' ) );
        $be->moveUploadedFile( '/tmp/bar.txt', '/tmp/bar-moved.txt' );
        static::assertSame( 'test-foo', $be->fileGetContents( '/tmp/foo.txt' ) );
        static::assertFalse( $be->fileGetContents( '/tmp/bar.txt' ) );
        static::assertSame( 'test-bar', $be->fileGetContents( '/tmp/bar-moved.txt' ) );
        static::assertFalse( $be->fileGetContents( '/tmp/baz.txt' ) );

        $be->bFailToReadFile = true;
        static::assertFalse( $be->fileGetContents( '/tmp/foo.txt' ) );
    }


    public function testIsUploadedFile() : void {
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', 'test-foo' );
        $be->addUploadedFile( '/tmp/bar.txt', 'test-bar' );
        static::assertTrue( $be->isUploadedFile( '/tmp/foo.txt' ) );
        static::assertTrue( $be->isUploadedFile( '/tmp/bar.txt' ) );
        static::assertFalse( $be->isUploadedFile( '/tmp/baz.txt' ) );
        $be->removeFile( '/tmp/bar.txt' );
        static::assertTrue( $be->isUploadedFile( '/tmp/foo.txt' ) );
        static::assertFalse( $be->isUploadedFile( '/tmp/bar.txt' ) );
    }


    public function testMoveUploadedFile() : void {
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', 'test-foo' );
        $be->addUploadedFile( '/tmp/bar.txt', 'test-bar' );

        self::assertTrue( $be->moveUploadedFile( '/tmp/foo.txt', '/tmp/foo-moved.txt' ) );
        self::assertFalse( $be->moveUploadedFile( '/tmp/foo.txt', '/tmp/foo-moved.txt' ) );
        self::assertFalse( $be->moveUploadedFile( '/tmp/baz.txt', '/tmp/baz-moved.txt' ) );
        $be->bFailToMoveUpload = true;
        self::assertFalse( $be->moveUploadedFile( '/tmp/bar.txt', '/tmp/bar-moved.txt' ) );
    }


    public function testRemoveFile() : void {
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', 'test-foo' );
        $be->addUploadedFile( '/tmp/bar.txt', 'test-bar' );

        self::assertTrue( $be->fileExists( '/tmp/foo.txt' ) );
        $be->removeFile( '/tmp/foo.txt' );
        self::assertFalse( $be->fileExists( '/tmp/foo.txt' ) );

        $be->moveUploadedFileEx( '/tmp/bar.txt', '/tmp/bar-moved.txt' );
        self::assertTrue( $be->fileExists( '/tmp/bar-moved.txt' ) );
        $be->removeFile( '/tmp/bar-moved.txt' );
        self::assertFalse( $be->fileExists( '/tmp/bar-moved.txt' ) );
    }


}
