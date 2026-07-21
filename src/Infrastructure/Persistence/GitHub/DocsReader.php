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

namespace App\Infrastructure\Persistence\GitHub;

use App\Infrastructure\Contract\HttpClientInterface;
use GuzzleHttp\Exception\ConnectException;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Exception\CommonMarkException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\{CacheInterface, ItemInterface};

/**
 * Reads documentation markdown files from the GitHub repository.
 *
 * @phpstan-type Doc array{
 *     title: string,
 *     slug: string,
 *     html: string,
 * }
 */
final readonly class DocsReader
{
	private const string GITHUB_API = 'https://api.github.com/repos/webifycms/app/contents/docs';

	private const string GITHUB_RAW = 'https://raw.githubusercontent.com/webifycms/app/main/docs';

	public function __construct(
		private ConverterInterface $converter,
		private LoggerInterface $logger,
		private CacheInterface $cache,
		private HttpClientInterface $httpClient,
		private Psr17Factory $psr17Factory,
	) {}

	/**
	 * Returns a list of all available docs (title, slug).
	 *
	 * @return array<int, array{title: string, slug: string}>
	 *
	 * @throws InvalidArgumentException
	 */
	public function findAll(): array
	{
		return $this->cache->get('docs_index', function (): array {
			$files = $this->fetchList();
			$docs  = [];

			foreach ($files as $file) {
				if (is_string($file['name'] ?? null) && str_ends_with($file['name'], '.md')) {
					$docs[] = [
						'title' => $this->titleFromName($file['name']),
						'slug'  => $this->slugFromName($file['name']),
					];
				}
			}

			return $docs;
		}, 3600);
	}

	/**
	 * Finds a single doc by its slug and returns its rendered HTML.
	 *
	 * @return null|Doc
	 *
	 * @throws InvalidArgumentException
	 */
	public function findBySlug(string $slug): ?array
	{
		foreach ($this->findAll() as $doc) {
			if ($doc['slug'] === $slug) {
				return $this->fetchDoc($doc);
			}
		}

		return null;
	}

	/**
	 * @param array{title: string, slug: string} $doc
	 *
	 * @return null|Doc
	 *
	 * @throws InvalidArgumentException
	 */
	private function fetchDoc(array $doc): ?array
	{
		$cacheKey = 'doc_' . $doc['slug'];

		return $this->cache->get($cacheKey, function (ItemInterface $item, bool &$save) use ($doc): ?array {
			$path     = $doc['slug'] . '.md';
			$markdown = $this->fetchRaw($path);

			if (null === $markdown) {
				$save = false;

				return null;
			}

			try {
				$html = $this->converter->convert($markdown)->getContent();
			} catch (CommonMarkException $e) {
				$this->logger->error('Failed to convert doc markdown to HTML', [
					'exception' => $e,
					'slug'      => $doc['slug'],
				]);

				$html = '';
			}

			return [
				'title' => $doc['title'],
				'slug'  => $doc['slug'],
				'html'  => $html,
			];
		}, 3600);
	}

	/**
	 * @return array<int, array{name: string}>
	 */
	private function fetchList(): array
	{
		$request = $this->psr17Factory
			->createRequest('GET', self::GITHUB_API)
			->withHeader('User-Agent', 'WebifyCMS-Site/1.0')
			->withHeader('Accept', 'application/vnd.github.v3+json')
		;

		try {
			$response = $this->httpClient->getClient(['timeout' => 10])->sendRequest($request);
		} catch (ClientExceptionInterface|ConnectException $e) {
			$this->logger->error('Failed to fetch docs list from GitHub API', [
				'exception' => $e,
			]);

			return [];
		}

		/** @var null|array<int, array{name: string}> $data */
		$data = json_decode((string) $response->getBody(), true);

		if (!is_array($data)) {
			return [];
		}

		return $data;
	}

	private function fetchRaw(string $path): ?string
	{
		$url = self::GITHUB_RAW . '/' . $path;

		$request = $this->psr17Factory
			->createRequest('GET', $url)
			->withHeader('User-Agent', 'WebifyCMS-Site/1.0')
		;

		try {
			$response = $this->httpClient->getClient(['timeout' => 10])->sendRequest($request);
		} catch (ClientExceptionInterface|ConnectException $e) {
			$this->logger->error('Failed to fetch doc from GitHub', [
				'path'      => $path,
				'exception' => $e,
			]);

			return null;
		}

		return (string) $response->getBody();
	}

	private function titleFromName(string $name): string
	{
		$withoutExt = (string) pathinfo($name, PATHINFO_FILENAME);
		$title      = str_replace(['-', '_'], ' ', $withoutExt);

		return ucwords($title);
	}

	private function slugFromName(string $name): string
	{
		return strtolower((string) pathinfo($name, PATHINFO_FILENAME));
	}
}
