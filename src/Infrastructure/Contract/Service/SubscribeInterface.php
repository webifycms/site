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
 * Subscribe service interface for newsletter subscription.
 */
interface SubscribeInterface
{
	/**
	 * Subscribe an email to the newsletter.
	 *
	 * @param array{name: string, email: string} $data
	 *
	 * @return array{statusCode: int, success: bool, message: string}
	 */
	public function subscribe(array $data): array;
}
