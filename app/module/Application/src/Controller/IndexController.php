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

    public function indexAction() {
        return new ViewModel([
            'clothings' => $this->clothingService->getAllClothingPaged(0, 12),
            'best_sellers' => $this->clothingService->getAllClothingPaged(12, 30),
            'just_for_you' => $this->clothingService->getAllClothingPaged(30, 42)
        ]);
    }

    public function clothingAction() {
        $clothing_uuid = $this->params()->fromRoute('uuid');

        return new ViewModel([
            'clothing' => $this->clothingService->getClothingDTORESTById($clothing_uuid)
        ]);
    }

    public function clothingsFilterAction() {

        $clothing_name = $this->params()->fromQuery('name');
        $start = intval($this->params()->fromQuery('start', 0));
        $limit = intval($this->params()->fromQuery('limit', 20));

        if (is_null($clothing_name)) throw new \Exception('Not implemented');

        return new ViewModel([
            'clothings' => $this->clothingService->getClothingByNamePaged(
                $clothing_name,
                $start,
                $limit
            )
        ]);
    }
}
