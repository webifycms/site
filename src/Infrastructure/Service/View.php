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

use App\Infrastructure\Exception\TemplateNotFoundException;
use App\Infrastructure\Helper\Vite;
use Webify\Base\Application\Service\ConfigInterface;

/**
 * View service manages templates, page-level metadata, template parameters, and asset URLs.
 *
 * Controllers set properties in __invoke, then the template accesses them
 * through the $view variable. Properties are public, so partials can also
 * read or update them at render time.
 */
final class View
{
	/**
	 * @var string The page title
	 */
	public string $title = '';

	/**
	 * @var string The page description
	 */
	public string $description = '';

	/**
	 * @var string The canonical URL (set by controllers)
	 */
	public string $canonical = '';

	/**
	 * @var string The current path used to highlight the active nav link (e.g. "/extensions")
	 */
	public string $currentPath = '/';

	/**
	 * Template parameters passed to the template.
	 *
	 * @var array<string, mixed>
	 */
	public array $params = [];

	/**
	 * Template-specific variables accessible in the template.
	 *
	 * @var array<string, mixed>
	 */
	public array $data = [];

	/**
	 * Meta tags to be registered in the HTML head.
	 *
	 * @var array<string, array{string, string}>
	 */
	private array $metaTags = [];

	/**
	 * @var Vite the Vite helper
	 */
	private readonly Vite $vite;

	/**
	 * The constructor.
	 */
	public function __construct(
		private readonly ConfigInterface $config,
		private readonly Url $urlService,
	) {
		$this->vite = new Vite($this->config, $this->urlService);
	}

	/**
	 * Returns the title by concatenating the page title and site name.
	 */
	public function getTitle(): string
	{
		return sprintf(
			'' === $this->title ? '%s' : '%s | %s',
			htmlspecialchars($this->title, ENT_QUOTES),
			htmlspecialchars($this->config->get('name', 'WebifyCMS'), ENT_QUOTES)
		);
	}

	/**
	 * Adds a meta tag.
	 */
	public function addMeta(string $name, string $content, string $type = 'name'): self
	{
		$this->metaTags[$name] = [$type, htmlspecialchars($content, ENT_QUOTES)];

		return $this;
	}

	/**
	 * Returns the HTML for all registered meta tags.
	 */
	public function registerMetaTags(): string
	{
		$tags = '';

		foreach ($this->metaTags as $name => [$type, $content]) {
			$tags .= '<meta ' . $type . '="' . htmlspecialchars($name, ENT_QUOTES)
				. '" content="' . $content . '" />' . "\n\t\t";
		}

		return $tags;
	}

	/**
	 * Returns the resolved URL for the given asset path.
	 *
	 * Use this for individual assets like images, fonts, etc.
	 * For entry points (CSS/JS files), use assetEntryHtml() instead.
	 *
	 * In development the URL points to the Vite dev server; in production
	 * the manifest.json is used to resolve hashed filenames.
	 */
	public function assetUrl(string $path): string
	{
		return $this->vite->resolveUrl($path);
	}

	/**
	 * Generates complete HTML tags for a Vite entry point (CSS or JS).
	 *
	 * Follows the official Vite backend integration guide:
	 *   - In development: injects @vite/client + the entry script/link
	 *   - In production: resolves manifest entries with proper CSS/JS ordering
	 *
	 * @see https://vite.dev/guide/backend-integration
	 *
	 * @param string $entry The entry source path (e.g. "assets/js/app.js")
	 *
	 * @return string The complete HTML tags string
	 */
	public function assetEntryHtml(string $entry): string
	{
		return $this->vite->entryPointHtml($entry);
	}

	/**
	 * Returns the site URL for the given path.
	 *
	 * Use this for internal links like navigation, routes, etc.
	 */
	public function url(string $path = ''): string
	{
		if ('' === $path) {
			return $this->urlService->getBaseUrl();
		}

		return $this->urlService->resolveUrl($path);
	}

	/**
	 * Renders a PHP template with the given variables and returns the output.
	 *
	 * @param string               $template the template name (without .php)
	 * @param array<string, mixed> $data     template-specific variables merged into $this->data
	 *
	 * @return string the rendered template output
	 */
	public function render(string $template, array $data = []): string
	{
		$this->data = array_merge($this->data, $data);

		ob_start();

		$view = $this;

		require $this->resolveTemplate($template);

		return (string) ob_get_clean();
	}

	/**
	 * Renders a partial template. Output is written directly to the active buffer.
	 *
	 * @param string $partial the partial name (without .php)
	 */
	public function renderPartial(string $partial): void
	{
		$view = $this;

		require $this->resolvePartial($partial);
	}

	/**
	 * Resolves the full path of a template file based on the given template name.
	 *
	 * @throws TemplateNotFoundException if the template file does not exist
	 */
	private function resolveTemplate(string $template): string
	{
		$path = $this->config->basePath . '/templates/' . $template . '.php';

		if (!file_exists($path)) {
			throw TemplateNotFoundException::forTemplate($path);
		}

		return $path;
	}

	/**
	 * Resolves the full path of a partial file based on the given partial name.
	 *
	 * @throws TemplateNotFoundException if the partial file does not exist
	 */
	private function resolvePartial(string $partial): string
	{
		$path = $this->config->basePath . '/templates/partials/' . $partial . '.php';

		if (!file_exists($path)) {
			throw TemplateNotFoundException::forTemplate($path);
		}

		return $path;
	}
}
