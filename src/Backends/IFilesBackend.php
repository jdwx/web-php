<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


interface IFilesBackend {


    public function fileExists( string $i_stPath ) : bool;


    public function fileGetContents( string $i_stPath ) : string|false;


    public function fileGetContentsEx( string $i_stPath ) : string;


    public function isUploadedFile( string $i_stPath ) : bool;


    public function moveUploadedFile( string $i_stFrom, string $i_stTo ) : bool;


    public function moveUploadedFileEx( string $i_stFrom, string $i_stTo ) : void;


}
