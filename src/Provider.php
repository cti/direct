<?php

namespace Cti\Direct;

use Cti\Core\Web;
use Cti\Di\Manager;

class Provider
{
    /**
     * @var Cti\Direct\Service
     */
    protected $service;
    
    /**
     * @param Cti\Direct\Service $service
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