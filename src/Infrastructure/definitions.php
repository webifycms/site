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

use App\Infrastructure\Contract\HttpClientInterface;
use App\Infrastructure\Contract\Service\SubscribeInterface;
use App\Infrastructure\Persistence\Filesystem\{ExtensionsReader, PostReader, RateLimitStorage, ThemesReader};
use App\Infrastructure\Persistence\GitHub\DocsReader;
use App\Infrastructure\Presentation\Api\Middleware\{ExceptionHandler, RateLimiter};
use App\Infrastructure\Presentation\Api\{RequestParser, ResponseBuilder};
use App\Infrastructure\Presentation\Http\Middleware\PageCache;
use App\Infrastructure\Service\{ErrorHandler, HttpClient, SitemapGenerator, Subscribe, Url, View};
use League\CommonMark\{ConverterInterface, GithubFlavoredMarkdownConverter};
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Webify\Base\Application\Service\ConfigInterface;
use Webify\Base\Infrastructure\Contract\ErrorHandlerInterface;
use Webify\Base\Infrastructure\Environment\Environment as AppEnvironment;

use function DI\factory;

return [
	// Error handler service
	ErrorHandlerInterface::class     => factory(
		static function (ContainerInterface $container): ErrorHandler {
			$psr17Factory = $container->get(Psr17Factory::class);

			if (!$psr17Factory instanceof Psr17Factory) {
				throw new UnexpectedValueException(sprintf(
					'Container entry "%s" must be an instance of %s.',
					Psr17Factory::class,
					Psr17Factory::class,
				));
			}

			$view = $container->get(View::class);

			if (!$view instanceof View) {
				throw new UnexpectedValueException(sprintf(
					'Container entry "%s" must be an instance of %s.',
					View::class,
					View::class,
				));
			}

			return new ErrorHandler($psr17Factory, $view);
		}
	),
	// Add page cache using "symfony/cache"
	CacheInterface::class            => factory(
		static function (ConfigInterface $config): CacheInterface {
			$namespace = $config->get('id', 'webifycms');

			if (!is_string($namespace)) {
				throw new UnexpectedValueException('Config key "id" must be a string.');
			}

			$lifetime = $config->get('cache.lifetime', 3600);

			if (!is_int($lifetime)) {
				throw new UnexpectedValueException('Config key "cache.lifetime" must be an integer.');
			}

			return new FilesystemAdapter($namespace, $lifetime, $config->cachePath . '/pages');
		}
	),
	PageCache::class                 => factory(
		static function (
			CacheInterface $cache,
			ConfigInterface $config,
			AppEnvironment $environment,
			Psr17Factory $factory
		): PageCache {
			return new PageCache($cache, $config, $environment, $factory);
		}
	),
	// Rate limiter
	RateLimiter::class               => factory(
		static function (
			Psr17Factory $factory,
			RateLimitStorage $storage,
		): RateLimiter {
			return new RateLimiter($factory, $storage);
		}
	),
	// Rate limit storage (file-based)
	RateLimitStorage::class          => factory(
		static function (ConfigInterface $config): RateLimitStorage {
			return new RateLimitStorage($config);
		}
	),
	Url::class                       => factory(
		static fn (ConfigInterface $config) => new Url($config)
	),
	// Sitemap XML generator
	SitemapGenerator::class          => factory(
		static function (Url $url, PostReader $posts, DocsReader $docs): SitemapGenerator {
			return new SitemapGenerator($url, $posts, $docs);
		}
	),
	// Extensions data reader
	ExtensionsReader::class          => factory(
		static function (ConfigInterface $config): ExtensionsReader {
			return new ExtensionsReader($config);
		}
	),
	// Themes data reader
	ThemesReader::class              => factory(
		static function (ConfigInterface $config): ThemesReader {
			return new ThemesReader($config);
		}
	),
	// CommonMark converter with full GFM support (tables, strikethrough, autolinks, etc.)
	// + HeadingPermalinkExtension to add id attributes to headings so internal anchor links work.
	ConverterInterface::class        => factory(
		static function (): GithubFlavoredMarkdownConverter {
			$config = [
				'heading_permalink' => [
					'insert'               => 'none',
					'apply_id_to_heading'  => true,
					'id_prefix'            => '',
					'fragment_prefix'      => '',
				],
			];
			$converter = new GithubFlavoredMarkdownConverter($config);

			$converter->getEnvironment()->addExtension(new HeadingPermalinkExtension());

			return $converter;
		}
	),
	DocsReader::class                => factory(
		static function (
			ConverterInterface $converter,
			LoggerInterface $logger,
			CacheInterface $cache,
			HttpClientInterface $httpClient,
			Psr17Factory $psr17Factory
		): DocsReader {
			return new DocsReader($converter, $logger, $cache, $httpClient, $psr17Factory);
		}
	),
	// Subscribe service
	SubscribeInterface::class        => factory(
		static function (
			HttpClientInterface $httpClient,
			Psr17Factory $requestFactory,
			ConfigInterface $config,
			LoggerInterface $logger
		): SubscribeInterface {
			return new Subscribe($httpClient, $requestFactory, $config, $logger);
		}
	),
	// Validator
	ValidatorInterface::class        => factory(
		static function (): ValidatorInterface {
			return Validation::createValidatorBuilder()
				->getValidator()
			;
		}
	),
	// HTTP client
	HttpClientInterface::class       => factory(
		static function (ConfigInterface $config): HttpClientInterface {
			$custom = $config->get('httpClient', []);

			if (!is_array($custom)) {
				throw new UnexpectedValueException('Config key "httpClient" must be an array.');
			}

			$version = $config->get('version', '0.1.0');

			if (!is_string($version)) {
				throw new UnexpectedValueException('Config key "version" must be a string.');
			}

			$customConfig = [];

			foreach ($custom as $key => $value) {
				if (!is_string($key)) {
					throw new UnexpectedValueException('Config key "httpClient" must only contain string keys.');
				}

				$customConfig[$key] = $value;
			}

			return new HttpClient(array_merge([
				'connect_timeout' => 5,
				'timeout'         => 30,
				'http_errors'     => true,
				'headers'         => [
					'Accept'     => 'application/json',
					'User-Agent' => sprintf('WebifyCMS/%s', $version),
				],
			], $customConfig));
		}
	),
	// API Response Builder
	ResponseBuilder::class           => factory(
		static function (
			Psr17Factory $psr17Factory
		): ResponseBuilder {
			return new ResponseBuilder($psr17Factory);
		}
	),
	// Request Parser
	RequestParser::class             => factory(
		static function (
			ValidatorInterface $validator,
			LoggerInterface $logger,
		): RequestParser {
			return new RequestParser($validator, $logger);
		}
	),
	// Exception Handler Middleware
	ExceptionHandler::class          => factory(
		static function (ResponseBuilder $responseBuilder, LoggerInterface $logger): ExceptionHandler {
			return new ExceptionHandler($responseBuilder, $logger);
		}
	),
];
