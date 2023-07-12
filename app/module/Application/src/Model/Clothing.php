<?php

namespace Application\Model;

class Clothing extends \ArrayObject {
    
    private ?int $id;
    private ?string $file_name;
    private ?string $label;
    private ?string $size;
    private ?bool $kids;

	public function getId(): ?int {
		return $this->id;
	}
	
	/**
	 * @param  $id 
	 * @return self
	 */
	public function setId(?int $id): self {
		$this->id = $id;
		return $this;
	}

	public function getFileName(): ?string {
		return $this->file_name;
	}
	
	/**
	 * @param  $file_name 
	 * @return self
	 */
	public function setFileName(?string $file_name): self {
		$this->file_name = $file_name;
		return $this;
	}

	public function getLabel(): ?string {
		return $this->label;
	}
	
	/**
	 * @param  $label 
	 * @return self
	 */
	public function setLabel(?string $label): self {
		$this->label = $label;
		return $this;
	}

	public function getSize(): ?string {
		return $this->size;
	}
	
	/**
	 * @param  $size 
	 * @return self
	 */
	public function setSize(?string $size): self {
		$this->size = $size;
		return $this;
	}

	public function getKids(): ?bool {
		return $this->kids;
	}
	
	/**
	 * @param  $kids 
	 * @return self
	 */
	public function setKids(?bool $kids): self {
		$this->kids = $kids;
		return $this;
	}

    public function exchangeArray(object|array $data): array {
        $this->id = $data['id'] ?? null;
        $this->file_name = $data['file_name'] ?? null;
        $this->label = $data['label'] ?? null;
        $this->size = $data['size'] ?? null;
        $this->kids = $data['kids'] ?? null;

        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'label' => $this->label,
            'size' => $this->size,
            'kids' => $this->kids,
        ];
    }  
}