<?php /** @noinspection PhpLackOfCohesionInspection */


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


use JDWX\Web\ServerInterface;


class MockServer implements ServerInterface {


    public string $stDocumentRoot = '/var/www/html';

    public string $stHttpHost = 'www.example.com';

    public string $stHttpReferer = 'https://www.example.org/page.html';

    public string $stHttpUserAgent = 'Super Fake User Agent';

    public string $stPathInfo = '/path/to/script.php';

    public string $stPhpSelf = '/index.php';

    public string $stRequestMethod = 'GET';

    public string $stRequestScheme = 'https';

    public string $stRequestUri = '/';

    public string $stRemoteAddr = '127.0.0.1';

    public int $iRemotePort = 12345;

    public string $stScriptFilename = '/var/www/html/index.php';

    public string $stScriptName = '/index.php';

    public string $stServerAddr = '198.18.0.1';

    public string $stServerName = 'www.example.com';


    public function __construct( ?string $i_nstRequestMethod = null, ?string $i_stRequestUri = null ) {
        if ( is_string( $i_nstRequestMethod ) ) {
            $this->stRequestMethod = $i_nstRequestMethod;
        }
        if ( is_string( $i_stRequestUri ) ) {
            $this->stRequestUri = $i_stRequestUri;
        }
    }


    public function documentRoot() : string {
        return $this->stDocumentRoot;
    }


    public function httpHost() : string {
        return $this->stHttpHost;
    }


    public function httpReferer() : string {
        return $this->stHttpReferer;
    }


    public function httpUserAgent() : string {
        return $this->stHttpUserAgent;
    }


    public function https() : bool {
        return 'https' === $this->requestScheme();
    }


    public function pathInfo() : string {
        return $this->stPathInfo;
    }


    public function phpSelf() : string {
        return $this->stPhpSelf;
    }


    public function remoteAddr() : string {
        return $this->stRemoteAddr;
    }


    public function remotePort() : int {
        return $this->iRemotePort;
    }


    public function requestMethod() : string {
        return $this->stRequestMethod;
    }


    public function requestScheme() : string {
        return $this->stRequestScheme;
    }


    public function requestUri() : string {
        return $this->stRequestUri;
    }


    public function scriptFilename() : string {
        return $this->stScriptFilename;
    }


    public function scriptName() : string {
        return $this->stScriptName;
    }


    public function serverAddr() : string {
        return $this->stServerAddr;
    }


    public function serverName() : string {
        return $this->stServerName;
    }


}
