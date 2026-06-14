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

namespace App\Test\Architecture;

use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

/**
 * Architecture tests that enforce DDD and clean architecture principles.
 *
 * Domain is the innermost layer — it must not know about Application or Infrastructure.
 * Application sits between Domain and Infrastructure.
 * Contract is a shared kernel that can be referenced by all layers.
 * Infrastructure implements Domain interfaces (Dependency Inversion).
 *
 * @internal
 */
final class ArchitectureSpec
{
	/**
	 * Domain classes must not depend on Application or Infrastructure.
	 * Dependency on Contract (shared kernel) is permitted.
	 */
	public function testDomainDoesNotDependOnApplicationOrInfrastructure(): Rule
	{
		return PHPat::rule()
			->classes(Selector::inNamespace('App\Domain'))
			->shouldNot()
			->dependOn()
			->classes(
				Selector::inNamespace('App\Application'),
				Selector::inNamespace('App\Infrastructure'),
			)
			->because('Domain is the innermost layer and must not depend on outer layers')
		;
	}

	/**
	 * Application classes must not depend on Infrastructure classes.
	 */
	public function testApplicationDoesNotDependOnInfrastructure(): Rule
	{
		return PHPat::rule()
			->classes(Selector::inNamespace('App\Application'))
			->shouldNot()
			->dependOn()
			->classes(Selector::inNamespace('App\Infrastructure'))
			->because('Application should not depend on Infrastructure details')
		;
	}
}
