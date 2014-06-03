<?php

namespace Cti\Direct;

use Cti\Di\Manager;

class Controller
{
    function get(Module $module, Service $service)
    {
        echo 'Ext.Direct.addProvider({
            type: "remoting",
            url: "'. $module->getUrl() . '",
            actions: '.json_encode($service->getActions()).'
        });';
    }

    function post(Manager $manager, Service $service)
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

