<?php

namespace Cti\Direct;

use Cti\Core\Application;
use Cti\Di\Configuration;

class Extension
{
    /** 
     * Namespace for direct server api
     * @var string
     */
    public $namespace = 'Direct';

    /**
     * Controller for processing requests
     * @var string
     */
    public $controller = 'Cti\Direct\Controller';

    /**
     * Controller location
     */
    public $path = '/direct/';

    /**
     * init extension
     * @param Application $application
     * @param Configuration $configuration
     */
    function init(Application $application, Configuration $configuration)
    {
        // mount controller
        $controllers = $configuration->push('Cti\Core\Web', 'controllers', $this->controller, $this->path);

        // add service classes
        foreach($application->listClasses($this->namespace) as $class) {
            $configuration->push('Cti\Direct\Service', 'list', $class);
        }
    }
}