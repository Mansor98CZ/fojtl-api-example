<?php declare(strict_types = 1);

namespace App\Domain\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteProductReqDto
{
	/**
	 * @Assert\NotBlank
	 */
	public int $id;
}
