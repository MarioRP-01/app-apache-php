<?php

namespace Application\Model\Dto\Rest;
use Application\Model\Clothing;

class ClothingDTOREST implements DTORESTInterface {

    /**
     * @param array<string, string> $_links
     * @param array<string, string> $_expandable
     */
    public function __construct(
        readonly ?int $id,
        readonly ?string $file_name,
        readonly ?string $label,
        readonly ?string $size,
        readonly ?bool $kids,
        private array $_links,
        private array $_expandable
    ) {

    }

    public static function fromDTO(Clothing $clothing) {
        return new self(
            $clothing->getId(),
            $clothing->getFileName(),
            $clothing->getLabel(),
            $clothing->getSize(),
            $clothing->getKids(),
            [],
            []
        );
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getFileName(): ?string {
        return $this->file_name;
    }

    public function getLabel(): ?string {
        return $this->label;
    }

    public function getSize(): ?string {
        return $this->size;
    }

    public function getKids(): ?bool {
        return $this->kids;
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
            'id' => $this->getId(),
            'file_name' => $this->getFileName(),
            'label' => $this->getLabel(),
            'size' => $this->getSize(),
            'kids' => $this->getKids(),
            '_links' => $this->get_links(),
            '_expandable' => $this->get_expandable()
        ];
    }
}

