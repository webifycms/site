import '../img/logo.png';
import '../img/daisycon.svg';
import '../img/showcase_webifycms.jpg';
import '../css/app.css';
import hljs from 'highlight.js/lib/core';
import php from 'highlight.js/lib/languages/php';
import bash from 'highlight.js/lib/languages/bash';
import json from 'highlight.js/lib/languages/json';
import yaml from 'highlight.js/lib/languages/yaml';
import xml from 'highlight.js/lib/languages/xml';
import css from 'highlight.js/lib/languages/css';
import sql from 'highlight.js/lib/languages/sql';
import javascript from 'highlight.js/lib/languages/javascript';
import typescript from 'highlight.js/lib/languages/typescript';

hljs.registerLanguage('php', php);
hljs.registerLanguage('bash', bash);
hljs.registerLanguage('json', json);
hljs.registerLanguage('yaml', yaml);
hljs.registerLanguage('xml', xml);
hljs.registerLanguage('css', css);
hljs.registerLanguage('sql', sql);
hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('typescript', typescript);

(function () {
    'use strict';

    hljs.highlightAll();

    const nav = document.getElementById('nav');
    let ticking = false;

    document.addEventListener('scroll', function () {
        if (!ticking) {
            requestAnimationFrame(function () {
                nav.classList.toggle('nav--compact', window.scrollY > 30);
                ticking = false;
            });
            ticking = true;
        }
    });

    const toggle = document.getElementById('navToggle');
    const links = document.getElementById('navLinks');

    if (toggle && links) {
        toggle.addEventListener('click', function () {
            links.classList.toggle('nav__links--open');
        });

        links.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                links.classList.remove('nav__links--open');
            });
        });
    }

    const themeBtn = document.getElementById('themeToggle');
    const htmlEl = document.documentElement;

    if (themeBtn) {
        themeBtn.addEventListener('click', function () {
            const next = htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            htmlEl.setAttribute('data-theme', next);
            localStorage.setItem('wf-theme', next);
        });
    }

    const obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) {
                e.target.classList.add('reveal--vis');
                obs.unobserve(e.target);
            }
        });
    }, {threshold: 0.06, rootMargin: '0px 0px -40px 0px'});

    document.querySelectorAll('.reveal').forEach(function (el) {
        obs.observe(el);
    });

    var form = document.getElementById('signupForm');
    if (form) {
        var nIn = document.getElementById('sName');
        var eIn = document.getElementById('sEmail');
        var cIn = document.getElementById('sConsent');
        var nErr = document.getElementById('nameErr');
        var eErr = document.getElementById('emailErr');
        var cErr = document.getElementById('consentErr');
        var succ = document.getElementById('signupSuccess');

        function err(inp, el, show) {
            inp.classList.toggle('--error', show);
            el.classList.toggle('--show', show);
        }

        function clearFieldErrors() {
            err(nIn, nErr, false);
            err(eIn, eErr, false);
            err(cIn, cErr, false);
            nErr.textContent = '';
            eErr.textContent = '';
            cErr.textContent = '';
        }

        function setSubmitting(submitting, btn, originalText) {
            btn.textContent = submitting ? 'Subscribing...' : originalText;
            btn.disabled = submitting;
        }

        function showSuccess() {
            form.style.display = 'none';
            succ.style.display = 'block';
        }

        function showFieldErrors(errors) {
            clearFieldErrors();

            if (errors.name) {
                nIn.classList.add('--error');
                nErr.textContent = errors.name[0];
                nErr.classList.add('--show');
            }
            if (errors.email) {
                eIn.classList.add('--error');
                eErr.textContent = errors.email[0];
                eErr.classList.add('--show');
            }
            if (errors.consent) {
                cIn.classList.add('--error');
                cErr.textContent = errors.consent[0];
                cErr.classList.add('--show');
            }
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearFieldErrors();

            var nv = nIn.value.trim();
            var ev = eIn.value.trim();
            var ok = true;

            if (!nv) {
                err(nIn, nErr, true);
                ok = false;
            }
            if (!ev || !ev.includes('@') || !ev.includes('.')) {
                err(eIn, eErr, true);
                ok = false;
            }
            if (!cIn.checked) {
                err(cIn, cErr, true);
                ok = false;
            }
            if (!ok) return;

            var btn = form.querySelector('button[type="submit"]');
            var originalText = btn.textContent;

            setSubmitting(true, btn, originalText);

            fetch('/api/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ name: nv, email: ev })
            })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function (result) {
                var data = result.data;

                if (result.ok && data.success) {
                    showSuccess();
                    return;
                }

                if (409 === result.status) {
                    showSuccess();
                    return;
                }

                if (422 === result.status && data.errors) {
                    showFieldErrors(data.errors);
                    setSubmitting(false, btn, originalText);
                    return;
                }

                setSubmitting(false, btn, originalText);
                alert(data.message || 'Something went wrong. Please try again later.');
            })
            .catch(function () {
                setSubmitting(false, btn, originalText);
                alert('Something went wrong. Please try again later.');
            });
        });

        nIn.addEventListener('input', function () {
            err(nIn, nErr, false);
        });
        eIn.addEventListener('input', function () {
            err(eIn, eErr, false);
        });
        cIn.addEventListener('change', function () {
            err(cIn, cErr, false);
        });
    }

    document.querySelectorAll('.share__btn--copy').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = btn.getAttribute('data-copy-url');

            if (!url) return;

            navigator.clipboard.writeText(url).then(function () {
                btn.classList.add('is-copied');

                setTimeout(function () {
                    btn.classList.remove('is-copied');
                }, 2000);
            });
        });
    });
})();
