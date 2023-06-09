<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Service\ClothingService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    private ClothingService $clothingService;

    public function __construct(ClothingService $clothingService) {
        $this->clothingService = $clothingService;
    }

    public function indexAction()
    {
        $clothings = $this->clothingService->getAllClothingPaged(1, 10);

        return new ViewModel([
            'clothings' => $clothings
        ]);
    }
}
