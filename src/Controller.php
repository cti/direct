<?php

namespace Cti\Direct;

use Cti\Tools\Web;
use Cti\Di\Manager;

class Controller
{
    /**
     * @var string
     */
    public $url = 'direct';

    function getIndex(Provider $provider, Web $application)
    {
        $location = $application->getUrl($this->url);
        echo $provider->getJavascript($location);
    }

    function postIndex(Provider $provider)
    {
        $request = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
        $response = $provider->handle($request);
        echo json_encode($response);
    }
}