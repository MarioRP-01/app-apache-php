<?php

namespace Application\Service;

use Application\Model\Clothing;
use Application\Model\Dao\ClothingDAO;
use Application\Utils\Constants;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Stdlib\ArrayUtils;

class ClothingService
{
    private ClassMethodsHydrator $hydrator;

    public function __construct(
        readonly ClothingDAO $clothingDAO
    ) {
        $this->hydrator = new ClassMethodsHydrator();
    }

    /**
     * @return array<Clothing>
     */
    public function getAllClothingPaged(int $page, $page_size) {
        $result =  $this->clothingDAO->getAllClothingPaged($page, $page_size);
        $result = array_map(function($item) {
            return $this->hydrator->hydrate($item, new Clothing);
        }, ArrayUtils::iteratorToArray($result));

        return $result;
    }

    public function getClothingById(int $id) {
        $result = $this->clothingDAO->getClothingById($id);
        return !is_null($result) ?
            $this->hydrator->hydrate(
                $result,
                new Clothing
            ) :
            null;
    }

    /**
     * Return the path to the image file if it exists, otherwise return false
     */
    public function getImagePath(string $file_name): string|bool {
        $file_name = basename($file_name);
        $path = Constants::CLOTHING_IMAGE_PATH . '/' . $file_name;
        return file_exists($path) ? $path : false;
    }
}
