<?php declare(strict_types = 1);

namespace App\Domain\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProductReqDto
{
	public float $price = 0.0;

	/** @Assert\NotBlank */
	public string $name;
}
