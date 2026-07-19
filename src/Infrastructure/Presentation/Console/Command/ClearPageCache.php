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

use App\Infrastructure\Helper\Directory;
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

		return Directory::deleteRecursively($path) ? Command::SUCCESS : Command::FAILURE;
	}
}
