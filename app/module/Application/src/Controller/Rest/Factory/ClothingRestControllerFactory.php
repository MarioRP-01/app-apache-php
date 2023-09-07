<?php

namespace Application\Controller\Rest\Factory;

use Application\Controller\Rest\ClothingRestController;
use Application\Service\ClothingService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class ClothingRestControllerFactory implements FactoryInterface {

    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ) {
        /** @var ClothingService $clothingService */
        $clothingService = $container->get(ClothingService::class);
        
        return new ClothingRestController($clothingService);
    }
}
