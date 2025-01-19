<?php declare(strict_types = 1);

namespace App\Model\Api\Middleware;

use App\Model\Api\RequestAttributes;
use Contributte\Middlewares\IMiddleware;
use Contributte\Middlewares\Security\IAuthenticator;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationMiddleware implements IMiddleware
{

	private const WHITELIST_PATHS = ['/api/public'];

	private IAuthenticator $authenticator;

	public function __construct(IAuthenticator $authenticator)
	{
		$this->authenticator = $authenticator;
	}

	protected function denied(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$response->getBody()->write(Json::encode([
			'status' => 'error',
			'message' => 'Client authentication failed',
			'code' => 401,
		]));

		return $response
			->withHeader('Content-Type', 'application/json')
			->withStatus(401);
	}

	protected function isWhitelisted(ServerRequestInterface $request): bool
	{
		foreach (self::WHITELIST_PATHS as $whitelist) {
			if (str_starts_with($request->getUri()->getPath(), $whitelist)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Authenticate user from given request
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		return $next($request, $response);
	}

}
