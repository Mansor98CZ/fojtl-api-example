<?php declare(strict_types = 1);

namespace App\Domain\Api\Facade;

use Apitte\Core\Exception\Api\ClientErrorException;
use App\Domain\Api\Request\CreateProductReqDto;
use App\Domain\Api\Request\DeleteProductReqDto;
use App\Domain\Api\Request\UpdateProductReqDto;
use App\Domain\Api\Response\ProductResDto;
use App\Domain\Product\Product;
use App\Model\Database\EntityManagerDecorator;
use App\Model\Exception\Runtime\Database\EntityNotFoundException;
use Nette\Http\IResponse;

final class ProductsFacade
{

	public function __construct(private EntityManagerDecorator $em)
	{
	}

	/**
	 * @param mixed[] $criteria
	 * @param string[] $orderBy
	 * @return ProductResDto[]
	 */
	public function findBy(array $criteria = [], array $orderBy = ['id' => 'ASC'], int $limit = 10, int $offset = 0): array
	{
		$entities = $this->em->getRepository(Product::class)->findBy($criteria, $orderBy, $limit, $offset);
		$result = [];

		foreach ($entities as $entity) {
			$result[] = ProductResDto::from($entity);
		}

		return $result;
	}

	/**
	 * @return ProductResDto[]
	 */
	public function findAll(): array
	{
		$products = $this->em->getRepository(Product::class)->findAll();
		$result = [];
		foreach ($products as $entity) {
			$result[] = ProductResDto::from($entity);
		}
		return $result;
	}

	/**
	 * @param mixed[] $criteria
	 * @param string[] $orderBy
	 */
	public function findOneBy(array $criteria, ?array $orderBy = null): ProductResDto
	{
		$entity = $this->em->getRepository(Product::class)->findOneBy($criteria, $orderBy);

		if ($entity === null) {
			throw new EntityNotFoundException();
		}

		return ProductResDto::from($entity);
	}

	public function findOne(int $id): ProductResDto
	{
		return $this->findOneBy(['id' => $id]);
	}

	public function create(CreateProductReqDto $dto): Product
	{
		$product = new Product();
		$product->setPrice($dto->price)
			->setName($dto->name);

		$this->em->persist($product);
		$this->em->flush($product);

		return $product;
	}

	public function update(UpdateProductReqDto $dto): Product
	{
		$product = $this->em->getRepository(Product::class)->find($dto->id);
		if (!$product instanceof Product) {
			throw ClientErrorException::create()
				->withMessage('Product not found')
				->withCode(IResponse::S404_NotFound);
		}
		$product->setName($dto->name)
			->setPrice($dto->price);
		$this->em->flush();
		return $product;

	}

	public function delete(DeleteProductReqDto $dto): void
	{
		$product = $this->em->getRepository(Product::class)->find($dto->id);
		if (!$product instanceof Product) {
			throw ClientErrorException::create()
				->withMessage('Product not found')
				->withCode(IResponse::S404_NotFound);
		}
		$this->em->remove($product);
		$this->em->flush();
	}

}
