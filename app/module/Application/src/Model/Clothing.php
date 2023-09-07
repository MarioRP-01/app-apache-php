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
	public ?bool $kids;
	public ?int $gender_id;
	public ?int $category_id;

    public function exchangeArray(object|array $data): array {
        $this->uuid = $data['uuid'] ?? null;
		$this->name = $data['name'] ?? null;
		$this->brand = $data['brand'] ?? null;
		$this->price = $data['price'] ?? null;
		$this->description = $data['description'] ?? null;
		$this->primary_color = $data['primary_color'] ?? null;
		$this->label = $data['label'] ?? null;
		$this->kids = $data['kids'] ?? null;
		$this->gender_id = $data['gender_id'] ?? null;
		$this->category_id = $data['category_id'] ?? null;

        return [
            'uuid' => $this->uuid,
			'name' => $this->name,
			'brand' => $this->brand,
			'price' => $this->price,
			'description' => $this->description,
			'primary_color' => $this->primary_color,
			'label' => $this->label,
			'kids' => $this->kids,
			'gender_id' => $this->gender_id,
			'category_id' => $this->category_id
        ];
    }  
}