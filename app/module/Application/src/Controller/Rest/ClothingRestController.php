<?php

declare(strict_types=1);

namespace Application\Controller\Rest;

use Application\Service\ClothingService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Stdlib\ResponseInterface;

class ClothingRestController extends AbstractActionController
{

    public function __construct(
        readonly ClothingService $clothingService
    ) { }

    public function getClothingsAction() {
        $start = $this->params()->fromQuery('start', 0);
        $limit = $this->params()->fromQuery('limit', 8);

        $clothings = $this->clothingService->getAllClothingPaged($start, $limit);

        $response = $this->getResponse();

        return $response
            ->setContent(json_encode($clothings))
            ->setStatusCode(200);
    }
  
    public function getClothingAction() {
        die('clothingAction');
    }

    public function getClothingMainImageAction(): ResponseInterface {
        $response = $this->getResponse();
        $headers = $response->getHeaders();

        $uuid = $this->params('uuid');

        if (!$path = $this->clothingService->getMainImageFilePath($uuid))
            return $response->setStatusCode(404);
        
        $fileContent = file_get_contents($path);

        $headers
            ->addHeaderLine('Content-Type', 'image/jpeg')
            ->addHeaderLine('Content-Length', (string) filesize($path));
        
        return $response
            ->setContent($fileContent)
            ->setStatusCode(200);
    }
}