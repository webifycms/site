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

use App\Infrastructure\Persistence\Filesystem\PostIndexer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates posts.json index from Markdown files with YAML front matter.
 *
 * Run after creating or updating a .md file to rebuild posts.json.
 */
#[AsCommand(
	name: 'posts:generate-index',
	description: 'Generates posts.json from .md files with YAML front matter.',
)]
final readonly class GeneratePostsIndex
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private PostIndexer $indexer,
	) {}

	/**
	 * Executes the command.
	 */
	public function __invoke(InputInterface $input, OutputInterface $output): int
	{
		$index = $this->indexer->generate();

		if ([] === $index) {
			$output->writeln('<error>No valid posts found.</error>');

			return Command::FAILURE;
		}

		file_put_contents(
			$this->indexer->getPath() . '/posts.json',
			json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n",
		);
		$output->writeln(
			sprintf('<info>Generated posts.json with %d entries.</info>', count($index))
		);

		return Command::SUCCESS;
	}
}
