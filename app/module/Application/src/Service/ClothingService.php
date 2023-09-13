<?php

namespace Application\Service;

use Application\Model\Clothing;
use Application\Model\Dao\ClothingDAO;
use Application\Model\Dto\Rest\ClothingDTOREST;
use Application\Model\Pagination\Page;
use Application\Model\Pagination\PageInterface;
use Application\Traits\RouterTrait;
use Application\Utils\Constants;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\Stdlib\ArrayUtils;

class ClothingService {
    
    use RouterTrait;

    private ReflectionHydrator $hydrator;

    public function __construct(
        readonly ClothingDAO $clothingDAO,
        readonly TreeRouteStack $router,
    ) {
        $this->hydrator = new ReflectionHydrator();
    }

    /**
     * @return array<Clothing>
     */
    public function getAllClothingPaged(int $start, int $limit): PageInterface {

        $result = ArrayUtils::iteratorToArray(
            $this->clothingDAO->getAllClothingPaged($start, $limit)
        );

        $result = array_map(function($item) {

            $clothing = $this->hydrator->hydrate($item, new Clothing);
            return ClothingDTOREST::fromDTO($clothing)
                ->set_expandable($this->createClothing_expandable($clothing))
                ->set_links($this->createClothing_links($clothing));

        }, $result);

        $size = count($result);

        return new Page(
            $result,
            $limit,
            $start,
            $size,
            $this->createClothingPage_links($start, $limit, $size)
        );
    }

    public function getClothingByNamePaged(
        string $name,
        int $start,
        int $limit
    ): PageInterface {

        $result = ArrayUtils::iteratorToArray(
            $this->clothingDAO->getClothingByNamePaged(
                $name,
                $start,
                $limit
            )
        );

        $result = array_map(function($item) {

            $clothing = $this->hydrator->hydrate($item, new Clothing);
            return ClothingDTOREST::fromDTO($clothing)
                ->set_expandable($this->createClothing_expandable($clothing))
                ->set_links($this->createClothing_links($clothing));

        }, $result);

        $size = count($result);

        return new Page(
            $result,
            $limit,
            $start,
            $size,
            $this->createClothingPage_links($start, $limit, $size)
        );
    }

    public function getClothingById(string $uuid): ?Clothing {
        $result = $this->clothingDAO->getClothingById($uuid);
        if (is_null($result)) return null;
        $result['price'] = floatval($result['price']);
        return $this->hydrator->hydrate($result, new Clothing);
    }

    public function getClothingDTORESTById(string $uuid): ?ClothingDTOREST {
        $clothing = $this->getClothingById($uuid);
        return !is_null($clothing) ?
            ClothingDTOREST::fromDTO($clothing)
                ->set_expandable($this->createClothing_expandable($clothing))
                ->set_links($this->createClothing_links($clothing)) :
            null;
    }

    private function createDTORESTfromDTO(Clothing $clothing) {
        $clothing = ClothingDTOREST::fromDTO($clothing);
    }

    /**
     * Return the path to the image file if it exists, otherwise return false
     */
    public function getMainImageFilePath(string $uuid): string|bool {
        $uuid = basename($uuid);
        $path = Constants::CLOTHING_IMAGE_PATH . '/' . $uuid . '.jpg'; 
        return file_exists($path) ? $path : false;
    }

    private function getClothingItemLink(string $clothing_uuid) {
        return $this->getRoute('api/clothings/item', ['uuid' => $clothing_uuid]);
    }

    private function getClothingItemUILink(string $clothing_uuid) {
        return $this->getRoute('clothing', ['uuid' => $clothing_uuid]);
    }

    private function getMainImageLinkByClothing(Clothing $clothing) {

        $params = [
            'uuid' => $clothing->uuid,
        ];

        $options = [
            'force_canonical' => true,
        ];

        return $this->getRoute('api/clothings/item/images/main-item', $params, $options);
    }

    private function createClothing_expandable(Clothing $clothing): array {
        return [
            'main_image' => $this->getMainImageLinkByClothing($clothing),
        ];
    }

    private function createClothing_links(Clothing $clothing): array {

        return [
            'self' => $this->getClothingItemLink($clothing->uuid),
            'webui' => $this->getClothingItemUILink($clothing->uuid),
        ];
    }

    private function createClothingPage_links(
        int $start,
        int $limit,
        int $size
    ): array {

        $options_self = $start !== 0 ? [
            'query' => [
                'start' => $start,
                'limit' => $limit,
            ] 
        ] : [];

        $result = [
            'base' => $this->getRoute('home', [], ['force_canonical' => true]),
            'context' => '',
            'self' => $this->getRoute('api/clothings', [], $options_self)
        ];

        if ($start > 0) 
            $result['prev'] = $this->getRoute('api/clothings', [],  [
                'query' => [
                    'start' => max($start - $limit, 0),
                    'limit' => $limit,
                ]
            ]);

        if ($size <= $limit) {
            $result['next'] = $this->getRoute('api/clothings', [], [
                'query' => [
                    'start' => $start + $limit,
                    'limit' => $limit,
                ] 
            ]);
        }

        return $result;
    }
}
