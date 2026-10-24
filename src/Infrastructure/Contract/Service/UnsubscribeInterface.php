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

namespace App\Infrastructure\Contract\Service;

/**
 * Unsubscribe service interface for newsletter deletion requests.
 */
interface UnsubscribeInterface
{
	/**
	 * Unsubscribe an email from the newsletter (request deletion).
	 *
	 * @param array{email: string} $data
	 *
	 * @return array{statusCode: int, success: bool, message: string}
	 */
	public function unsubscribe(array $data): array;
}