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

use App\Infrastructure\Service\View;

/**
 * @var View $view
 */
?>
    <footer class="footer">
      <div class="container">
        <div class="footer__notify" id="notify">
          <div class="footer__notify-inner">
            <div>
              <p class="signup__label">// Stay in the loop</p>
              <h2 class="signup__title">Be the first to know when it <em>launches</em></h2>
              <p class="signup__desc">
                Low-traffic updates only &mdash; launch announcement, major milestones, and the occasional deep-dive into the
                architecture. No noise.
              </p>
            </div>
            <div>
              <div class="signup__card">
                <h3 class="signup__card-title">Get launch updates</h3>
                <p class="signup__card-sub">Join developers following the build. Unsubscribe any time.</p>

                <form id="signupForm" novalidate>
                  <div class="fg">
                    <label for="sName">Your name</label>
                    <input type="text" id="sName" name="name" placeholder="Mohammed" autocomplete="given-name" />
                    <div class="fg__error" id="nameErr">Please enter your name</div>
                  </div>
                  <div class="fg">
                    <label for="sEmail">Email address</label>
                    <input type="email" id="sEmail" name="email" placeholder="you@example.com" required autocomplete="email" />
                    <div class="fg__error" id="emailErr">Please enter a valid email address</div>
                  </div>
                  <div class="fg fg--checkbox">
                    <label>
                      <input type="checkbox" id="sConsent" name="consent" required />
                      I agree to receive launch updates. You can unsubscribe at any time.
                    </label>
                    <div class="fg__error" id="consentErr">Please accept to continue</div>
                  </div>
                  <button type="submit" class="btn btn--accent">Notify me at launch &rarr;</button>
                  <p class="signup__privacy">No spam, ever. Unsubscribe with one click.</p>
                </form>

                <div class="signup__success" id="signupSuccess">
                  <div class="signup__success-icon">&#10003;</div>
                  <h3>You're on the list</h3>
                  <p>We'll be in touch when WebifyCMS is ready. Thanks for your support.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="footer__grid">
          <div>
            <a href="<?= $view->url('/'); ?>" class="logo logo--footer">
                <img src="<?= $view->assetUrl('img/logo-full.png'); ?>" alt="WebifyCMS" width="180" height="67.53" />
            </a>
             <p class="footer__about">
              An open-source PHP application framework built on Clean Architecture and Domain-Driven Design.
            </p>
          </div>
          <div>
            <div class="footer__heading">Project</div>
            <ul class="footer__list">
              <li><a href="<?= $view->url('/#features'); ?>">Features</a></li>
              <li><a href="<?= $view->url('/#roadmap'); ?>">Roadmap</a></li>
              <li><a href="<?= $view->url('/extensions'); ?>">Extensions</a></li>
              <li><a href="<?= $view->url('/publishing'); ?>">Publishing</a></li>
              <li><a href="<?= $view->url('/docs'); ?>">Docs</a></li>
            </ul>
          </div>
          <div>
            <div class="footer__heading">Community</div>
            <ul class="footer__list">
              <li><a href="https://github.com/webifycms/app" target="_blank" rel="noopener">GitHub</a></li>
              <li><a href="https://github.com/webifycms/app/issues" target="_blank" rel="noopener">Issues</a></li>
              <li><a href="<?= $view->url('/contributing'); ?>">Contribute</a></li>
            </ul>
          </div>
          <div>
            <div class="footer__heading">Legal</div>
            <ul class="footer__list">
              <li><a href="<?= $view->url('/license'); ?>">MIT License</a></li>
              <li><a href="<?= $view->url('/privacy'); ?>">Privacy Policy</a></li>
              <li><a href="https://github.com/Shifrin" target="_blank" rel="noopener">Author</a></li>
            </ul>
          </div>
        </div>
        <div class="footer__bottom">&copy; 2026 WebifyCMS. Open source, MIT licensed. Built with <a href="https://github.com/webifycms/app" target="_blank" rel="noopener">WebifyCMS</a>.</div>
      </div>
    </footer>
    <?= $view->renderAnalyticsScript(); ?>
  </body>
</html>
