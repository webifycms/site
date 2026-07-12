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

/**
 * Computes previous and next post navigation for a single post view, and
 * page-based pagination for the posts listing.
 *
 * @phpstan-import-type PostMeta from PostReader
 *
 * @phpstan-type ListPagination array{
 *     items: PostMeta[],
 *     currentPage: int,
 *     totalPages: int,
 *     total: int,
 *     perPage: int,
 *     prev: null|int,
 *     next: null|int,
 *     pages: int[],
 * }
 */
final readonly class PostPagination
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private PostReader $reader,
	) {}

	/**
	 * Returns the previous and next posts relative to a slug.
	 *
	 * @return array{prev: null|PostMeta, next: null|PostMeta}
	 */
	public function forSlug(string $slug): array
	{
		return $this->reader->findAdjacent($slug);
	}

	/**
	 * Returns a slice of posts for the given page along with pagination metadata.
	 *
	 * @return ListPagination
	 */
	public function forList(int $page = 1, int $perPage = 10): array
	{
		$all         = $this->reader->findAll();
		$total       = count($all);
		$totalPages  = max(1, (int) ceil($total / $perPage));
		$page        = max(1, min($page, $totalPages));
		$offset      = ($page - 1) * $perPage;
		$items       = array_slice($all, $offset, $perPage);
		$prev        = 1 < $page ? $page - 1 : null;
		$next        = $page < $totalPages ? $page + 1 : null;
		$pages       = $this->buildPageRange($page, $totalPages);

		return [
			'items'       => $items,
			'currentPage' => $page,
			'totalPages'  => $totalPages,
			'total'       => $total,
			'perPage'     => $perPage,
			'prev'        => $prev,
			'next'        => $next,
			'pages'       => $pages,
		];
	}

	/**
	 * Builds a compact range of page numbers to display.
	 *
	 * Shows the first, last, and up to 5 pages around the current page,
	 * with gaps represented as 0 so the template can insert ellipsis.
	 *
	 * @return int[]
	 */
	private function buildPageRange(int $current, int $total): array
	{
		if (7 >= $total) {
			return range(1, $total);
		}

		$pages   = [];
		$pages   = [1, 0]; // gap
		$pages   = array_merge($pages, range($current - 1, $current + 1));
		$pages[] = 0; // gap
		$pages[] = $total;

		if (4 >= $current) {
			$pages   = range(1, 5);
			$pages[] = 0; // gap
			$pages[] = $total;
		}

		if ($total - 3 <= $current) {
			$pages = [1, 0]; // gap
			$pages = array_merge($pages, range($total - 4, $total));
		}

		return $pages;
	}
}
