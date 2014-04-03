<?php

namespace Nekufa\Direct;

use Nekufa\Tools\Web;
use Nekufa\Di\Manager;

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