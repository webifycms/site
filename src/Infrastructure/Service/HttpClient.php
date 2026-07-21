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

namespace App\Infrastructure\Service;

use App\Infrastructure\Contract\HttpClientInterface;
use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

/**
 * The concrete HTTP client implementation.
 */
final readonly class HttpClient implements HttpClientInterface
{
	/**
	 * The constructor.
	 *
	 * @param array<string, mixed> $defaultConfig the default HTTP client configuration
	 */
	public function __construct(
		private array $defaultConfig = []
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function getClient(array $config = []): ClientInterface
	{
		return new Client(array_merge($this->defaultConfig, $config));
	}
}
