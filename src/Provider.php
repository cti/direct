<?php

namespace Nekufa\Direct;

use Nekufa\Application\Web;
use Nekufa\Di\Manager;

class Provider
{
    /**
     * @var Nekufa\Direct\Service
     */
    protected $service;
    
    /**
     * @param Nekufa\Direct\Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    function getJavascript($url)
    {
        echo 'Ext.Direct.addProvider({
            type: "remoting",
            url: "'. $url . '",
            actions: '.json_encode($this->service->getActions()).'
        });';
    }

    function postIndex(Manager $manager, Service $service)
    {
        $data = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
        if(!is_array($data)) {
            $response = $service->handle($manager, Request::create($data));

        } else {
            $response = array();
            foreach($data as $request) {
                $response[] = $service->handle($manager, Request::create($request));
            }
        }

        echo json_encode($response);
    }
}