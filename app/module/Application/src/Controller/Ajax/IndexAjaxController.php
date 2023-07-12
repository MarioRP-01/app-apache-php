<?php

declare(strict_types=1);

namespace Application\Controller\Ajax;

use Application\Service\ClothingService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Stdlib\ResponseInterface;

class IndexAjaxController extends AbstractActionController
{

    public function __construct(
        readonly ClothingService $clothingService
    ) { }

    public function clothingsAction() {
        die('clothingsAction');
    }

    public function clothingAction() {
        die('clothingAction');
    }

    public function getClothingImageAction(): ResponseInterface {
        $response = $this->getResponse();
        $headers = $response->getHeaders();

        $file_name = $this->params('file_name');

        if (!$path = $this->clothingService->getImagePath($file_name)) 
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