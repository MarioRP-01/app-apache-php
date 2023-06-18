<?php

declare(strict_types=1);

namespace Application\Controller\Ajax;

use Application\Service\ClothingService;
use Laminas\Mvc\Controller\AbstractActionController;

class IndexAjaxController extends AbstractActionController
{
    private ClothingService $clothingService;

    public function __construct(ClothingService $clothingService) {
        $this->clothingService = $clothingService;
    }

    public function clothingsAction() {
        die('clothingsAction');
    }

    public function clothingAction() {
        die('clothingAction');
    }
}