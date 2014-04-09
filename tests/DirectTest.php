<?php

use Cti\Di\Manager;
use Cti\Direct\Request;
use Cti\Direct\Service;

class DirectTest extends PHPUnit_Framework_TestCase
{
    function testRequestAndResponse()
    {
        $request = Request::create((object) array(
            'action' => 'action',
            'method' => 'method',
            'tid' => 1,
            'type' => 'type',
            'data' => array('z')
        ));

        $this->assertSame($request->getAction(), 'action');
        $this->assertSame($request->getMethod(), 'method');
        $this->assertSame($request->getTid(), 1);
        $this->assertSame($request->getType(), 'type');
        $this->assertCount(1, $request->getData());
        $this->assertSame($request->getData(), array('z'));

        $response = $request->generateResponse();
        $this->assertInstanceOf('Cti\Direct\Response', $response);
        $this->assertSame($response->getAction(), 'action');
        $this->assertSame($response->getMethod(), 'method');
        $this->assertSame($response->getTid(), 1);

        $this->assertNull($response->getResult());
        $this->assertNull($response->getType());
        $this->assertNull($response->getFile());
        $this->assertNull($response->getLine());

        $response->setResult('result')->setType('success')->setFile(__FILE__)->setLine(1);
    }

    function testCalling()
    {
        $manager = new Manager;
        $tid = 1;

        $manager->getConfiguration()->set('Cti\Direct\Service', 'list', array('Common\Api'));

        $response = $manager->call('Cti\Direct\Service', 'handle', array(
            'request' => Request::create((object) array(
                'action' => 'Api',
                'method' => 'greet', 
                'tid' => $tid,
                'type' => 'type',
                'data' => array('Cti')
            ))
        ));

        $this->assertSame($response->getResult(), "Hello, Cti!");

        // test cache by tid
        $responseWithSameTid = $manager->call('Cti\Direct\Service', 'handle', array(
            'request' => Request::create((object) array(
                'action' => 'Api',
                'method' => 'anotherGreet',
                'tid' => $tid,
                'type' => 'type',
                'data' => array('Cti2')
            ))
        ));

        $this->assertSame($response, $responseWithSameTid);

        // test exception
        $response = $manager->call('Cti\Direct\Service', 'handle', array(
            'request' => Request::create((object) array(
                'action' => 'Api',
                'method' => 'exception',
                'tid' => ++$tid,
                'type' => 'type',
                'data' => array('message')
            ))
        ));

        $this->assertSame($response->getType(), 'exception');
        $this->assertSame($response->getResult(), 'message');
        $this->assertNotNull($response->getFile());
        $this->assertNotNull($response->getLine());

        // test incorrect action
        $response = $manager->call('Cti\Direct\Service', 'handle', array(
            'request' => Request::create((object) array(
                'action' => 'no_action',
                'method' => 'no_method',
                'tid' => ++$tid,
                'type' => 'type',
                'data' => array()
            ))
        ));

        $this->assertSame($response->getType(), 'exception');
        $this->assertContains("Action no_action not found", $response->getResult());

        // test incorrect method
        $response = $manager->call('Cti\Direct\Service', 'handle', array(
            'request' => Request::create((object) array(
                'action' => 'Api',
                'method' => 'no_method',
                'tid' => ++$tid,
                'type' => 'type',
                'data' => array()
            ))
        ));

        $this->assertSame($response->getType(), 'exception');
        $this->assertContains("Method no_method not found", $response->getResult());

    }


    function testServiceArguments()
    {
        $service = new Service('Common\Api', 'Common\Sys');
        $anotherService = new Service(array('Common\Api', 'Common\Sys'));

        $this->assertSame(
            json_encode($service->getActions()), 
            json_encode($anotherService->getActions())
        );
    }
}