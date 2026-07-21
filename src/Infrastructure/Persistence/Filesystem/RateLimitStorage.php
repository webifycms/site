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

use function array_filter;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function glob;
use function is_array;
use function is_dir;
use function json_decode;
use function json_encode;
use function mkdir;
use function time;
use function unlink;

/**
 * File-based storage for rate limit timestamps.
 *
 * Each client key gets a JSON file containing an array of Unix timestamps
 * representing the times at which requests were made. Expired entries
 * are pruned on read, and entire files are removed when they become empty.
 */
final readonly class RateLimitStorage
{
	/**
	 * How often (in seconds) to scan the storage directory and remove
	 * files that contain only expired timestamps. This keeps the disk
	 * clean without running on every request.
	 */
	private const int CLEANUP_INTERVAL = 300;

	/**
	 * The directory name where rate limit files are stored.
	 */
	private const string STORAGE_DIR = 'rate-limiter';

	/**
	 * The directory where rate limit files are stored.
	 * Each client key corresponds to a JSON file in this directory.
	 */
	private string $storageDir;

	/**
	 * The constructor.
	 *
	 * If the storage directory does not exist, it will be created.
	 */
	public function __construct(
		private ConfigInterface $config,
	) {
		$path = $this->config->cachePath . DIRECTORY_SEPARATOR . self::STORAGE_DIR;

		if (!is_dir($path)) {
			mkdir($path, 0o755, true);
		}

		$this->storageDir = $path;
	}

	/**
	 * Load the list of request timestamps for the given key.
	 *
	 * @return list<int> Unix timestamps of requests within the sliding window
	 */
	public function load(string $key): array
	{
		$file = $this->filePath($key);

		if (!file_exists($file)) {
			return [];
		}

		$content = file_get_contents($file);

		if (false === $content) {
			return [];
		}

		/** @var list<int> $data */
		$data = json_decode($content, true);

		return is_array($data) ? $data : [];
	}

	/**
	 * Persist the list of request timestamps for the given key.
	 *
	 * @param list<int> $timestamps Unix timestamps to store
	 */
	public function save(string $key, array $timestamps): void
	{
		file_put_contents($this->filePath($key), json_encode($timestamps), LOCK_EX);
	}

	/**
	 * Remove expired timestamps from every stored file.
	 *
	 * To avoid running expensive disk scans on every request, this method
	 * uses a lock file and only executes once per CLEANUP_INTERVAL.
	 *
	 * @param int $windowSeconds The sliding window size in seconds.
	 *                           Timestamps older than (now - windowSeconds) are discarded.
	 */
	public function cleanup(int $windowSeconds): void
	{
		$dir      = $this->storageDir;
		$lockFile = $dir . '/.cleanup.lock';
		$now      = time();

		if (file_exists($lockFile)) {
			$lastCleanup = (int) file_get_contents($lockFile);

			if ($now - $lastCleanup < self::CLEANUP_INTERVAL) {
				return;
			}
		}

		file_put_contents($lockFile, (string) $now, LOCK_EX);

		$files = glob($dir . '/*.json');

		if (false === $files) {
			return;
		}

		foreach ($files as $file) {
			$content = file_get_contents($file);

			if (false === $content) {
				continue;
			}

			/** @var list<int> $data */
			$data = json_decode($content, true);

			if (!is_array($data)) {
				unlink($file);

				continue;
			}

			$data = array_filter(
				$data,
				static fn (int $timestamp): bool => $now - $windowSeconds < $timestamp,
			);

			if ([] === $data) {
				unlink($file);
			}
		}
	}

	/**
	 * Build the full filesystem path for a given key.
	 */
	private function filePath(string $key): string
	{
		return $this->storageDir . DIRECTORY_SEPARATOR . $key . '.json';
	}
}
