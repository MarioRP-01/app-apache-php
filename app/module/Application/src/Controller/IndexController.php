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
        return new ViewModel();
    }

    public function clothingAction() {
        return new ViewModel();
    }
}
