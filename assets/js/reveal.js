// ================================================================
// SCROLL REVEAL ANIMATIONS
// ================================================================
// Elements with class "reveal" fade/slide in when they enter the
// viewport. Uses IntersectionObserver for performance — no scroll
// event listeners needed.
//
// How it works:
//   - Each .reveal element is observed with a 6% visibility
//     threshold and a -40px bottom offset (triggers slightly
//     before fully in view).
//   - When intersecting, the "reveal--vis" class is added and
//     the element is unobserved (animation fires only once).
//
// CSS classes:
//   .reveal         — initial hidden state (opacity: 0, translated)
//   .reveal--vis    — visible state (opacity: 1, translate reset)
//
// To use on a new element, just add class="reveal" in the HTML.
// ================================================================

export function init() {
	const obs = new IntersectionObserver(
		function (entries) {
			entries.forEach(function (e) {
				if (e.isIntersecting) {
					e.target.classList.add("reveal--vis");
					obs.unobserve(e.target);
				}
			});
		},
		{ threshold: 0.06, rootMargin: "0px 0px -40px 0px" },
	);

	document.querySelectorAll(".reveal").forEach(function (el) {
		obs.observe(el);
	});
}
