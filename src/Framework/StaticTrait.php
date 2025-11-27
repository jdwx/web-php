<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


trait StaticTrait {


    /** @var array<string, string> */
    private const array CONTENT_TYPES = [
        'html' => 'text/html',
        'js' => 'application/javascript',
        'css' => 'text/css',
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'ico' => 'image/x-icon',
        'svg' => 'image/svg+xml',
        'txt' => 'text/plain',
        'webp' => 'image/webp',
    ];


    /** @var array<string, string> */
    protected array $rExtraContentTypes = [];


    public function inferContentType( ?string $i_pathName, ?string $i_nstDefault = null ) : ?string {
        return $this->lookupContentType( $this->inferExtension( $i_pathName ) ) ?? $i_nstDefault;
    }


    public function inferContentTypeEx( ?string $i_pathName, ?string $i_nstDefault = null ) : string {
        $nst = $this->inferContentType( $i_pathName, $i_nstDefault );
        if ( is_string( $nst ) ) {
            return $nst;
        }
        throw new \RuntimeException( 'Failed to infer content type for path: ' . $i_pathName );
    }


    public function inferExtension( ?string $i_pathName ) : ?string {
        if ( ! is_string( $i_pathName ) ) {
            return null;
        }
        $path = pathinfo( $i_pathName );
        if ( ! empty( $path[ 'extension' ] ) ) {
            return $path[ 'extension' ];
        }
        return null;
    }


    /** @return iterable<string, string> */
    public function listExtensions() : iterable {
        yield from self::CONTENT_TYPES;
        yield from $this->rExtraContentTypes;
    }


    public function lookupContentType( ?string $i_nstExtension ) : ?string {
        if ( ! is_string( $i_nstExtension ) ) {
            return null;
        }
        return $this->rExtraContentTypes[ $i_nstExtension ] ?? self::CONTENT_TYPES[ $i_nstExtension ] ?? null;
    }


    public function mapContentType( string $i_stExtension, string $i_stContentType ) : void {
        $this->rExtraContentTypes[ $i_stExtension ] = $i_stContentType;
    }


    public function multiViews( string $i_stPath ) : ?string {
        if ( file_exists( $i_stPath ) ) {
            return $i_stPath;
        }
        foreach ( $this->listExtensions() as $stExt => $stContentType ) {
            $stCheckPath = "{$i_stPath}.{$stExt}";
            if ( file_exists( $stCheckPath ) ) {
                return $stCheckPath;
            }
        }

        return null;
    }


}