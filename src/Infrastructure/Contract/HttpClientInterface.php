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

namespace App\Infrastructure\Contract;

use Psr\Http\Client\ClientInterface;

/**
 * Contract for HTTP client.
 */
interface HttpClientInterface
{
	/**
	 * Get the HTTP client.
	 *
	 * @param array<string, mixed> $config the HTTP client configuration
	 */
	public function getClient(array $config = []): ClientInterface;
}
