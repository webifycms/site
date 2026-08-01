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

use Webify\Base\Application\Service\ConfigInterface;

/**
 * Reads theme data from the resources/themes.json file.
 *
 * The JSON file is the single source of truth for theme metadata.
 * To add or update a theme, edit the JSON — no PHP changes needed.
 *
 * @phpstan-type Theme array{
 *     name: string,
 *     package: string,
 *     status: string,
 *     version: string,
 *     description: string,
 *     url: string,
 * }
 */
final readonly class ThemesReader
{
	/**
	 * @var string absolute path to resources/themes.json
	 */
	private string $path;

	/**
	 * The constructor.
	 */
	public function __construct(
		private ConfigInterface $config,
	) {
		$this->path = $this->config->basePath . '/resources/themes.json';
	}

	/**
	 * Returns all themes from the JSON file.
	 *
	 * @return Theme[]
	 */
	public function findAll(): array
	{
		if (!file_exists($this->path)) {
			return [];
		}

		$contents = file_get_contents($this->path);

		if (false === $contents) {
			return [];
		}

		/** @var null|Theme[] $data */
		$data = json_decode($contents, true);

		return is_array($data) ? $data : [];
	}
}
