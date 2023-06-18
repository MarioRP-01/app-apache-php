<?php

namespace Application\Controller\Ajax\Factory;

use Application\Controller\Ajax\IndexAjaxController;
use Application\Service\ClothingService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class IndexAjaxControllerFactory implements FactoryInterface {

    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ) {
        /** @var ClothingService $clothingService */
        $clothingService = $container->get(ClothingService::class);
        
        return new IndexAjaxController($clothingService);
    }
}
