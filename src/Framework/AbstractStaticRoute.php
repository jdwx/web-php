<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


abstract class AbstractStaticRoute extends AbstractRoute {


    use StaticTrait;


    private string $stContentType = 'application/octet-stream';


    public function setContentType( string $i_stContentType ) : void {
        $this->stContentType = $i_stContentType;
    }


    abstract protected function getContent() : string;


    protected function handleGET( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        return Response::binary( $this->getContent(), 200, $this->stContentType );
    }


}
