<?php

namespace Application\Model;

class Clothing extends \ArrayObject {
    
    public ?string $uuid;
	public ?string $name;
	public ?string $brand;
	public ?float $price;
	public ?string $description;
	public ?string $primary_color;
	public ?string $label;
	public ?string $gender;
	public ?string $size;

    public function exchangeArray(object|array $data): array {
        $this->uuid = $data['uuid'] ?? null;
		$this->name = $data['name'] ?? null;
		$this->brand = $data['brand'] ?? null;
		$this->price = $data['price'] ?? null;
		$this->description = $data['description'] ?? null;
		$this->primary_color = $data['primary_color'] ?? null;
		$this->label = $data['label'] ?? null;
		$this->gender = $data['gender'] ?? null;
		$this->size = $data['size'] ?? null;

        return [
            'uuid' => $this->uuid,
			'name' => $this->name,
			'brand' => $this->brand,
			'price' => $this->price,
			'description' => $this->description,
			'primary_color' => $this->primary_color,
			'label' => $this->label,
			'gender' => $this->gender,
			'size' => $this->size
        ];
    }  
}