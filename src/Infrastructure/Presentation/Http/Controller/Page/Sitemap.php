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

namespace App\Infrastructure\Presentation\Http\Controller\Page;

use App\Infrastructure\Persistence\Filesystem\PostReader;
use App\Infrastructure\Persistence\GitHub\DocsReader;
use App\Infrastructure\Service\Url;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * Generates the sitemap.xml for search engines.
 */
final readonly class Sitemap
{
	/**
	 * Static pages to include in the sitemap.
	 *
	 * @var array<array{loc: string, priority: float, changefreq: string}>
	 */
	private const array STATIC_PAGES = [
		['loc' => '/', 'priority' => 1.0, 'changefreq' => 'weekly'],
		['loc' => '/extensions', 'priority' => 0.8, 'changefreq' => 'monthly'],
		['loc' => '/license', 'priority' => 0.3, 'changefreq' => 'yearly'],
		['loc' => '/publishing', 'priority' => 0.9, 'changefreq' => 'weekly'],
		['loc' => '/docs', 'priority' => 0.8, 'changefreq' => 'weekly'],
	];

	/**
	 * The constructor.
	 */
	public function __construct(
		private Psr17Factory $psr17Factory,
		private Url $url,
		private PostReader $posts,
		private DocsReader $docs,
	) {}

	/**
	 * Handles the sitemap request.
	 */
	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		$xml = $this->build();

		return $this->psr17Factory
			->createResponse(200)
			->withBody($this->psr17Factory->createStream($xml))
			->withHeader('Content-Type', 'application/xml')
		;
	}

	/**
	 * Builds the sitemap XML string.
	 */
	private function build(): string
	{
		$urls = [];

		foreach (self::STATIC_PAGES as $page) {
			$urls[] = $this->urlEntry($page['loc'], $page['priority'], $page['changefreq']);
		}

		foreach ($this->posts->findAll() as $post) {
			$urls[] = $this->urlEntry(
				'/publishing/' . $post['slug'],
				0.8,
				'monthly',
				$post['date']
			);
		}

		try {
			foreach ($this->docs->findAll() as $doc) {
				$urls[] = $this->urlEntry('/docs/' . $doc['slug'], 0.7, 'monthly');
			}
		} catch (InvalidArgumentException) {
			// Docs unavailable — skip them in the sitemap.
		}

		return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
			. '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
			. "\n"
			. implode("\n", $urls)
			. "\n"
			. '</urlset>';
	}

	/**
	 * Builds a single <url> entry.
	 */
	private function urlEntry(string $loc, float $priority, string $changefreq, string $lastmod = ''): string
	{
		$url = $this->url->resolveUrl($loc);
		$xml = '  <url>' . "\n"
			. '    <loc>' . htmlspecialchars($url, ENT_XML1) . '</loc>' . "\n";

		if ('' !== $lastmod) {
			$xml .= '    <lastmod>' . htmlspecialchars($lastmod, ENT_XML1) . '</lastmod>' . "\n";
		}

		$xml .= '    <changefreq>' . $changefreq . '</changefreq>' . "\n"
			. '    <priority>' . number_format($priority, 1) . '</priority>' . "\n"
			. '  </url>';

		return $xml;
	}
}
