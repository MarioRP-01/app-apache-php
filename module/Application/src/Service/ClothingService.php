<?php

namespace Application\Service;

use Application\Model\Clothing;
use Application\Model\Dao\ClothingDAO;
use Laminas\Hydrator\ClassMethodsHydrator;

class ClothingService
{
    private ClassMethodsHydrator $hydrator;
    private ClothingDAO $clothingDAO;

    public function __construct(ClothingDAO $clothingDAO) {
        $this->hydrator = new ClassMethodsHydrator();
        $this->clothingDAO = $clothingDAO;
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

