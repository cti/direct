<?php

namespace Cti\Direct;

use Cti\Core\Web;

class Controller
{
    /**
     * @var string
     */
    public $url = 'direct';

    function get(Provider $provider, Web $web)
    {
        $location = $web->getUrl($this->url);
        echo $provider->getJavascript($location);
    }

    function post(Provider $provider)
    {
        $request = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
        $response = $provider->handle($request);
        echo json_encode($response);
    }
}

