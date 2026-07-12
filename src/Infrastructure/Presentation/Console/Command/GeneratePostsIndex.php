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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Webify\Base\Application\Service\ConfigInterface;

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
	 * The maximum length of the excerpt.
	 */
	private const int EXCERPT_MAX_LENGTH = 155;

	/**
	 * The constructor.
	 */
	public function __construct(
		private ConfigInterface $config,
	) {}

	/**
	 * Executes the command.
	 */
	public function __invoke(InputInterface $input, OutputInterface $output): int
	{
		$sourceDir = $this->config->basePath . '/resources/posts';
		// Phase 2 — build index from .md files
		$mdFiles = glob($sourceDir . '/*.md');

		if (false === $mdFiles || [] === $mdFiles) {
			$output->writeln('<error>No Markdown files found.</error>');

			return Command::FAILURE;
		}

		$index = [];

		foreach ($mdFiles as $mdFile) {
			$basename = basename($mdFile);

			if ('posts.json' === pathinfo($basename, PATHINFO_FILENAME)) {
				continue;
			}

			$contents = file_get_contents($mdFile);

			if (false === $contents) {
				$output->writeln(sprintf('<error>Failed to read: %s</error>', $basename));

				continue;
			}

			$post = $this->parseFrontMatter($contents, $basename, $output);

			if (null === $post) {
				continue;
			}

			$index[] = $post;
		}

		if ([] === $index) {
			$output->writeln('<error>No valid posts found.</error>');

			return Command::FAILURE;
		}

		usort($index, static fn (array $a, array $b): int => $b['date'] <=> $a['date']);

		file_put_contents(
			$sourceDir . '/posts.json',
			json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n",
		);

		$output->writeln(sprintf('<info>Generated posts.json with %d entries.</info>', count($index)));

		return Command::SUCCESS;
	}

	/**
	 * Parses YAML front matter and body from a .md file.
	 *
	 * @return null|array{title: string, slug: string, date: string, excerpt: string, file: string}
	 */
	private function parseFrontMatter(string $contents, string $basename, OutputInterface $output): ?array
	{
		if (!str_starts_with($contents, '---')) {
			$output->writeln(sprintf('<comment>Skipping (no front matter): %s</comment>', $basename));

			return null;
		}

		$end = strpos($contents, '---', 3);

		if (false === $end) {
			$output->writeln(sprintf('<comment>Skipping (unclosed front matter): %s</comment>', $basename));

			return null;
		}

		$yamlBlock  = substr($contents, 3, $end - 3);
		$body       = substr($contents, $end + 3);
		$metadata   = Yaml::parse($yamlBlock);

		if (!is_array($metadata) || !isset($metadata['title'], $metadata['slug'], $metadata['date'])) {
			$output->writeln(
				sprintf('<comment>Skipping (missing front-matter fields): %s</comment>', $basename)
			);

			return null;
		}

		return [
			'title'   => $metadata['title'],
			'slug'    => $metadata['slug'],
			'date'    => $metadata['date'],
			'excerpt' => $this->generateExcerpt($body),
			'file'    => $basename,
		];
	}

	/**
	 * Derives an SEO-length excerpt from the first paragraph of the body.
	 */
	private function generateExcerpt(string $markdown): string
	{
		$lines      = explode("\n", $markdown);
		$text       = '';
		$inPara     = false;
		$isFrontEnd = false;

		foreach ($lines as $line) {
			$trimmed = trim($line);

			// Skip the closing front-matter delimiter if body starts with it
			if ('---' === $trimmed) {
				$isFrontEnd = true;

				continue;
			}

			if ($isFrontEnd) {
				$isFrontEnd = false;

				continue;
			}

			// Empty line ends the current paragraph
			if ('' === $trimmed) {
				if ($inPara) {
					break;
				}

				continue;
			}

			// Skip ATX headings, thematic breaks, blockquotes, and list markers
			if (
				(bool) preg_match('/^#{1,6}\s/', $trimmed)
				|| (bool) preg_match('/^[-*_]{3,}$/', $trimmed)
				|| (bool) preg_match('/^>\s?/', $trimmed)
				|| (bool) preg_match('/^\d*\.\s|^[-*+]\s/', $trimmed)
			) {
				continue;
			}

			$inPara = true;
			// Strip inline Markdown formatting
			$clean = preg_replace(
				['/\[([^]]*)]\([^)]*\)/', '/[*_~`]{1,2}/', '/^#{1,6}\s*/'],
				['$1', '', ''],
				$trimmed
			);
			$text .= ' ' . $clean;
		}

		$excerpt = trim($text);
		$excerpt = html_entity_decode($excerpt, ENT_QUOTES);

		if (mb_strlen($excerpt) > self::EXCERPT_MAX_LENGTH) {
			$excerpt = mb_substr($excerpt, 0, self::EXCERPT_MAX_LENGTH - 3);
			$pos     = mb_strrpos($excerpt, ' ');

			if (false !== $pos) {
				$excerpt = mb_substr($excerpt, 0, $pos) . '...';
			}
		}

		return $excerpt;
	}
}
