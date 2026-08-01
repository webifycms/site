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

namespace App\Infrastructure\Persistence\Filesystem;

use Symfony\Component\Yaml\Yaml;
use Webify\Base\Application\Service\ConfigInterface;

/**
 * Generates a post-index from Markdown files with YAML front matter.
 *
 * Scans the posts directory for .md files, parses front matter,
 * generates excerpts, and returns a sorted index array.
 *
 * @phpstan-type PostMeta array{
 *     title: string,
 *     slug: string,
 *     date: string,
 *     updated: string,
 *     excerpt: string,
 *     file: string,
 * }
 */
final readonly class PostIndexer
{
	/**
	 * The maximum length of the excerpt.
	 */
	private const int EXCERPT_MAX_LENGTH = 155;

	/**
	 * @var string the path to the posts directory
	 */
	private string $path;

	/**
	 * The constructor.
	 */
	public function __construct(
		private ConfigInterface $config,
	) {
		$this->path = $this->config->basePath . '/resources/posts';
	}

	/**
	 * Generates the post-index from .md files.
	 *
	 * Scans the posts directory, parses YAML front matter from each file,
	 * generates excerpts, and returns the index sorted by date descending.
	 *
	 * @return list<PostMeta>
	 */
	public function generate(): array
	{
		$mdFiles = glob($this->path . '/*.md');

		if (false === $mdFiles || [] === $mdFiles) {
			return [];
		}

		$index = [];

		foreach ($mdFiles as $mdFile) {
			$basename = basename($mdFile);

			if ('posts.json' === pathinfo($basename, PATHINFO_FILENAME)) {
				continue;
			}

			$contents = file_get_contents($mdFile);

			if (false === $contents) {
				continue;
			}

			$post = $this->parseFrontMatter($contents, $basename);

			if (null !== $post) {
				$index[] = $post;
			}
		}

		usort($index, static fn (array $a, array $b): int => $b['date'] <=> $a['date']);

		return $index;
	}

	/**
	 * Returns the post-directory path.
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * Parses YAML front matter and body from a .md file.
	 *
	 * @return null|PostMeta
	 */
	private function parseFrontMatter(string $contents, string $basename): ?array
	{
		if (!str_starts_with($contents, '---')) {
			return null;
		}

		$end = strpos($contents, '---', 3);

		if (false === $end) {
			return null;
		}

		$yamlBlock = substr($contents, 3, $end - 3);
		$body      = substr($contents, $end + 3);
		$metadata  = Yaml::parse($yamlBlock);

		if (!is_array($metadata) || !isset($metadata['title'], $metadata['slug'], $metadata['date'])) {
			return null;
		}

		return [
			'title'   => $metadata['title'],
			'slug'    => $metadata['slug'],
			'date'    => $metadata['date'],
			'updated' => $metadata['updated'] ?? $metadata['date'],
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

			if ('---' === $trimmed) {
				$isFrontEnd = true;

				continue;
			}

			if ($isFrontEnd) {
				$isFrontEnd = false;

				continue;
			}

			if ('' === $trimmed) {
				if ($inPara) {
					break;
				}

				continue;
			}

			if (
				(bool) preg_match('/^#{1,6}\s/', $trimmed)
				|| (bool) preg_match('/^[-*_]{3,}$/', $trimmed)
				|| (bool) preg_match('/^>\s?/', $trimmed)
				|| (bool) preg_match('/^\d*\.\s|^[-*+]\s/', $trimmed)
			) {
				continue;
			}

			$inPara = true;
			$clean  = preg_replace(
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
