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

namespace App\Infrastructure\Helper;

/**
 * Directory utility helper.
 */
final class Directory
{
	/**
	 * Delete a directory and its contents recursively.
	 */
	public static function deleteRecursively(string $directory): bool
	{
		if (!is_dir($directory)) {
			return false;
		}

		$files = array_diff(scandir($directory), ['.', '..']);

		foreach ($files as $file) {
			$path = $directory . DIRECTORY_SEPARATOR . $file;

			if (is_dir($path)) {
				self::deleteRecursively($path);

				continue;
			}

			unlink($path);
		}

		return rmdir($directory);
	}
}
