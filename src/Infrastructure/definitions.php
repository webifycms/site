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

use App\Infrastructure\Persistence\GitHub\DocsReader;
use App\Infrastructure\Presentation\Http\Middleware\PageCache;
use App\Infrastructure\Service\{ErrorHandler, Url, View};
use League\CommonMark\{ConverterInterface, GithubFlavoredMarkdownConverter};
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Webify\Base\Application\Service\ConfigInterface;
use Webify\Base\Infrastructure\Contract\ErrorHandlerInterface;
use Webify\Base\Infrastructure\Environment\Environment as AppEnvironment;

use function DI\factory;

return [
	// Error handler service
	ErrorHandlerInterface::class => factory(
		static function (ContainerInterface $container) {
			return new ErrorHandler(
				$container->get(Psr17Factory::class),
				$container->get(View::class),
			);
		}
	),
	// Add page cache using "symfony/cache"
	CacheInterface::class        => factory(
		static function (ConfigInterface $config): CacheInterface {
			return new FilesystemAdapter(
				$config->get('id', 'webifycms'),
				$config->get('cache.lifetime', 3600),
				$config->cachePath . '/pages'
			);
		}
	),
	PageCache::class             => factory(
		static function (
			CacheInterface $cache,
			ConfigInterface $config,
			AppEnvironment $environment,
			Psr17Factory $factory
		): PageCache {
			return new PageCache($cache, $config, $environment, $factory);
		}
	),
	Url::class                   => factory(
		static fn (ConfigInterface $config) => new Url($config)
	),
	// CommonMark converter with full GFM support (tables, strikethrough, autolinks, etc.)
	// + HeadingPermalinkExtension to add id attributes to headings so internal anchor links work.
	ConverterInterface::class    => factory(
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
	DocsReader::class            => factory(
		static function (
			ConverterInterface $converter,
			LoggerInterface $logger,
			CacheInterface $cache
		): DocsReader {
			return new DocsReader($converter, $logger, $cache);
		}
	),
];
