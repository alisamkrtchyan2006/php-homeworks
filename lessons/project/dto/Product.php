<?php

declare(strict_types=1);

class Product
{
    public string $id;
    public string $name;
    public Category $category;
    public int $price;
    public int $quantity;

    public function __construct(string $id, string $name, Category $category, int $price, int $quantity)
    {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->price = $price;
        $this->quantity = $quantity;
    }
}