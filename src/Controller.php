<?php

namespace Cti\Direct;

use Cti\Core\Module\Web;
use Cti\Di\Manager;

class Controller
{
    /**
     * @var string
     */
    public $url = 'direct';

    /**
     * @inject
     * @var Cti\Direct\Service
     */
    protected $service;

    function get(Web $web)
    {
        echo 'Ext.Direct.addProvider({
            type: "remoting",
            url: "'. $web->getUrl($this->url) . '",
            actions: '.json_encode($this->service->getActions()).'
        });';
    }

    function post(Manager $manager)
    {
        $data = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
        if(!is_array($data)) {
            $response = $this->service->handle($manager, Request::create($data));

        } else {
            $response = array();
            foreach($data as $request) {
                $response[] = $this->service->handle($manager, Request::create($request));
            }
        }

        echo json_encode($response);
    }
}

