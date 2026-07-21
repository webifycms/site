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

// should require the composer autoloader on first
require __DIR__ . '/vendor/autoload.php';

use PhpCsFixer\Finder;
use Webify\Tools\Fixer\Fixer;

$finder = Finder::create()
	->in(__DIR__)
	->exclude(
		[
			'vendor',
			'runtime',
			'templates',
		]
	)
	->ignoreDotFiles(false)
	->name('*.php')
	->notName('.php-cs-fixer.*')
;
$header = <<<'HEADER'
	The file is part of the "webifycms/site", WebifyCMS site.

	@see https://webifycms.com

	@copyright Copyright (c) 2026 WebifyCMS
	@license https://webifycms.com/license
	@author Mohammed Shifreen <mshifreen@gmail.com>
	HEADER;
$rules = [
	'echo_tag_syntax'         => [
		'format'                         => 'short',
		'shorten_simple_statements_only' => false,
	],
	'phpdoc_to_comment'       => false,
	'global_namespace_import' => [
		'import_classes'   => true,
		'import_constants' => false,
		'import_functions' => true,
	],
	'header_comment'          => [
		'header'       => $header,
		'location'     => 'after_open',
		'comment_type' => 'PHPDoc',
		'separate'     => 'top',
	],
];

return new Fixer($finder, $rules)
	->getConfig()
	->setUsingCache(false)
;
