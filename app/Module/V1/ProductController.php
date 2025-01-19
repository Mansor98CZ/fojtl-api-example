<?php

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Request\CreateProductReqDto;
use App\Domain\Api\Request\DeleteProductReqDto;
use App\Domain\Api\Request\UpdateProductReqDto;
use App\Domain\Api\Response\ProductResDto;
use App\Model\Exception\Runtime\Database\EntityNotFoundException;
use Doctrine\DBAL\Exception\DriverException;
use Nette\Http\IResponse;
use Nette\Utils\Json;

/**
 * @Apitte\Path("/product")
 */
class ProductController extends BaseV1Controller
{
	private ProductsFacade $productsFacade;

	public function __construct(ProductsFacade $productsFacade)
	{
		$this->productsFacade = $productsFacade;
	}

	/**
	 * @Apitte\Path("/")
	 * @Apitte\Method({"GET"})
	 */
	public function getAction(ApiRequest $request): ProductResDto|array
	{
		$id = $request->getParameter('id');
		if ($id) {
			try {
				return $this->productsFacade->findOne($id);
			} catch (EntityNotFoundException $e) {
				throw ClientErrorException::create()
					->withMessage('Product not found')
					->withCode(IResponse::S404_NotFound);
			}
		}
		return $this->productsFacade->findAll();
	}

	/**
	 * @Apitte\Path("/")
	 * @Apitte\Method({"POST"})
	 * @Apitte\RequestBody(entity="App\Domain\Api\Request\CreateProductReqDto")
	 */
	public function postAction(ApiRequest $request, ApiResponse $response): mixed
	{
		/** @var CreateProductReqDto $dto */
		$dto = $request->getParsedBody();
		try {
			$product = $this->productsFacade->create($dto);
			return $response
				->writeBody(Json::encode(['id' => $product->getId()]))
				->withStatus(IResponse::S201_Created)
				->withHeader('Content-Type', 'application/json');
		} catch (DriverException $e) {
			throw ServerErrorException::create()
				->withMessage('Cannot create product')
				->withPrevious($e);
		}
	}

	/**
	 * @Apitte\Path("/")
	 * @Apitte\Method({"PUT"})
	 * @Apitte\RequestBody(entity="App\Domain\Api\Request\UpdateProductReqDto")
	 */
	public function putAction(ApiRequest $request, ApiResponse $response): mixed
	{
		/** @var UpdateProductReqDto $dto */
		$dto = $request->getParsedBody();
		try {
			$this->productsFacade->update($dto);
			return $response->withStatus(IResponse::S204_NoContent)
				->withHeader('Content-Type', 'application/json');
		} catch (DriverException $e) {
			throw ServerErrorException::create()
				->withMessage('Cannot update product')
				->withPrevious($e);
		}
	}
	/**
	 * @Apitte\Path("/")
	 * @Apitte\Method({"DELETE"})
	 * @Apitte\RequestBody(entity="App\Domain\Api\Request\DeleteProductReqDto")
	 */
	public function deleteAction(ApiRequest $request, ApiResponse $response): mixed
	{
		/** @var DeleteProductReqDto $dto */
		$dto = $request->getParsedBody();
		try {
			$this->productsFacade->delete($dto);
			return $response->withStatus(IResponse::S204_NoContent)
				->withHeader('Content-Type', 'application/json');
		} catch (DriverException $e) {
			throw ServerErrorException::create()
				->withMessage('Cannot update product')
				->withPrevious($e);
		}
	}
}
