// ================================================================
// NAVIGATION
// ================================================================
// Two behaviours:
//   1. Compact nav — toggles nav--compact class when scrolled
//      past 30px, used to shrink the nav bar on scroll.
//   2. Mobile menu — toggles nav__links--open on hamburger click,
//      auto-closes when any link is tapped.
//
// Related CSS classes:
//   .nav--compact        — compact state (smaller padding/logo)
//   .nav__links--open    — mobile menu visible
//
// The scroll handler uses requestAnimationFrame throttling
// to avoid layout thrashing on rapid scroll events.
// ================================================================

export function init() {
	const nav = document.getElementById("nav");
	let ticking = false;

	document.addEventListener("scroll", function () {
		if (!ticking) {
			requestAnimationFrame(function () {
				nav.classList.toggle("nav--compact", window.scrollY > 30);
				ticking = false;
			});
			ticking = true;
		}
	});

	const toggle = document.getElementById("navToggle");
	const links = document.getElementById("navLinks");

	if (toggle && links) {
		toggle.addEventListener("click", function () {
			links.classList.toggle("nav__links--open");
		});

		// Close mobile menu when any nav link is tapped
		links.querySelectorAll("a").forEach(function (a) {
			a.addEventListener("click", function () {
				links.classList.remove("nav__links--open");
			});
		});
	}
}
