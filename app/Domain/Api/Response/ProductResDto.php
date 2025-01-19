<?php declare(strict_types = 1);

namespace App\Domain\Api\Response;

use App\Domain\Product\Product;
use DateTimeInterface;

final class ProductResDto
{

	public int $id;

	public string $name;

	public float $price;

	public static function from(Product $product): self
	{
		$self = new self();
		$self->id = $product->getId();
		$self->name = $product->getName();
		$self->price = $product->getPrice();

		return $self;
	}

}
