<?php

declare(strict_types=1);

use App\Infrastructure\Service\View;

/**
 * @var View $view
 */
?>
<?php $view->renderPartial('header'); ?>

<section class="hero--publication">
	<div class="hero__orb hero__orb--1" aria-hidden="true"></div>
	<div class="hero__orb hero__orb--2" aria-hidden="true"></div>

	<div class="container hero__inner">
		<div class="hero__badge">Legal</div>
		<h1 class="title">MIT <em>License</em></h1>
		<p class="hero__subtitle">
			WebifyCMS is open source software released under the MIT License.
		</p>
	</div>
</section>

<section class="section section--raised">
	<div class="container">
		<article class="docs__content" style="margin: 0 auto;">
			<h1>WebifyCMS License</h1>

			<p>Copyright &copy; 2026 WebifyCMS (<a href="https://www.webifycms.com" target="_blank" rel="noopener">https://www.webifycms.com</a>)</p>

			<p>Permission is hereby granted, free of charge, to any person obtaining a copy
			of this software and associated documentation files (the "Software"), to deal
			in the Software without restriction, including without limitation the rights
			to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
			copies of the Software, and to permit persons to whom the Software is
			furnished to do so, subject to the following conditions:</p>

			<p>The above copyright notice and this permission notice shall be included in all
			copies or substantial portions of the Software.</p>

			<p><strong>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
			IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
			FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
			AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
			LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
			OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
			SOFTWARE.</strong></p>
		</article>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>
