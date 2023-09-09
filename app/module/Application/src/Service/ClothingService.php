<?php

namespace Application\Service;

use Application\Model\Clothing;
use Application\Model\Dao\ClothingDAO;
use Application\Model\Dto\Rest\ClothingDTOREST;
use Application\Model\Pagination\Page;
use Application\Model\Pagination\PageInterface;
use Application\Traits\RouterTrait;
use Application\Utils\Constants;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\Stdlib\ArrayUtils;

class ClothingService {
    
    use RouterTrait;

    private ObjectPropertyHydrator $hydrator;

    public function __construct(
        readonly ClothingDAO $clothingDAO,
        readonly TreeRouteStack $router,
    ) {
        $this->hydrator = new ObjectPropertyHydrator();
    }

    /**
     * @return array<Clothing>
     */
    public function getAllClothingPaged(int $start, int $limit): PageInterface {

        $result = ArrayUtils::iteratorToArray(
            $this->clothingDAO->getAllClothingPaged($start, $limit)
        );

        $result = array_map(function($item) {

            $clothing =  $this->hydrator->hydrate($item, new Clothing);
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

    public function createDTORESTFromClothing(Clothing $clothing) {
        $clothing = ClothingDTOREST::fromDTO($clothing);
    }

    /**
     * Return the path to the image file if it exists, otherwise return false
     */
    public function getImagePath(string $uuid): string|bool {
        $uuid = basename($uuid);
        $path = Constants::CLOTHING_IMAGE_PATH . '/' . $uuid . '.jpg'; 
        return file_exists($path) ? $path : false;
    }

    public function getClothingItemLink(int $clothing_id) {
        return $this->getRoute('api/clothings/item', ['id' => $clothing_id]);
    }

    public function getClothingItemUILink(int $clothing_id) {
        return $this->getRoute('clothings-item', ['id' => $clothing_id]);
    }

    public function getImageLinkByClothing(Clothing $clothing) {

        $params = [
            'file_name' => $clothing->uuid,
        ];

        $options = [
            'force_canonical' => true,
        ];

        return $this->getRoute('api/clothings/images', $params, $options);
    }

    public function getImageLinkById(string $uuid) {

        $clothing = $this->getClothingById($uuid);
        if (is_null($clothing))
            return null;

        return $this->getImageLinkByClothing($clothing);
    }

    public function createClothing_expandable(Clothing $clothing): array {
        return [
            'image' => $this->getImageLinkByClothing($clothing),
        ];
    }

    public function createClothing_links(Clothing $clothing): array {

        return [
            'self' => $this->getClothingItemLink($clothing->uuid),
            'webui' => $this->getClothingItemUILink($clothing->uuid),
        ];
    }

    public function createClothingPage_links(
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
