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

namespace App\Infrastructure\Presentation\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Webify\Base\Application\Service\ConfigInterface;

/**
 * Command to clear the page cache.
 */
#[AsCommand(
	name: 'pageCache:clear',
	description: 'Command to clear the page cache.',
)]
final readonly class ClearPageCache
{
	public function __construct(
		private ConfigInterface $config
	) {}

	/**
	 * Executes the command.
	 */
	public function __invoke(OutputInterface $output): int
	{
		$path = $this->config->cachePath . '/pages';

		return $this->deleteRecursively($path) ? Command::SUCCESS : Command::FAILURE;
	}

	/**
	 * Delete a directory and its contents recursively.
	 */
	private function deleteRecursively(string $directory): bool
	{
		$files = array_diff(scandir($directory), ['.', '..']);

		foreach ($files as $file) {
			$path = $directory . DIRECTORY_SEPARATOR . $file;

			if (is_dir($path)) {
				$this->deleteRecursively($path);

				continue;
			}

			unlink($path);
		}

		return rmdir($directory);
	}
}
