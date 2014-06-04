<?php

namespace Cti\Direct;

use Build\Application;
use Cti\Core\Application\Bootloader;
use Cti\Core\Module\Project;
use Cti\Core\Module\Web;
use Cti\Di\Configuration;

class Module implements Bootloader
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
    public $path = 'direct';

    /**
     * @inject
     * @var Build\Application
     */
    public $application;

    /**
     * Aplication url
     */
    public $url;

    public function boot(Application $application)
    {
        $application->getManager()->getInitializer()->after('Cti\Core\Module\Web', array($this, 'registerController'));

        $configuration = $application->getManager()->getConfiguration();
        $classes = $application->getProject()->getClasses('Direct');

        if(!count($classes)) {
            $list = $configuration->get('Cti\Direct\Service', 'list');
            $list = is_null($list) ? array() : $list;
            $configuration->set('Cti\Direct\Service', 'list', $list);

        } else {
            foreach($classes as $class) {
                $configuration->push('Cti\Direct\Service', 'list', $class);
            }            
        }
    }

    public function getUrl()
    {
        if(is_null($this->url)) {
            $this->url = $this->application->getWeb()->getUrl($this->path);
        }
        return $this->url;
    }

    public function registerController(Web $web)
    {
        $web->add('/' . $this->path, $this->controller);
    }
}