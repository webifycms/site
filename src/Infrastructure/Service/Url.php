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

namespace App\Infrastructure\Service;

use UnexpectedValueException;
use Webify\Base\Application\Service\ConfigInterface;

/**
 * Url service that handles URL-related operations.
 */
final class Url
{
	/**
	 * @var string the base URL of the application
	 */
	private string $baseUrl;

	/**
	 * @var string the URL scheme (http:// or https://)
	 */
	private string $scheme;

	/**
	 * @var array<string> the list of allowed hosts for the application
	 */
	private array $allowedHosts = [
		'webifycms.com',
		'webifycms.com.local',
		'localhost',
		'127.0.0.1',
	];

	/**
	 * The constructor.
	 */
	public function __construct(
		private readonly ConfigInterface $config
	) {
		$configUrl = ltrim($this->config->baseUrl, '/');

		$this->scheme = $this->determineScheme($configUrl);

		if ('' === $configUrl) {
			$configUrl = $this->toTheFallback();
		}

		$this->baseUrl = $this->scheme . $this->stripScheme($configUrl);
	}

	/**
	 * Returns the base URL of the application.
	 */
	public function getBaseUrl(): string
	{
		return $this->baseUrl;
	}

	/**
	 * Resolves the full URL for the given path.
	 */
	public function resolveUrl(string $path): string
	{
		if (!str_starts_with($path, '/')) {
			return '/' . $path;
		}

		return $this->baseUrl . $path;
	}

	/**
	 * Determines the base URL of the application if it's not set in the configuration safely.
	 */
	private function toTheFallback(): string
	{
		// Get the host name safely
		$host = $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';

		if (!is_string($host)) {
			return '/';
		}

		// Strict validation: Prevent Host Header Injection
		// Filter out characters that shouldn't be in a domain name
		$sanitizedHost = preg_replace('/[^a-zA-Z0-9.:-]/', '', $host);

		if (null === $sanitizedHost) {
			throw new UnexpectedValueException('Failed to sanitize the request host.');
		}

		// Additional validation against a whitelist of expected local/staging domains
		// Extract domain without port for whitelist checking if needed
		$domainOnly = parse_url($this->scheme . $sanitizedHost, PHP_URL_HOST);

		// If the host looks fishy or isn't allowed, default to a safe fallback
		if ('' === $sanitizedHost || !is_string($domainOnly) || !in_array($domainOnly, $this->allowedHosts, true)) {
			return '/';
		}

		// Determine the subfolder path if the app isn't running in the root directory
		$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

		if (!is_string($scriptName)) {
			return '/';
		}

		$currentDir = dirname($scriptName);
		$basePath   = (DIRECTORY_SEPARATOR === $currentDir || '.' === $currentDir) ? '' : $currentDir;
		// Fix Windows backslashes if running locally
		$basePath = str_replace('\\', '/', $basePath);

		return rtrim($this->scheme . $sanitizedHost . $basePath, '/');
	}

	/**
	 * Determine the URL scheme safely.
	 *
	 * Checks the config baseUrl first, then falls back to $_SERVER.
	 */
	private function determineScheme(string $configUrl): string
	{
		if (str_starts_with($configUrl, 'https://')) {
			return 'https://';
		}

		if (str_starts_with($configUrl, 'http://')) {
			return 'http://';
		}

		if (
			isset($_SERVER['HTTPS'])
			&& ('on' === $_SERVER['HTTPS'] || $this->config->get('portSsl') === $_SERVER['SERVER_PORT'])
		) {
			return 'https://';
		}

		return 'http://';
	}

	/**
	 * Strips the scheme prefix from a URL if present.
	 */
	private function stripScheme(string $url): string
	{
		return (string) preg_replace('/^https?:\/\//', '', $url);
	}
}
