<?php

namespace Nekufa\Direct;

use Exception;
use Nekufa\Di\Manager;
use Nekufa\Di\Reflection;

class Service
{
    /**
     * action map to class
     * @var array
     */
    protected $classes = array();

    /**
     * action description
     * @var array
     */
    protected $actions = array();

    /**
     * response hash
     * @var array[Response]
     */
    protected $responses = array();

    /**
     * @param $list
     */
    function __construct($list)
    {
        $list = is_array($list) ? $list : func_get_args();

        foreach ($list as $class) {
            $alias = basename(str_replace('\\', DIRECTORY_SEPARATOR, $class));
            $this->classes[$alias] = $class;

            $this->actions[$alias] = array();
            foreach (Reflection::getReflectionClass($class)->getMethods() as $method) {
                if (!$method->isConstructor()) {
                    $len = 0;
                    foreach ($method->getParameters() as $parameter) {
                        if (is_null($parameter->getClass())) {
                            $len++;
                        }
                    }
                    $this->actions[$alias][] = array(
                        'name' => $method->getName(),
                        'len' => $len,
                    );
                }
            }
        }
    }

    /**
     * @param Manager $manager
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    function handle(Manager $manager, Request $request)
    {
        $response = $request->generateResponse();
        
        if (isset($this->responses[$request->getTid()])) {
            return $this->responses[$request->getTid()];
        }

        try {
            if (!isset($this->classes[$request->getAction()])) {
                throw new Exception(sprintf("Action %s not found", $request->getAction()));
            }

            foreach ($this->actions[$request->getAction()] as $info) {
                if ($request->getMethod() == $info['name']) {
                    $response->setType($request->getType());
                    $response->setResult($manager->call(
                        $this->classes[$request->getAction()],
                        $request->getMethod(),
                        $request->getData()
                    ));
                    break;
                }
            }

            if(!$response->getType()) {
                throw new Exception(sprintf("Method %s not found", $response->getMethod()));
            }

        } catch (Exception $e) {
            $response->setType('exception');
            $response->setResult($e->getMessage());
            $response->setFile($e->getFile());
            $response->setLine($e->getLine());

        }
        return $this->responses[$response->getTid()] = $response;            
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

}