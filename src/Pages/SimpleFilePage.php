<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


use JDWX\Strict\OK;


class SimpleFilePage extends AbstractBinaryPage {


    private const array EXTENSION_MAP = [
        'css' => 'text/css',
        'gif' => 'image/gif',
        'html' => 'text/html',
        'ico' => 'image/x-icon',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
        'txt' => 'text/plain',
        'webp' => 'image/webp',
        'xml' => 'text/xml',
        'zip' => 'application/zip',
    ];


    public const int DEFAULT_READ_SIZE = 4096;


    private readonly int $uReadSize;


    public function __construct( private readonly string $stFilename,
                                 ?string                 $i_nstContentType = null,
                                 ?int                    $nuReadSize = null ) {
        parent::__construct( $i_nstContentType ?? static::inferType( $stFilename ) );
        $this->uReadSize = $nuReadSize ?? self::DEFAULT_READ_SIZE;
    }


    public static function inferType( string $i_stFilename ) : ?string {
        $uDot = strrpos( $i_stFilename, '.' );
        if ( $uDot === false ) {
            return null;
        }
        $stExt = substr( $i_stFilename, $uDot + 1 );
        return self::EXTENSION_MAP[ $stExt ] ?? null;
    }


    /**
     * @inheritDoc
     */
    public function stream() : iterable {
        $f = OK::fopen( $this->stFilename, 'rb' );
        while ( ! feof( $f ) ) {
            yield OK::fread( $f, $this->uReadSize );
        }
        OK::fclose( $f );
    }


}
