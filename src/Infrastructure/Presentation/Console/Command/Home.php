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

/**
 * Home command, just an example command. It outputs a message to the console.
 */
#[AsCommand(
	name: 'app:home',
	description: 'Example command just outputs a message to the console.',
)]
final class Home
{
	/**
	 * Executes the command.
	 */
	public function __invoke(OutputInterface $output): int
	{
		$output->writeln($this->message());

		return Command::SUCCESS;
	}

	/**
	 * Returns a message to be output to the console.
	 */
	private function message(): string
	{
		return "Hello, world! Let's simply transform to web.";
	}
}
