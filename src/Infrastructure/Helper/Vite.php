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

namespace App\Infrastructure\Helper;

use App\Infrastructure\Service\Url;
use Webify\Base\Application\Service\ConfigInterface;

use function file_exists;

/**
 * Vite asset helper for development and production environments.
 *
 * Follows the official Vite backend integration guide:
 *
 * @see https://vite.dev/guide/backend-integration
 *
 * In dev mode, assets are served from the Vite dev server
 * and the @vite/client script is injected for HMR.
 * In production, the manifest.json is read to resolve hashed filenames
 * with proper CSS/JS tag ordering.
 */
final readonly class Vite
{
	/**
	 * The path to the manifest file in production.
	 */
	private const string MANIFEST_PATH = '/public/.vite/manifest.json';

	/**
	 * The path to the hot file used by the Vite dev server.
	 */
	private const string HOT_FILE = '/public/hot';

	/**
	 * The constructor.
	 */
	public function __construct(
		private ConfigInterface $config,
		private Url $urlService
	) {}

	/**
	 * Returns the resolved URL for the given asset path.
	 *
	 * Use this for individual assets like images, fonts, etc.
	 * For entry points (CSS/JS files), use entryPointHtml() instead.
	 */
	public function resolveUrl(string $path): string
	{
		$path     = str_starts_with($path, 'assets/') ? $path : 'assets/' . $path;
		$manifest = $this->loadManifest();

		if (null !== $manifest && isset($manifest[$path]['file'])) {
			return $this->urlService->resolveUrl($manifest[$path]['file']);
		}

		if ($this->isDevServerRunning()) {
			return $this->devServerUrl() . '/' . $path;
		}

		return $this->urlService->resolveUrl('/' . $path);
	}

	/**
	 * Generates full HTML tags for a Vite entry point, following the
	 * official backend integration guide tag ordering:
	 *
	 *   1. @vite/client (dev only, for HMR)
	 *   2. <link rel="stylesheet"> for the entry's own CSS
	 *   3. <link rel="stylesheet"> for imported chunks' CSS
	 *   4. <link rel="stylesheet"> for entry if it's a CSS file
	 *   5. <script type="module"> for the entry
	 *   6. <link rel="modulepreload"> for imported JS chunks (optional)
	 *
	 * @param string $entry The entry source path (e.g. "assets/js/app.js")
	 *
	 * @return string The complete HTML tags string
	 */
	public function entryPointHtml(string $entry): string
	{
		$entry = str_starts_with($entry, 'assets/') ? $entry : 'assets/' . $entry;

		if ($this->isDevServerRunning()) {
			return $this->devEntryHtml($entry);
		}

		$manifest = $this->loadManifest();

		if (null !== $manifest && isset($manifest[$entry])) {
			return $this->prodEntryHtml($manifest, $entry);
		}

		return '';
	}

	/**
	 * Builds the HTML tags for the given entry when the Vite dev server is running.
	 */
	private function devEntryHtml(string $entry): string
	{
		$base   = $this->devServerUrl();
		$isCss  = str_ends_with($entry, '.css');
		$html   = '';

		// 1. @vite/client for HMR (only for JS entries, Vite handles CSS via JS)
		if (!$isCss) {
			$html .= '<script type="module" src="' . $base . '/@vite/client"></script>' . "\n";
		}

		// 2. The entry itself
		if ($isCss) {
			$html .= '<link rel="stylesheet" href="' . $base . '/' . $entry . '">';
		} else {
			$html .= '<script type="module" src="' . $base . '/' . $entry . '"></script>';
		}

		return $html;
	}

	/**
	 * Builds the HTML tags from the manifest, following the recommended tag ordering:
	 *   1. Entry CSS
	 *   2. Imported chunks CSS
	 *   3. Entry JS (or CSS if it's a CSS entry)
	 *   4. Module preload for imported JS chunks
	 *
	 * @param array<string, array<string, mixed>> $manifest
	 */
	private function prodEntryHtml(array $manifest, string $entry): string
	{
		$chunk = $manifest[$entry];
		$html  = '';

		// 1. Entry's own CSS files
		foreach ($chunk['css'] ?? [] as $css) {
			$html .= '<link rel="stylesheet" href="' . $this->resolveManifestPath($css) . '">' . "\n";
		}

		// 2. Recursively collect CSS from imported chunks
		$seen = [$entry => true];
		$html .= $this->importedCssHtml($manifest, $chunk['imports'] ?? [], $seen);

		// 3. Entry itself
		if (str_ends_with($entry, '.css')) {
			$html .= '<link rel="stylesheet" href="' . $this->resolveManifestPath($chunk['file']) . '">';
		} else {
			$html .= '<script type="module" src="' . $this->resolveManifestPath($chunk['file']) . '"></script>';
		}

		// 4. Module preload for imported JS chunks
		$seen = [$entry => true];
		$html .= $this->importedPreloadHtml($manifest, $chunk['imports'] ?? [], $seen);

		return $html;
	}

	/**
	 * Recursively generates <link rel="stylesheet"> tags for CSS files
	 * imported by the given list of chunk keys.
	 *
	 * @param array<string, array<string, mixed>> $manifest
	 * @param array<string>                       $imports
	 * @param array<string, bool>                 $seen
	 */
	private function importedCssHtml(array $manifest, array $imports, array &$seen): string
	{
		$html = '';

		foreach ($imports as $key) {
			if (isset($seen[$key]) || !isset($manifest[$key])) {
				continue;
			}

			$seen[$key] = true;
			$chunk      = $manifest[$key];
			$html .= $this->importedCssHtml($manifest, $chunk['imports'] ?? [], $seen);

			foreach ($chunk['css'] ?? [] as $css) {
				$html .= '<link rel="stylesheet" href="' . $this->resolveManifestPath($css) . '">' . "\n";
			}
		}

		return $html;
	}

	/**
	 * Recursively generates <link rel="modulepreload"> tags for JS imports.
	 *
	 * @param array<string, array<string, mixed>> $manifest
	 * @param array<string>                       $imports
	 * @param array<string, bool>                 $seen
	 */
	private function importedPreloadHtml(array $manifest, array $imports, array &$seen): string
	{
		$html = '';

		foreach ($imports as $key) {
			if (isset($seen[$key]) || !isset($manifest[$key])) {
				continue;
			}

			$seen[$key] = true;
			$chunk      = $manifest[$key];
			$html .= $this->importedPreloadHtml($manifest, $chunk['imports'] ?? [], $seen);

			$html .= "\n" . '<link rel="modulepreload" href="' . $this->resolveManifestPath($chunk['file']) . '">';
		}

		return $html;
	}

	/**
	 * Loads and returns the manifest array, or null if not available.
	 *
	 * @return null|array<string, array<string, mixed>>
	 */
	private function loadManifest(): ?array
	{
		$manifestPath = $this->config->basePath . self::MANIFEST_PATH;

		if (!file_exists($manifestPath)) {
			return null;
		}

		$manifest = json_decode((string) file_get_contents($manifestPath), true);

		return is_array($manifest) ? $manifest : null;
	}

	/**
	 * Resolves a manifest file path to a full URL.
	 */
	private function resolveManifestPath(string $filePath): string
	{
		return $this->urlService->resolveUrl($filePath);
	}

	/**
	 * Returns the dev server base URL.
	 */
	private function devServerUrl(): string
	{
		return rtrim(
			(string) $this->config->get('vite.devServerUrl', $this->urlService->getBaseUrl()),
			'/'
		);
	}

	/**
	 * Checks if the Vite dev server is currently running by checking for the hot file.
	 */
	private function isDevServerRunning(): bool
	{
		return true === file_exists($this->config->basePath . self::HOT_FILE);
	}
}
