<?php

namespace Application\Service\Factory;

use Application\Model\Dao\ClothingDAO;
use Application\Service\ClothingService;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

class ClothingServiceFactory implements FactoryInterface {

    function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ) {
        /** @var TreeRouteStack $router */
        $router = $container->get('Router');

        /** @var AdapterInterface $dbAdapter */
        $dbAdapter = $container->get(AdapterInterface::class);

        $clothingDAO = new ClothingDAO($dbAdapter);

        return new ClothingService($clothingDAO, $router);
    }
}