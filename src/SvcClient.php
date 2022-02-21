<?php

namespace CVLB\Svc;

use DI\Container;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Redis;
use CVLB\Svc\Auth\Providers\AuthServiceProvider;
use function DI\autowire;

class SvcClient
{
    /**
     * @var object
     */
    protected object $service;
    
    /**
     * @var Container 
     */
    protected Container $container;
    
    /**
     * @param array{client_id: string, client_secret: string} $credentials 
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        $builder = new ContainerBuilder();

        $builder->addDefinitions([
            AuthServiceProvider::class => autowire()->constructor(new Redis, $credentials)
        ]);

        $this->container = $builder->build();
    }

    /**
     * @param $name
     * @param array $arguments
     * @return void
     */
    public function __call($name, array $arguments)
    {
        return call_user_func_array(array($this->service, $name), $arguments);
    }
    
    /**
     * @param string $name
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function load(string $name)
    {
        $this->setService($this->container->get($name));
    }

    /**
     * @param $service
     * @return void
     */
    protected function setService($service)
    {
        $this->service = $service;
    }
}