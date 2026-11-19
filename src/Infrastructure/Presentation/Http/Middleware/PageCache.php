<?php

/**
 * The file is part of the "webifycms/site", WebifyCMS site.
 *
 * @see https://webifycms.com
 *
 * @copyright Copyright (c) 2026 WebifyCMS
 * @license https://webifycms.com/license
 * @author Mohammed Shifreen <mshifreen@gmail.com>
 */
declare(strict_types=1);

namespace App\Infrastructure\Presentation\Http\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Symfony\Contracts\Cache\{CacheInterface, ItemInterface};
use UnexpectedValueException;
use Webify\Base\Application\Service\ConfigInterface;
use Webify\Base\Infrastructure\Environment\Environment;

/**
 * Page cache middleware, it caches the page for the configured duration.
 */
final readonly class PageCache implements MiddlewareInterface
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private CacheInterface $cache,
		private ConfigInterface $config,
		private Environment $environment,
		private Psr17Factory $factory
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if ($this->environment->isDevelopment() || 'GET' !== $request->getMethod()) {
			return $handler->handle($request);
		}

		$key        = md5((string) $request->getUri());
		$ttl        = $this->getConfigInt('cache.lifetime', 86400);
		$revalidate = $this->getConfigInt('cache.revalidate', 86400);

		try {
			$cached = $this->cache->get($key, function (ItemInterface $item) use ($handler, $request, $ttl): array {
				$item->expiresAfter($ttl);

				$response = $handler->handle($request);

				$response->getBody()->rewind();

				$body = $response->getBody()->getContents();

				return [
					'status'  => $response->getStatusCode(),
					'reason'  => $response->getReasonPhrase(),
					'headers' => $response->getHeaders(),
					'body'    => $body,
					'version' => $response->getProtocolVersion(),
				];
			});

			$response = $this->factory->createResponse($cached['status'], $cached['reason']);
			$response = $response->withProtocolVersion($cached['version']);
			$response = $response->withBody($this->factory->createStream($cached['body']));

			foreach ($cached['headers'] as $name => $values) {
				$response = $response->withHeader($name, $values);
			}

			return $response->withHeader(
				'Cache-Control',
				sprintf(
					'public, max-age=%d, stale-while-revalidate=%d',
					$ttl,
					$revalidate
				)
			);
		} catch (InvalidArgumentException) {
			return $handler->handle($request);
		}
	}

	/**
	 * Returns an integer configuration value or fails with a descriptive error.
	 */
	private function getConfigInt(string $key, int $default): int
	{
		$value = $this->config->get($key, $default);

		if (!is_int($value)) {
			throw new UnexpectedValueException(sprintf(
				'Configuration "%s" must be an integer, %s given.',
				$key,
				get_debug_type($value),
			));
		}

		return $value;
	}
}
