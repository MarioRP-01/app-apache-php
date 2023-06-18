<?php

namespace Application\Service;

use Application\Model\Clothing;
use Application\Model\Dao\ClothingDAO;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Stdlib\ArrayUtils;

class ClothingService
{
    private ClassMethodsHydrator $hydrator;
    private ClothingDAO $clothingDAO;

    public function __construct(ClothingDAO $clothingDAO) {
        $this->hydrator = new ClassMethodsHydrator();
        $this->clothingDAO = $clothingDAO;
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
}
