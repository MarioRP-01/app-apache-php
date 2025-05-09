<?php

namespace Application\Model\Dto\Rest;
use Application\Model\Clothing;

class ClothingDTOREST implements DTORESTInterface {

    /**
     * @param array<string, string> $_links
     * @param array<string, string> $_expandable
     */
    public function __construct(
        readonly ?string $uuid,
        readonly ?string $name,
        readonly ?string $brand,
        readonly ?float $price,
        readonly ?string $description,
        readonly ?string $primary_color,
        readonly ?string $label,
        readonly ?string $gender,
        readonly ?string $size,
        private array $_links = [],
        private array $_expandable = []
    ) {

    }

    public static function fromDTO(Clothing $clothing) {
        return new self(
            $clothing->uuid,
            $clothing->name,
            $clothing->brand,
            $clothing->price,
            $clothing->description,
            $clothing->primary_color,
            $clothing->label,
            $clothing->gender,
            $clothing->size
        );
    }

    public function get_links(): array {
        return $this->_links;
    }

    public function set_links(array $_links): self {
        $this->_links = $_links;
        return $this;
    }

    public function get_expandable(): array {
        return $this->_expandable;
    }

    public function set_expandable(array $_expandable): self {
        $this->_expandable = $_expandable;
        return $this;
    }

    public function jsonSerialize(): array {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'brand' => $this->brand,
            'price' => $this->price,
            'description' => $this->description,
            'primary_color' => $this->primary_color,
            'label' => $this->label,
            'gender' => $this->gender,
            'size' => $this->size,
            '_links' => $this->get_links(),
            '_expandable' => $this->get_expandable()
        ];
    }
}

