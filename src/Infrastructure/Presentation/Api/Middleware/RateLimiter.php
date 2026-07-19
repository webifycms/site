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

namespace App\Infrastructure\Presentation\Api\Middleware;

use App\Infrastructure\Persistence\Filesystem\RateLimitStorage;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

use function array_filter;
use function array_values;
use function count;
use function explode;
use function json_encode;
use function md5;
use function time;

/**
 * Rate limiter middleware — limits the number of requests per IP within a sliding time window.
 *
 * This middleware uses a fixed-window counter approach: for each client IP it
 * stores the timestamps of recent requests. When the number of timestamps
 * inside the current window exceeds MAX_REQUESTS the request is rejected
 * with HTTP 429 (Too Many Requests).
 */
final readonly class RateLimiter implements MiddlewareInterface
{
	/**
	 * Maximum number of requests a single client is allowed to make
	 * within the sliding time window before being throttled.
	 */
	private const int MAX_REQUESTS = 10;

	/**
	 * The sliding window size in seconds.
	 *
	 * Any request whose timestamp is older than (now - WINDOW_SECONDS) is
	 * considered expired and discarded from the counter. With the current
	 * value of 60, each client can make up to MAX_REQUESTS (10) requests
	 * per 60-second period. The window "slides" forward on every new
	 * request, so the effective limit is 10 requests in the *last* 60
	 * seconds, not 10 requests per calendar minute.
	 */
	private const int WINDOW_SECONDS = 60;

	public function __construct(
		private Psr17Factory $factory,
		private RateLimitStorage $storage,
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$key = md5($this->getClientIp($request));

		// Run periodic clean-up of expired files in the background.
		$this->storage->cleanup(self::WINDOW_SECONDS);

		$timestamps = $this->storage->load($key);
		$now        = time();

		// Discard timestamps that have fallen outside the sliding window.
		$timestamps = array_values(array_filter(
			$timestamps,
			static fn (int $timestamp): bool => $now - self::WINDOW_SECONDS < $timestamp,
		));

		if (count($timestamps) >= self::MAX_REQUESTS) {
			return $this->factory->createResponse(429)
				->withHeader('Content-Type', 'application/json')
				->withBody(
					$this->factory->createStream(
						(string) json_encode(
							[
								'success' => false,
								'message' => 'Too many requests. Please try again later.',
							],
							JSON_UNESCAPED_UNICODE,
						),
					),
				)
			;
		}

		$timestamps[] = $now;

		$this->storage->save($key, $timestamps);

		return $handler->handle($request);
	}

	/**
	 * Extract the client IP address from the request, respecting
	 * Cloudflare and reverse-proxy headers.
	 */
	private function getClientIp(ServerRequestInterface $request): string
	{
		$cfConnectingIp = $request->getHeaderLine('CF-Connecting-IP');

		if ('' !== $cfConnectingIp) {
			return $cfConnectingIp;
		}

		$forwardedFor = $request->getHeaderLine('X-Forwarded-For');

		if ('' !== $forwardedFor) {
			return explode(',', $forwardedFor)[0];
		}

		return $request->getServerParams()['REMOTE_ADDR'] ?? '127.0.0.1';
	}
}
